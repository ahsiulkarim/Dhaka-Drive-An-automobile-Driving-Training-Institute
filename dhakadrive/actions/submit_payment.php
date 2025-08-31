<?php
// --- 1. INITIAL SETUP ---

// Include the core configuration file for database connection, session management,
// and all helper functions.
require_once '../config.php';


// --- 2. SCRIPT EXECUTION GUARD ---

// This script must only be accessed via a form submission using the POST method.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    die("Error: Invalid access method.");
}


// --- 3. SECURITY & AUTHENTICATION CHECK ---

// A user must be logged in to submit payment information.
// If not, redirect them to the login page.
if (!isLoggedIn()) {
    set_message("You must be logged in to submit payment.", "error");
    redirect('../login.php');
}


// --- 4. DATA RETRIEVAL AND VALIDATION ---

// Get the ID of the currently logged-in user from the session.
$user_id = getUserId();

// Retrieve and validate the data submitted from the payment form.
$enrollment_id = isset($_POST['enrollment_id']) ? (int)$_POST['enrollment_id'] : 0;
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
$trx_id = isset($_POST['trx_id']) ? trim($_POST['trx_id']) : null; // Only for MFS

// Basic validation: Ensure required fields are present.
if ($enrollment_id <= 0 || empty($payment_method)) {
    set_message("Invalid payment submission. Required information is missing.", "error");
    redirect('../dashboard.php');
}

// Validate that the payment method is one of the allowed options.
$allowed_methods = ['MFS', 'Card', 'Cash'];
if (!in_array($payment_method, $allowed_methods)) {
    set_message("Invalid payment method selected.", "error");
    redirect('../dashboard.php');
}

// If the method is MFS, the Transaction ID is required.
if ($payment_method === 'MFS' && empty($trx_id)) {
    set_message("Transaction ID (TrxID) is required for MFS payments.", "error");
    redirect('../dashboard.php');
}


// --- 5. VERIFY OWNERSHIP OF THE ENROLLMENT ---

// This is a crucial security check. We must ensure that the logged-in user
// actually owns the enrollment record they are trying to pay for.
// This prevents a malicious user from paying for someone else's enrollment.
$stmt_verify = $conn->prepare("SELECT id, status FROM enrollments WHERE id = ? AND user_id = ?");
$stmt_verify->bind_param("ii", $enrollment_id, $user_id);
$stmt_verify->execute();
$result_verify = $stmt_verify->get_result();

if ($result_verify->num_rows !== 1) {
    // If no record is found, the user does not own this enrollment, or it doesn't exist.
    set_message("Error: You do not have permission to update this enrollment, or it does not exist.", "error");
    redirect('../dashboard.php');
}

// Check if the enrollment is in the correct state to accept payment.
$enrollment = $result_verify->fetch_assoc();
if ($enrollment['status'] !== 'Awaiting Payment') {
    set_message("This enrollment is not currently awaiting payment. Please check its status.", "info");
    redirect('../dashboard.php');
}
$stmt_verify->close();


// --- 6. DETERMINE NEW STATUS AND PREPARE FOR UPDATE ---

// Based on the payment method, set the new status for the enrollment record.
$new_status = '';
$feedback_message = '';

switch ($payment_method) {
    case 'MFS':
    case 'Card':
        // For digital payments, the status becomes 'Payment Submitted'
        // and requires admin confirmation.
        $new_status = 'Payment Submitted';
        $feedback_message = 'Your payment information has been submitted successfully! You will be notified once it is confirmed by an administrator.';
        break;
    case 'Cash':
        // For cash payments, the status indicates the user will pay in person.
        $new_status = 'Awaiting Cash Payment';
        $feedback_message = 'Your preference to pay in person has been recorded. Your class schedule can be confirmed after payment is received.';
        break;
}

// For Card payments, we simulate the TrxID. For Cash, it's not applicable.
if ($payment_method === 'Card') {
    $trx_id = 'SIMULATED_CARD_PAYMENT';
} elseif ($payment_method === 'Cash') {
    $trx_id = null;
}


// --- 7. DATABASE UPDATE ---

// Prepare the SQL statement to update the enrollment record with the new payment details.
$sql_update = "UPDATE enrollments SET status = ?, payment_method = ?, trx_id = ? WHERE id = ? AND user_id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("sssii", $new_status, $payment_method, $trx_id, $enrollment_id, $user_id);


// --- 8. PROVIDE FEEDBACK AND REDIRECT ---

// Execute the update query.
if ($stmt_update->execute()) {
    // If the update was successful, set the success message.
    set_message($feedback_message, "success");
} else {
    // If there was a database error, set an error message.
    set_message("A database error occurred while submitting your payment. Please try again.", "error");
    // For debugging: error_log("Payment submission failed: " . $stmt_update->error);
}

// Close the statement and the database connection.
$stmt_update->close();
$conn->close();

// Redirect the user back to their dashboard to see the updated status.
redirect('../dashboard.php');

?>