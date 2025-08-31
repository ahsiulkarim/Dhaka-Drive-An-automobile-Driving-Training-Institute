<?php
// Include the core configuration and helper functions file.
// This must be the first line to ensure session_start() is called before any output.
require_once 'config.php';

// Set the title for this specific page. This variable will be used in the header.
$pageTitle = "Login - Dhaka Drive";

// Include the header template. This will render the HTML head, body tag, and the main navigation.
include 'partials/header.php';

// --- Page-specific Logic ---

// Security check: If a user is already logged in, they should not see the login page.
// Instead, redirect them to their appropriate dashboard.
if (isLoggedIn()) {
    // Check if the logged-in user is an admin or a regular user and redirect accordingly.
    if (isAdmin()) {
        redirect('admin.php');
    } else {
        redirect('dashboard.php');
    }
}
?>

<!-- Start of the main content section for the login form -->
<section class="auth-form-section">
    <div class="container">

        <!-- 
            The login form.
            - It uses the POST method for security (credentials are not sent in the URL).
            - The 'action' attribute points to the PHP script that will process the login data.
        -->
        <form id="login-form" class="auth-form" action="actions/login_process.php" method="POST">

            <h2 class="form-title">Login to Your Account</h2>

            <?php
            // Display any feedback messages (like "Invalid password" or "Account created successfully")
            // that might have been set in the session by other scripts.
            display_message();
            ?>

            <!-- Email Input Group -->
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Enter your email"
                    required
                >
            </div>

            <!-- Password Input Group with Show/Hide Toggle -->
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                    <!-- The <i> tag is used for the show/hide icon, controlled by app.js -->
                    <i class="toggle-password-icon" aria-label="Show password"></i>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn">Login</button>

            <!-- Link to switch to the registration page -->
            <p class="form-switch">
                Don't have an account? <a href="signup.php">Sign up now</a>.
            </p>

        </form>
    </div>
</section>
<!-- End of the main content section -->

<?php
// Include the footer template. This will render the site footer and the closing HTML tags.
include 'partials/footer.php';
?>