<?php
// --- 1. INITIAL SETUP ---

// Include the core configuration file. This starts the session, connects to the
// database, and provides access to all helper functions.
require_once '../config.php';


// --- 2. SCRIPT EXECUTION GUARD ---

// This script is intended to be accessed only via a form submission using the POST method.
// This check prevents direct URL access to the script.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 3. AUTHENTICATION CHECK ---

    // A user must be logged in to enroll in a course.
    // If they are not, we set a message and redirect them to the login page.
    if (!isLoggedIn()) {
        // We also store their intended course in the session, so we can potentially
        // redirect them back after they log in (an advanced feature, but good to have).
        $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? '../index.php';
        set_message("You must be logged in to enroll in a course.", "error");
        redirect('../login.php');
    }


    // --- 4. DATA RETRIEVAL AND VALIDATION ---

    // Retrieve the user ID from the active session.
    $user_id = getUserId();

    // Retrieve and sanitize the data submitted from the form.
    // The trim() function removes any accidental leading/trailing whitespace.
    $course_name = isset($_POST['course_name']) ? trim($_POST['course_name']) : '';
    $preferred_location = isset($_POST['preferred_location']) ? trim($_POST['preferred_location']) : '';
    $terms_agreed = isset($_POST['terms_agree']);

    // Validate the received data. If any required information is missing,
    // send the user back to the previous page with an error message.
    if (empty($course_name) || empty($preferred_location)) {
        set_message("Course name and preferred location are required.", "error");
        // HTTP_REFERER sends them back to the course page they were just on.
        redirect($_SERVER['HTTP_REFERER']);
    }

    if (!$terms_agreed) {
        set_message("You must agree to the Terms and Conditions to enroll.", "error");
        redirect($_SERVER['HTTP_REFERER']);
    }


    // --- 5. BUSINESS LOGIC: PREVENT DUPLICATE PENDING ENROLLMENTS ---

    // We should prevent a user from submitting multiple requests for the same course
    // if their previous request is still pending (e.g., 'Requested', 'Awaiting Payment').
    $check_sql = "SELECT id FROM enrollments 
                  WHERE user_id = ? 
                  AND course_name = ? 
                  AND status NOT IN ('Completed', 'Not Available', 'Payment Rejected', 'Cancelled')";
    
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("is", $user_id, $course_name);
    $stmt_check->execute();
    $stmt_check->store_result();

    // If a pending enrollment already exists (num_rows > 0), inform the user.
    if ($stmt_check->num_rows > 0) {
        set_message("You already have a pending enrollment request for this course. Please check your dashboard for updates.", "info");
        redirect('../dashboard.php');
    }
    $stmt_check->close();


    // --- 6. DATABASE INSERTION ---

    // If all checks pass, we can proceed to insert the new enrollment request into the database.
    // The status is set to 'Requested' by default.
    $insert_sql = "INSERT INTO enrollments (user_id, course_name, preferred_location, status) VALUES (?, ?, ?, 'Requested')";
    
    // Use a prepared statement to securely insert the data.
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("iss", $user_id, $course_name, $preferred_location);

    
    // --- 7. PROVIDE FEEDBACK AND REDIRECT ---

    // Execute the insertion query and check if it was successful.
    if ($stmt_insert->execute()) {
        // If successful, set a success message.
        set_message("Your enrollment request has been sent successfully! You will be notified of any updates on your dashboard.", "success");
    } else {
        // If there was a database error, set an error message.
        set_message("There was an error submitting your request. Please try again later.", "error");
        // For debugging, you might want to log the error: error_log($stmt_insert->error);
    }

    // Close the prepared statement.
    $stmt_insert->close();

    // No matter the outcome, redirect the user to their dashboard to see the result.
    redirect('../dashboard.php');

} else {
    // If the script is accessed directly without a POST request, redirect to the homepage.
    redirect('../index.php');
}

// --- 8. CLEANUP ---
// Close the database connection at the end of the script.
$conn->close();
?>