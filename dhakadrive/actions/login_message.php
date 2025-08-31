<?php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        set_message("Email and password are required.", "error");
        redirect('../login.php');
    }

    $stmt = $conn->prepare("SELECT id, name, email, password, role, account_status, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            if ($user['account_status'] === 'inactive') {
                set_message("Your account is deactivated. Please contact support.", "error");
                redirect('../login.php');
            }

            if ($user['is_verified'] == 0 && $user['role'] == 'user') {
                $_SESSION['verify_email'] = $email;
                $otp = rand(100000, 999999);
                $update_otp_stmt = $conn->prepare("UPDATE users SET otp = ? WHERE email = ?");
                $update_otp_stmt->bind_param("ss", $otp, $email);
                $update_otp_stmt->execute();
                $update_otp_stmt->close();

                set_message("Your account is not verified. Please check your email for a new OTP. Your new OTP is: " . $otp, "info");
                redirect('../otp-verify.php');
            }

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Log the login event
            $history_stmt = $conn->prepare("INSERT INTO login_history (user_id) VALUES (?)");
            $history_stmt->bind_param("i", $user['id']);
            $history_stmt->execute();
            $history_stmt->close();

            redirect(isAdmin() ? '../admin.php' : '../dashboard.php');

        } else {
            set_message("Invalid email or password.", "error");
            redirect('../login.php');
        }
    } else {
        set_message("Invalid email or password.", "error");
        redirect('../login.php');
    }
    $stmt->close();
    $conn->close();
}
?>