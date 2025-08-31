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

// CRITICAL: Ensure the user is logged in AND is an administrator.
// If a regular user somehow tries to access this script, they will be redirected.
if (!isAdmin()) {
    set_message("Access Denied. You do not have permission to perform this action.", "error");
    redirect('../login.php');
}


// --- 4. DATA RETRIEVAL AND VALIDATION ---

// Get the ID of the user being replied to and the message content from the form.
$user_id_to_reply = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$message_text = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate the inputs. The user ID must be a valid number and the message cannot be empty.
if ($user_id_to_reply <= 0 || empty($message_text)) {
    set_message("Invalid request. User ID or message text is missing.", "error");
    // Redirect back to the main messaging tab if data is invalid.
    redirect('../admin.php?tab=messaging');
}


// --- 5. VERIFY THAT THE TARGET USER EXISTS ---

// Before saving the message, it's good practice to ensure the user we are replying to
// actually exists in the database.
$stmt_verify = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'user'");
$stmt_verify->bind_param("i", $user_id_to_reply);
$stmt_verify->execute();
$result_verify = $stmt_verify->get_result();

if ($result_verify->num_rows !== 1) {
    // If no user is found with that ID, it's an invalid request.
    set_message("Error: The user you are trying to reply to does not exist.", "error");
    redirect('../admin.php?tab=messaging');
}
$stmt_verify->close();


// --- 6. DATABASE INSERTION AND NOTIFICATION ---

// We need to perform two database operations: save the message and create a notification.
// A transaction ensures that both succeed or both fail, maintaining data integrity.
$conn->begin_transaction();

try {
    // --- Step 6a: Insert the admin's message into the 'messages' table ---
    $sql_insert_message = "INSERT INTO messages (user_id, sender_role, message) VALUES (?, 'admin', ?)";
    $stmt_insert = $conn->prepare($sql_insert_message);
    $stmt_insert->bind_param("is", $user_id_to_reply, $message_text);
    $stmt_insert->execute();
    $stmt_insert->close();

    // --- Step 6b: Create a notification for the user ---
    $notification_message = "You have a new message from the admin. Please check your dashboard.";
    $sql_insert_notification = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $stmt_notify = $conn->prepare($sql_insert_notification);
    $stmt_notify->bind_param("is", $user_id_to_reply, $notification_message);
    $stmt_notify->execute();
    $stmt_notify->close();

    // --- Step 6c: Commit the transaction ---
    // If both queries were successful, make the changes permanent.
    $conn->commit();
    
    // Set a success message for the admin.
    set_message("Your reply has been sent successfully.", "success");

} catch (mysqli_sql_exception $exception) {
    // --- Step 6d: Handle Errors and Rollback ---
    // If any query failed, undo all changes from this transaction.
    $conn->rollback();
    
    // Set an error message for the admin.
    set_message("A database error occurred. Your message could not be sent.", "error");
    // For debugging: error_log("Admin reply transaction failed: " . $exception->getMessage());
}


// --- 7. CLEANUP AND REDIRECT ---

// Close the database connection.
$conn->close();

// Redirect the admin back to the messaging tab, showing the conversation they were just in.
// This provides a smooth user experience.
redirect('../admin.php?tab=messaging&user_id=' . $user_id_to_reply);
?>