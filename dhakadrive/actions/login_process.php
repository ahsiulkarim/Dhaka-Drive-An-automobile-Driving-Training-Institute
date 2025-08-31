<?php
// --- 1. INITIAL SETUP ---

// Include the core configuration file. This file contains the database connection,
// session_start(), and all helper functions. It's the first thing we need.
require_once '../config.php';


// --- 2. SCRIPT EXECUTION GUARD ---

// This script is designed to process form data submitted via the POST method.
// This 'if' block ensures that the code is only executed if it's a POST request,
// preventing users from accessing this page directly via their browser's address bar.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 3. RETRIEVE AND SANITIZE FORM DATA ---

    // Retrieve the email and password from the submitted form data ($_POST superglobal).
    // The trim() function is used to remove any accidental whitespace from the beginning
    // or end of the user's input.
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    // --- 4. INPUT VALIDATION ---

    // Check if either of the required fields is empty. If so, we don't need to
    // query the database. We can immediately send the user back with an error message.
    if (empty($email) || empty($password)) {
        // Use the helper function from config.php to set a feedback message in the session.
        set_message("Email and password are required.", "error");
        // Use the helper function to redirect the user back to the login page.
        redirect('../login.php');
    }


    // --- 5. DATABASE QUERY (SECURELY) ---

    // Prepare a SQL query to select the user from the database based on their email.
    // Using a prepared statement with a placeholder (?) is crucial for preventing SQL injection attacks.
    $sql = "SELECT id, name, email, password, role, account_status, is_verified FROM users WHERE email = ?";
    
    // Prepare the statement with the database connection.
    $stmt = $conn->prepare($sql);

    // If the statement preparation fails, it indicates a server/database error.
    if ($stmt === false) {
        // In a production environment, you would log this error instead of displaying it.
        die("Error preparing the statement: " . $conn->error);
    }
    
    // Bind the user's email input (as a string, "s") to the placeholder in the SQL query.
    $stmt->bind_param("s", $email);
    
    // Execute the prepared statement.
    $stmt->execute();
    
    // Get the result set from the executed statement.
    $result = $stmt->get_result();


    // --- 6. PROCESS THE QUERY RESULT ---

    // Check if exactly one user was found with the provided email.
    if ($result->num_rows === 1) {
        
        // Fetch the user's data from the result set as an associative array.
        $user = $result->fetch_assoc();

        // --- 7. VERIFY PASSWORD HASH ---

        // Use PHP's built-in password_verify() function to securely check if the
        // submitted password matches the hashed password stored in the database.
        // This is the correct and secure way to handle password verification.
        if (password_verify($password, $user['password'])) {
            
            // --- 8. POST-VERIFICATION CHECKS ---

            // Check if the user's account has been deactivated by an admin.
            if ($user['account_status'] === 'inactive') {
                set_message("Your account is deactivated. Please contact support.", "error");
                redirect('../login.php');
            }

            // Check if the user is a regular user and has not yet verified their account via OTP.
            if ($user['role'] === 'user' && $user['is_verified'] == 0) {
                // Generate a new OTP for security.
                $new_otp = rand(100000, 999999);
                
                // Update the database with the new OTP.
                $update_otp_stmt = $conn->prepare("UPDATE users SET otp = ? WHERE id = ?");
                $update_otp_stmt->bind_param("si", $new_otp, $user['id']);
                $update_otp_stmt->execute();
                $update_otp_stmt->close();
                
                // Set session variable to allow access to the OTP page.
                $_SESSION['verify_email'] = $user['email'];

                // In a real application, an email would be sent here. We simulate it with an alert message.
                set_message("Your account is not verified. A new OTP has been sent (simulated). Your OTP is: " . $new_otp, "info");
                redirect('../otp-verify.php');
            }

            // --- 9. ESTABLISH USER SESSION ---

            // If all checks pass, the login is successful.
            // We store essential, non-sensitive user information in the session.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Record this successful login event in the login_history table.
            $history_stmt = $conn->prepare("INSERT INTO login_history (user_id) VALUES (?)");
            $history_stmt->bind_param("i", $user['id']);
            $history_stmt->execute();
            $history_stmt->close();

            // --- 10. REDIRECT USER TO THEIR DASHBOARD ---

            // Redirect based on the user's role.
            if (isAdmin()) {
                redirect('../admin.php');
            } else {
                redirect('../index.php');
            }

        } else {
            // This block runs if the password does not match the hash.
            set_message("Invalid email or password.", "error");
            redirect('../login.php');
        }

    } else {
        // This block runs if no user was found with the given email address.
        // We use the same generic error message to prevent "user enumeration" attacks.
        set_message("Invalid email or password.", "error");
        redirect('../login.php');
    }

    // --- 11. CLEANUP ---

    // Close the statement and the database connection to free up resources.
    $stmt->close();
    $conn->close();

} else {
    // If the script was accessed without a POST request, redirect to the homepage.
    redirect('../index.php');
}
?>