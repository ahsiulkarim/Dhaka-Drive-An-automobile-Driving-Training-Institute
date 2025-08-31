<?php
// --- 1. INITIAL SETUP ---

// Include the core configuration file. This starts the session, connects to the
// database, and provides access to all helper functions.
require_once '../config.php';


// --- 2. SCRIPT EXECUTION GUARD ---

// This script must only be accessed via a form submission using the POST method.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    die("Error: Invalid access method.");
}


// --- 3. DETERMINE ACTION TYPE (USER EDIT vs. ADMIN EDIT) ---

// Check if the hidden 'is_admin_edit' field was submitted. This tells us an admin
// is performing the action.
$is_admin_edit = isset($_POST['is_admin_edit']) && $_POST['is_admin_edit'] == '1';

// Initialize variables that will be set based on the action type.
$user_id_to_update = 0;
$redirect_url = '../index.php'; // Default redirect location


// --- 4. AUTHENTICATION AND PERMISSION CHECKS ---

if ($is_admin_edit) {
    // --- SCENARIO A: ADMIN IS EDITING A USER ---

    // Security: Verify the person submitting the form IS an admin.
    if (!isAdmin()) {
        set_message("Access Denied. You do not have permission to perform this action.", "error");
        redirect('../login.php');
    }
    // For an admin edit, the user_id comes from the hidden form field.
    $user_id_to_update = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    // After the action, the admin should be sent back to the user management tab.
    $redirect_url = '../admin.php?tab=users';

} else {
    // --- SCENARIO B: USER IS EDITING THEIR OWN PROFILE ---

    // Security: Verify the person submitting the form IS a logged-in user.
    if (!isLoggedIn()) {
        set_message("You must be logged in to update your profile.", "error");
        redirect('../login.php');
    }
    // The user_id comes directly from their own session for security.
    $user_id_to_update = getUserId();
    // After the action, the user should be sent back to their dashboard.
    $redirect_url = '../dashboard.php';
}


// --- 5. DATA RETRIEVAL AND VALIDATION ---

// Retrieve the data submitted from the profile form.
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

// Validate the received data.
if (empty($name) || empty($mobile) || $user_id_to_update <= 0) {
    set_message("Full Name and Mobile Number are required. An error occurred.", "error");
    redirect($redirect_url);
}


// --- 6. DATABASE UPDATE ---

// Prepare the SQL query to update the user's record.
$sql = "UPDATE users SET name = ?, mobile = ?, address = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    set_message("A database error occurred. Please try again.", "error");
    redirect($redirect_url);
}

// Bind the sanitized inputs to the placeholders in the SQL query.
$stmt->bind_param("sssi", $name, $mobile, $address, $user_id_to_update);


// --- 7. EXECUTE QUERY AND PROVIDE FEEDBACK ---

if ($stmt->execute()) {
    // If the query was successful, set a success message.
    set_message("Profile has been updated successfully.", "success");

    // If a user was editing their OWN profile, we must also update their name
    // in the session so the "Welcome, [Name]!" message is correct immediately.
    if (!$is_admin_edit) {
        $_SESSION['user_name'] = $name;
    }
} else {
    // If the query failed, set an error message.
    set_message("There was an error updating the profile. Please try again.", "error");
    // For debugging: error_log("Profile update failed for user_id {$user_id_to_update}: " . $stmt->error);
}


// --- 8. CLEANUP AND REDIRECT ---

// Close the statement and database connection.
$stmt->close();
$conn->close();

// Redirect the user/admin back to the appropriate page.
redirect($redirect_url);
?>