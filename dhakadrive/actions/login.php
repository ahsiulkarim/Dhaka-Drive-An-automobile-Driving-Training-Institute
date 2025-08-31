<?php

// Go up one directory to find the config file
include '../config.php'; 

// Start the session to manage user login state
session_start();

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize user inputs to prevent XSS
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password']; // We don't sanitize the password before hashing/checking

    // 2. Prepare SQL statement to prevent SQL Injection
    $sql = "SELECT id, name, email, password, user_type FROM `register` WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // 3. Bind parameters and execute the statement
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // 4. Check if exactly one user was found
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // 5. Verify the password
            // SECURITY WARNING: md5() is not secure. Use password_verify() for new projects.
            // This code is for compatibility with your existing database structure.
            if (md5($password) == $user['password']) {
                
                // Password is correct, regenerate session ID for security
                session_regenerate_id(true);

                // 6. Store user data in the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_type'] = $user['user_type'];

                // 7. Redirect based on user role
                if ($user['user_type'] == 'admin') {
                    // Redirect to the admin dashboard (path is relative to the root)
                    header("Location: ../admin/dashboard.php");
                    exit();
                } else {
                    // Redirect to the user homepage
                    header("Location: ../home.php");
                    exit();
                }

            } else {
                // Password was incorrect
                $_SESSION['error_message'] = "Incorrect email or password. Please try again.";
                header("Location: ../login.php");
                exit();
            }

        } else {
            // No user found with that email
            $_SESSION['error_message'] = "Incorrect email or password. Please try again.";
            header("Location: ../login.php");
            exit();
        }

        mysqli_stmt_close($stmt);

    } else {
        // Database query failed
        $_SESSION['error_message'] = "A database error occurred. Please try again later.";
        header("Location: ../login.php");
        exit();
    }
    
    mysqli_close($conn);

} else {
    // If the page is accessed directly, redirect to the login form
    header("Location: ../login.php");
    exit();
}

?>