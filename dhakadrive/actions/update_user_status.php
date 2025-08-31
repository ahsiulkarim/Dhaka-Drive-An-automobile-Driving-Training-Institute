<?php
// --- 1. INITIAL SETUP ---

// Include the core configuration file for database connection, session management,
// and all helper functions.
require_once '../config.php';


// --- 2. SCRIPT EXECUTION GUARD & AUTHENTICATION ---

// This script is designed to be accessed via a link (GET method).
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405); // Method Not Allowed
    die("Error: This page can only be accessed via a link.");
}

// CRITICAL: Ensure the user is logged in AND is an administrator.
// This is the primary security check.
if (!isAdmin()) {
    set_message("Access Denied. You do not have permission to perform this action.", "error");
    redirect('../login.php');
}


// --- 3. DATA RETRIEVAL AND VALIDATION ---

// Get the user ID and the new status from the URL's query parameters (e.g., ?id=123&status=inactive).
$user_id_to_update = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$new_status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Validate the inputs. The user ID must be a valid number.
if ($user_id_to_update <= 0) {
    set_message("Invalid request. User ID is missing or invalid.", "error");
    redirect('../admin.php?tab=users');
}

// Validate that the new status is one of the two allowed values ('active' or 'inactive').
// This prevents arbitrary values from being inserted into the database.
$allowed_statuses = ['active', 'inactive'];
if (!in_array($new_status, $allowed_statuses)) {
    set_message("Invalid status specified. Action aborted.", "error");
    redirect('../admin.php?tab=users');
}


// --- 4. BUSINESS LOGIC & SECURITY CHECKS ---

// CRITICAL SECURITY CHECK #2: An admin should never be able to deactivate their own account
// or another admin's account through this simple script.
if ($user_id_to_update == getUserId()) {
    set_message("Error: You cannot change your own account status.", "error");
    redirect('../admin.php?tab=users');
}

// Fetch the role of the user we are about to modify to ensure they are not an admin.
$stmt_verify = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt_verify->bind_param("i", $user_id_to_update);
$stmt_verify->execute();
$result = $stmt_verify->get_result();

if ($result->num_rows !== 1) {
    set_message("Error: The target user does not exist.", "error");
    redirect('../admin.php?tab=users');
}

$target_user = $result->fetch_assoc();
if ($target_user['role'] === 'admin') {
    set_message("Error: Administrator accounts cannot be modified through this action.", "error");
    redirect('../admin.php?tab=users');
}
$stmt_verify->close();


// --- 5. DATABASE UPDATE AND NOTIFICATION ---

$conn->begin_transaction();

try {
    // --- Step 5a: Update the user's account_status in the database ---
    $sql_update = "UPDATE users SET account_status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $new_status, $user_id_to_update);
    $stmt_update->execute();
    $stmt_update->close();

    // --- Step 5b: Create a notification for the user ---
    // The message is dynamically created based on the action (activating or deactivating).
    $notification_message = ($new_status === 'active')
        ? "Your account has been reactivated by an administrator. You can now log in."
        : "Your account has been deactivated by an administrator. Please contact support if you believe this is an error.";
        
    $sql_notify = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $stmt_notify = $conn->prepare($sql_notify);
    $stmt_notify->bind_param("is", $user_id_to_update, $notification_message);
    $stmt_notify->execute();
    $stmt_notify->close();

    // --- Step 5c: Commit the transaction ---
    $conn->commit();
    
    // Set a success message for the admin.
    $feedback_message = "User account has been successfully " . ($new_status === 'active' ? 'activated' : 'deactivated') . ".";
    set_message($feedback_message, "success");

} catch (mysqli_sql_exception $exception) {
    // --- Step 5d: Handle Errors and Rollback ---
    $conn->rollback();
    set_message("A database error occurred. The user's status was not changed.", "error");
    // For debugging: error_log("User status update failed: " . $exception->getMessage());
}


// --- 6. CLEANUP AND REDIRECT ---

$conn->close();

// Redirect the admin back to the user management tab where they will see the feedback message.
redirect('../admin.php?tab=users');
?>