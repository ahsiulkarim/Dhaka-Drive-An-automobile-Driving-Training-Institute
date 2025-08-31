<?php
$pageTitle = "Verify Account - Dhaka Drive";
include 'partials/header.php';

// Security check: if there's no email in session for verification, redirect to login
if (!isset($_SESSION['verify_email'])) {
    set_message("Invalid verification attempt. Please log in to resend OTP.", "error");
    redirect('login.php');
}
?>

<section class="auth-form-section">
    <div class="container">
        <form id="otp-verify-form" class="auth-form" action="actions/otp_process.php" method="POST">
            <h2 class="form-title">Verify Your Account</h2>
            <?php display_message(); ?>
            <p class="text-center" style="margin-bottom: 1.5rem;">An OTP has been sent to your email (<?php echo htmlspecialchars($_SESSION['verify_email']); ?>). Please enter it below.</p>
            <div class="form-group">
                <label for="otp">Enter 6-Digit OTP</label>
                <input type="text" id="otp" name="otp" required maxlength="6" pattern="\d{6}" title="Please enter a 6-digit OTP">
            </div>
            <button type="submit" class="btn">Verify & Login</button>
        </form>
    </div>
</section>

<?php include 'partials/footer.php'; ?>