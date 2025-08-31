<?php
require_once '../config.php';

// Security checks
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isAdmin()) {
    set_message("Access Denied.", "error");
    redirect('../login.php');
}

// Data retrieval
$enrollment_id = isset($_POST['enrollment_id']) ? (int)$_POST['enrollment_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';
// THE FIX IS HERE: Get the assigned location from the form
$assigned_location = isset($_POST['assigned_location']) ? trim($_POST['assigned_location']) : '';

if ($enrollment_id <= 0 || empty($action)) {
    set_message("Invalid request. Missing information.", "error");
    redirect('../admin.php?tab=enrollments');
}

// Fetch current enrollment data
$stmt_fetch = $conn->prepare("SELECT user_id, course_name FROM enrollments WHERE id = ?");
$stmt_fetch->bind_param("i", $enrollment_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
if ($result->num_rows !== 1) {
    set_message("Error: Enrollment record not found.", "error");
    redirect('../admin.php?tab=enrollments');
}
$enrollment_data = $result->fetch_assoc();
$user_id_to_notify = $enrollment_data['user_id'];
$course_name_for_notification = $enrollment_data['course_name'];
$stmt_fetch->close();

// Business logic
$new_status = '';
$notification_message = '';
$feedback_message = '';
$feedback_type = 'success';
$sql_update_query = "UPDATE enrollments SET status = ? WHERE id = ?"; // Default query
$sql_params_types = "si"; // Default types: string, integer
$sql_params_values = []; // Default values array

switch ($action) {
    case 'approve_request':
        $new_status = 'Awaiting Payment';
        $notification_message = "Good news! Your request for \"{$course_name_for_notification}\" has been approved. Please complete the payment from your dashboard.";
        $feedback_message = 'Request approved. User notified to make payment.';
        $sql_params_values = [$new_status, $enrollment_id];
        break;

    case 'reject_request':
        $new_status = 'Not Available';
        $notification_message = "We're sorry, your request for \"{$course_name_for_notification}\" could not be fulfilled at this time. Please contact support.";
        $feedback_message = 'Request has been rejected.';
        $sql_params_values = [$new_status, $enrollment_id];
        break;

    case 'confirm_payment':
        // THE FIX IS HERE: Validate that the location was submitted for this action
        if (empty($assigned_location)) {
            set_message("Please enter the final class location before confirming payment.", "error");
            redirect('../admin.php?tab=enrollments');
        }
        $new_status = 'Approved';
        $notification_message = "Payment confirmed for \"{$course_name_for_notification}\"! Your assigned location is: {$assigned_location}. You can now schedule your classes from the dashboard.";
        $feedback_message = 'Payment confirmed and location assigned. User notified.';
        // We need a different query to update the location as well
        $sql_update_query = "UPDATE enrollments SET status = ?, assigned_location = ? WHERE id = ?";
        $sql_params_types = "ssi"; // string, string, integer
        $sql_params_values = [$new_status, $assigned_location, $enrollment_id];
        break;

    case 'reject_payment':
        $new_status = 'Payment Rejected';
        $notification_message = "There was an issue with your submitted payment for \"{$course_name_for_notification}\". Please review and try again, or contact support.";
        $feedback_message = 'Payment has been rejected.';
        $sql_params_values = [$new_status, $enrollment_id];
        break;
    
    default:
        $feedback_message = 'Invalid action specified.';
        $feedback_type = 'error';
        break;
}

// Database update and notification
if (!empty($feedback_message) && $feedback_type === 'success') {
    $conn->begin_transaction();
    try {
        // Prepare and execute the dynamic update statement
        $stmt_update = $conn->prepare($sql_update_query);
        // Use call_user_func_array to bind a dynamic number of parameters
        $stmt_update->bind_param($sql_params_types, ...$sql_params_values);
        $stmt_update->execute();
        $stmt_update->close();

        // Send notification
        $stmt_notify = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt_notify->bind_param("is", $user_id_to_notify, $notification_message);
        $stmt_notify->execute();
        $stmt_notify->close();

        $conn->commit();
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $feedback_message = "A database error occurred. The operation was cancelled.";
        $feedback_type = "error";
        error_log("Enrollment update failed: " . $exception->getMessage());
    }
}

// Redirect with feedback
set_message($feedback_message, $feedback_type);
redirect('../admin.php?tab=enrollments');
?>