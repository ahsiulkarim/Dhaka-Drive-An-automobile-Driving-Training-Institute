<?php
require_once '../config.php';

// Security: Must be a POST request from a logged-in user.
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isLoggedIn()) {
    redirect('../login.php');
}

// Get the form data.
$enrollment_id = isset($_POST['enrollment_id']) ? (int)$_POST['enrollment_id'] : 0;
$time_slot = isset($_POST['time_slot']) ? trim($_POST['time_slot']) : '';
$user_id = getUserId();

// Validate the data.
if ($enrollment_id <= 0 || empty($time_slot)) {
    set_message("Invalid data submitted. Please try again.", "error");
    redirect('../dashboard.php');
}

// Security: Verify again that the user owns this enrollment and it's ready for scheduling.
$stmt_verify = $conn->prepare("SELECT id FROM enrollments WHERE id = ? AND user_id = ? AND status = 'Approved' AND scheduled_slot IS NULL");
$stmt_verify->bind_param("ii", $enrollment_id, $user_id);
$stmt_verify->execute();

if ($stmt_verify->get_result()->num_rows !== 1) {
    set_message("This course cannot be scheduled at this time.", "error");
    redirect('../dashboard.php');
}
$stmt_verify->close();

// Update the database with the selected time slot.
$stmt_update = $conn->prepare("UPDATE enrollments SET scheduled_slot = ? WHERE id = ?");
$stmt_update->bind_param("si", $time_slot, $enrollment_id);

if ($stmt_update->execute()) {
    set_message("Your preferred schedule has been submitted successfully! We will contact you to confirm.", "success");
} else {
    set_message("A database error occurred. Please try again.", "error");
}

$stmt_update->close();
$conn->close();

// Redirect back to the dashboard.
redirect('../dashboard.php');
?>