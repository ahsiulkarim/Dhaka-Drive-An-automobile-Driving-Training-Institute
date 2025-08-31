<?php
require_once '../config.php';

if (!isset($_SESSION['verify_email'])) {
    redirect('../login.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = trim($_POST['otp']);
    $email = $_SESSION['verify_email'];

    if (empty($otp)) {
        set_message("Please enter the OTP.", "error");
        redirect('../otp-verify.php');
    }

    $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($otp == $user['otp']) {
            // OTP is correct, verify user
            $update_stmt = $conn->prepare("UPDATE users SET is_verified = 1, otp = NULL WHERE email = ?");
            $update_stmt->bind_param("s", $email);
            if ($update_stmt->execute()) {
                unset($_SESSION['verify_email']);
                set_message("Account verified successfully! Please log in to continue.", "success");
                redirect('../login.php');
            } else {
                set_message("Error verifying account. Please try again.", "error");
                redirect('../otp-verify.php');
            }
            $update_stmt->close();
        } else {
            set_message("Invalid OTP. Please try again.", "error");
            redirect('../otp-verify.php');
        }
    } else {
        set_message("Verification error. User not found.", "error");
        redirect('../login.php');
    }
    $stmt->close();
    $conn->close();
}
?>