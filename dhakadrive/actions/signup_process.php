<?php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($mobile) || empty($password)) {
        set_message("All fields are required.", "error");
        redirect('../signup.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_message("Invalid email format.", "error");
        redirect('../signup.php');
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        set_message("An account with this email already exists.", "error");
        redirect('../signup.php');
    }
    $stmt->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $otp = rand(100000, 999999);

    $stmt = $conn->prepare("INSERT INTO users (name, email, mobile, password, otp) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $mobile, $hashed_password, $otp);

    if ($stmt->execute()) {
        // In a real application, you would email the OTP. Here we'll store it for verification.
        $_SESSION['verify_email'] = $email; // Store email for the verification page
        set_message("Account created! An OTP has been sent to your email (simulated). Please verify. Your OTP is: " . $otp, "success");
        redirect('../otp-verify.php');
    } else {
        set_message("Error creating account: " . $conn->error, "error");
        redirect('../signup.php');
    }
    $stmt->close();
    $conn->close();
}
?>