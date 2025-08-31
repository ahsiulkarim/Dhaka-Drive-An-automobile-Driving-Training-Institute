<?php
// --- 1. INITIAL SETUP ---

// Include the core configuration file. This starts the session, connects to the
// database, and provides access to helper functions.
require_once '../config.php';


// --- 2. SCRIPT EXECUTION GUARD ---

// This script must only be accessed via a form submission using the POST method.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    die("Error: Invalid access method.");
}


// --- 3. SECURITY & AUTHENTICATION CHECK ---

// A user must be logged in to send a message.
if (!isLoggedIn()) {
    set_message("You must be logged in to send a message.", "error");
    redirect('../login.php');
}

// Admins should not use this form to send messages. They have their own reply form.
if (isAdmin()) {
    set_message("Admins cannot use this form.", "error");
    redirect('../admin.php');
}


// --- 4. DATA RETRIEVAL AND VALIDATION ---

// Get the ID of the currently logged-in user from their session.
$user_id = getUserId();

// Retrieve the message content from the submitted form.
// The trim() function removes any accidental whitespace from the start and end.
$message_text = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate the message. It cannot be empty.
if (empty($message_text)) {
    // If the message is empty, set an error and redirect back to the dashboard.
    set_message("Message cannot be empty.", "error");
    redirect('../dashboard.php'); // A specific tab could be targeted, e.g., ?tab=dash-support
}


// --- 5. DATABASE INSERTION ---

// Prepare the SQL query to insert the new message into the 'messages' table.
// The sender_role is hardcoded as 'user' for security.
$sql = "INSERT INTO messages (user_id, sender_role, message) VALUES (?, 'user', ?)";

// Prepare the statement with the database connection.
$stmt = $conn->prepare($sql);

// Check if the statement preparation was successful.
if ($stmt === false) {
    // This indicates a server/database error. In production, this should be logged.
    set_message("A database error occurred. Please try again.", "error");
    redirect('../dashboard.php');
}

// Bind the user's ID (integer) and their message (string) to the placeholders.
$stmt->bind_param("is", $user_id, $message_text);


// --- 6. EXECUTE QUERY AND PROVIDE FEEDBACK ---

// Execute the prepared statement to save the message.
if ($stmt->execute()) {
    // If the query was successful, set a success message for the user.
    set_message("Your message has been sent successfully!", "success");
} else {
    // If the query failed, set an error message.
    set_message("There was an error sending your message. Please try again.", "error");
    // For debugging: error_log("Send message failed: " . $stmt->error);
}


// --- 7. CLEANUP AND REDIRECT ---

// Close the prepared statement to free up server resources.
$stmt->close();

// Close the database connection.
$conn->close();

// Redirect the user back to their dashboard. They will see the feedback message
// and their newly sent message in the conversation thread.
// Redirecting to a specific tab can improve user experience.
redirect('../dashboard.php?tab=dash-support');
?>