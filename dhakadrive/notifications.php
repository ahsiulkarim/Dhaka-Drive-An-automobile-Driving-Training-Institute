<?php
// --- 1. INITIAL SETUP ---
require_once 'config.php';

// Security: A user MUST be logged in to see this page.
if (!isLoggedIn()) {
    set_message("You must be logged in to view your notifications.", "error");
    redirect('login.php');
}

// Set the page title for the header.
$pageTitle = "My Notifications - Dhaka Drive";

// --- 2. DATABASE LOGIC ---

$user_id = getUserId();
$notifications = []; // Prepare an array to hold the notification data.

// A) Fetch all notifications for the current user, newest first.
$stmt_fetch = $conn->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt_fetch->bind_param("i", $user_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row; // Add each notification to our array.
}
$stmt_fetch->close();

// B) After fetching, mark all unread notifications as "read".
// This happens automatically just by visiting this page.
$stmt_update = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$stmt_update->bind_param("i", $user_id);
$stmt_update->execute();
$stmt_update->close();


// --- 3. RENDER THE PAGE ---

// Include the standard site header.
include 'partials/header.php';
?>

<section id="notifications-page">
    <div class="container">
        <h1 class="section-title">My Notifications</h1>

        <div class="notification-list-container">
            <?php if (count($notifications) > 0): ?>
                <!-- If there are notifications, loop through and display them -->
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo ($notification['is_read'] == 0) ? 'unread' : ''; ?>">
                        <p><?php echo htmlspecialchars($notification['message']); ?></p>
                        <small><?php echo date('F j, Y, h:i A', strtotime($notification['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- If there are no notifications, show a friendly message -->
                <p class="text-center">You have no notifications.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Close the database connection and include the standard site footer.
$conn->close();
include 'partials/footer.php';
?>