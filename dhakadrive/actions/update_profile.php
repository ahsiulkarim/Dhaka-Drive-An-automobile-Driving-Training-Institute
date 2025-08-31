<?php
// --- 1. INITIAL SETUP ---

// Include the core configuration file. This starts the session, connects to the
// database, and gives us access to all helper functions.
require_once '../config.php';


// --- 2. SCRIPT EXECUTION GUARD ---

// This script is designed to process data from a form. We must ensure it's
// accessed via the POST method to prevent direct URL access.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    die("Error: This page can only be accessed by submitting the profile form.");
}


// --- 3. SECURITY & AUTHENTICATION CHECK ---

// A user must be logged in to update their own profile.
// If not, we redirect them to the login page.
if (!isLoggedIn()) {
    set_message("You must be logged in to update your profile.", "error");
    redirect('../login.php');
}


// --- 4. DATA RETRIEVAL AND VALIDATION ---

// Get the ID of the currently logged-in user from their session.
$user_id = getUserId();

// Retrieve the data submitted from the profile form.
// The trim() function removes any accidental whitespace from the user's input.
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

// Perform server-side validation. Even if the HTML has 'required', we must
// check again on the server for security.
if (empty($name) || empty($mobile)) {
    // If essential information is missing, set an error message and send the user back.
    set_message("Full Name and Mobile Number are required fields.", "error");
    redirect('../dashboard.php');
}

// Optional: More advanced validation can be added here, for example:
// - Check if the mobile number contains only valid characters.
// - Check if the name length is within a reasonable range.


// --- 5. DATABASE UPDATE OPERATION ---

// Prepare the SQL query to update the user's record in the 'users' table.
// We use a prepared statement with placeholders (?) to prevent SQL injection.
$sql = "UPDATE users SET name = ?, mobile = ?, address = ? WHERE id = ?";

// Prepare the statement with the database connection.
$stmt = $conn->prepare($sql);

// Check if the statement preparation was successful.
if ($stmt === false) {
    // This indicates a syntax error in the SQL or a problem with the database.
    // In a production environment, you would log this error.
    set_message("A database error occurred. Please try again.", "error");
    redirect('../dashboard.php');
}

// Bind the sanitized user inputs to the placeholders in the SQL query.
// The types are: "sssi" (string, string, string, integer).
$stmt->bind_param("sssi", $name, $mobile, $address, $user_id);


// --- 6. EXECUTE QUERY AND PROVIDE FEEDBACK ---

// Execute the prepared statement.
if ($stmt->execute()) {
    // The query was successful.
    
    // IMPORTANT: Update the user's name in the session as well, so the
    // "Welcome, [Name]!" message on the dashboard updates immediately
    // without requiring the user to log out and back in.
    $_SESSION['user_name'] = $name;

    // Set a success message to be displayed on the dashboard.
    set_message("Your profile has been updated successfully.", "success");
} else {
    // The query failed to execute.
    set_message("There was an error updating your profile. Please try again.", "error");
    // For debugging: error_log("Profile update failed: " . $stmt->error);
}


// --- 7. CLEANUP AND REDIRECT ---

// Close the prepared statement to free up server resources.
$stmt->close();

// Close the database connection.
$conn->close();

// Redirect the user back to their dashboard where they will see the feedback message.
redirect('../dashboard.php');

?>