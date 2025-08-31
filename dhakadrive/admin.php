<?php
require_once 'config.php';

// Security check: Ensure the user is logged in and is an admin.
if (!isAdmin()) {
    set_message("Access Denied. You must be an administrator to view this page.", "error");
    redirect('login.php');
}

$pageTitle = "Admin Panel - Dhaka Drive";

// --- Data Fetching ---

// 1. Fetch All Enrollments
$enrollments_sql = "SELECT e.*, u.name as user_name, u.email as user_email, u.mobile as user_mobile 
                    FROM enrollments e 
                    JOIN users u ON e.user_id = u.id 
                    ORDER BY e.created_at DESC";
$enrollments_result = $conn->query($enrollments_sql);

// 2. Fetch All Users (excluding the admin)
$users_sql = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC";
$users_result = $conn->query($users_sql);

// 3. Fetch All Login History
$login_history_sql = "SELECT lh.*, u.name as user_name, u.email as user_email
                      FROM login_history lh
                      JOIN users u ON lh.user_id = u.id
                      ORDER BY lh.login_time DESC LIMIT 100";
$login_history_result = $conn->query($login_history_sql);


// 4. Fetch Conversations for Messaging Tab
$conversations_sql = "
    SELECT 
        m.user_id,
        u.name as user_name,
        (SELECT message FROM messages WHERE user_id = m.user_id ORDER BY timestamp DESC LIMIT 1) as last_message,
        (SELECT timestamp FROM messages WHERE user_id = m.user_id ORDER BY timestamp DESC LIMIT 1) as last_message_time
    FROM messages m
    JOIN users u ON m.user_id = u.id
    GROUP BY m.user_id, u.name
    ORDER BY last_message_time DESC
";
$conversations_result = $conn->query($conversations_sql);

// 5. Fetch Messages for a specific selected conversation
$selected_user_messages = [];
$selected_user_name = 'Select a conversation'; // Default title
$selected_user_id = null;
if (isset($_GET['tab']) && $_GET['tab'] === 'messaging' && isset($_GET['user_id'])) {
    $selected_user_id = (int)$_GET['user_id'];
    // Also fetch the user's name for the title
    $user_name_stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $user_name_stmt->bind_param("i", $selected_user_id);
    $user_name_stmt->execute();
    $user_name_result = $user_name_stmt->get_result();
    if($user_name_row = $user_name_result->fetch_assoc()){
        $selected_user_name = 'Conversation with ' . htmlspecialchars($user_name_row['name']);
    }
    $user_name_stmt->close();

    $messages_stmt = $conn->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY timestamp ASC");
    $messages_stmt->bind_param("i", $selected_user_id);
    $messages_stmt->execute();
    $selected_user_messages_result = $messages_stmt->get_result();
    while ($row = $selected_user_messages_result->fetch_assoc()) {
        $selected_user_messages[] = $row;
    }
    $messages_stmt->close();
}

// 6. Fetch Initial Support Requests
$support_requests_sql = "
    SELECT m.*, u.name as user_name, u.email as user_email FROM messages m
    JOIN users u ON m.user_id = u.id
    WHERE m.id IN (SELECT MIN(id) FROM messages WHERE sender_role = 'user' GROUP BY user_id)
    ORDER BY m.timestamp DESC
";
$support_requests_result = $conn->query($support_requests_sql);

// Include the header at the end of the PHP logic block
include 'partials/header.php';
?>

<section id="admin-panel">
    <div class="container">
        <h1 class="admin-title">Admin Dashboard</h1>
        
        <?php display_message(); ?>

        <div class="admin-tabs">
            <a href="admin.php?tab=enrollments" class="tab-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'enrollments') ? 'active' : ''; ?>">Enrollments</a>
            <a href="admin.php?tab=users" class="tab-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'users') ? 'active' : ''; ?>">User Management</a>
            <a href="admin.php?tab=messaging" class="tab-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'messaging') ? 'active' : ''; ?>">Messaging</a>
            <a href="admin.php?tab=support" class="tab-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'support') ? 'active' : ''; ?>">Support Requests</a>
            <a href="admin.php?tab=log-history" class="tab-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'log-history') ? 'active' : ''; ?>">Login History</a>
        </div>

        <!-- Enrollments Tab -->
        <div id="enrollments" class="tab-content <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'enrollments') ? 'active' : ''; ?>">
            <h2 class="section-title">All Course Enrollments</h2>
            <div class="admin-table-container">
                <table id="enrollments-table">
                    <thead>
                        <tr><th>User Info</th><th>Course</th><th>Location & Schedule</th><th>Date</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if ($enrollments_result && $enrollments_result->num_rows > 0): ?>
                            <?php while($enroll = $enrollments_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($enroll['user_name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($enroll['user_email']); ?><br><?php echo htmlspecialchars($enroll['user_mobile']); ?></small>
                                        <?php if ($enroll['payment_method']): ?>
                                            <br><small><strong>Payment:</strong> <?php echo htmlspecialchars($enroll['payment_method']); ?>
                                            <?php if ($enroll['trx_id'] && $enroll['trx_id'] !== 'N/A' && $enroll['trx_id'] !== 'SIMULATED_CARD_PAYMENT'): ?>
                                                (TrxID: <?php echo htmlspecialchars($enroll['trx_id']); ?>)
                                            <?php endif; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($enroll['course_name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($enroll['assigned_location'] ?: 'N/A'); ?><br/>
                                        <small><strong>Schedule:</strong> <?php echo htmlspecialchars($enroll['scheduled_slot'] ?: 'Not Scheduled'); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($enroll['created_at'])); ?></td>
                                    <td><span class="status <?php echo strtolower(str_replace(' ', '-', $enroll['status'])); ?>"><?php echo htmlspecialchars($enroll['status']); ?></span></td>


                                    <td>
    <div class="action-buttons">
        <form action="actions/update_enrollment_status.php" method="POST" class="enrollment-action-form">
            <input type="hidden" name="enrollment_id" value="<?php echo $enroll['id']; ?>">
            
            <?php if ($enroll['status'] == 'Requested'): ?>
                <button type="submit" name="action" value="approve_request" class="btn btn-sm btn-approve">Approve</button>
                <button type="submit" name="action" value="reject_request" class="btn btn-sm btn-reject">Reject</button>

            <?php elseif ($enroll['status'] == 'Payment Submitted' || $enroll['status'] == 'Awaiting Cash Payment'): ?>
                <!-- THE FIX IS HERE: Added a text input for the location -->
                <div class="location-input-group">
                    <input 
                        type="text" 
                        name="assigned_location" 
                        class="form-control-sm" 
                        placeholder="Enter final location" 
                        value="<?php echo htmlspecialchars($enroll['preferred_location']); ?>" 
                        required 
                    >
                    <button type="submit" name="action" value="confirm_payment" class="btn btn-sm btn-approve">
                        <?php echo ($enroll['status'] == 'Awaiting Cash Payment') ? 'Confirm Cash' : 'Confirm'; ?>
                    </button>
                </div>
                <!-- Add a reject button for digital payments -->
                <?php if ($enroll['status'] == 'Payment Submitted'): ?>
                    <button type="submit" name="action" value="reject_payment" class="btn btn-sm btn-reject">Reject</button>
                <?php endif; ?>

            <?php else: ?>
                <span>Processed</span>
            <?php endif; ?>
        </form>
    </div>
</td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No enrollments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Management Tab -->
        <div id="users" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'users') ? 'active' : ''; ?>">
            <h2 class="section-title">All Registered Users</h2>
            <div class="admin-table-container">
                <table id="users-table">
                    <thead>
                        <tr><th>Name</th><th>Contact</th><th>Address</th><th>Document Info</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if ($users_result && $users_result->num_rows > 0): ?>
                            <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr 
                                    data-user-id="<?php echo $user['id']; ?>"
                                    data-user-name="<?php echo htmlspecialchars($user['name']); ?>"
                                    data-user-mobile="<?php echo htmlspecialchars($user['mobile']); ?>"
                                    data-user-address="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"
                                >
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?><br><?php echo htmlspecialchars($user['mobile']); ?></td>
                                    <td><?php echo htmlspecialchars($user['address'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php if (!empty($user['document_path'])): ?>
                                            <?php echo htmlspecialchars($user['document_number'] ?: 'Number not provided'); ?>
                                            <a href="<?php echo htmlspecialchars($user['document_path']); ?>" target="_blank" class="view-file-link" download>View File</a>
                                        <?php else: ?>
                                            Not Submitted
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="status <?php echo $user['account_status']; ?>"><?php echo $user['account_status']; ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm" data-action="edit-user">Edit</button>
                                            <?php if ($user['account_status'] == 'active'): ?>
                                                <a href="actions/update_user_status.php?id=<?php echo $user['id']; ?>&status=inactive" class="btn btn-sm btn-reject confirm-action" data-confirm-message="Are you sure you want to deactivate this user?">Deactivate</a>
                                            <?php else: ?>
                                                <a href="actions/update_user_status.php?id=<?php echo $user['id']; ?>&status=active" class="btn btn-sm btn-approve confirm-action" data-confirm-message="Are you sure you want to reactivate this user?">Reactivate</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Messaging Tab -->
        <div id="messaging" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'messaging') ? 'active' : ''; ?>">
            <h2 class="section-title">User Conversations</h2>
            <div class="messaging-grid">
                <div class="conversation-list-container">
                    <h3>Conversations</h3>
                    <ul id="conversation-list">
                        <?php if ($conversations_result && $conversations_result->num_rows > 0): ?>
                            <?php while($convo = $conversations_result->fetch_assoc()): ?>
                                <a href="admin.php?tab=messaging&user_id=<?php echo $convo['user_id']; ?>" style="text-decoration:none; color:inherit;">
                                <li data-userid="<?php echo $convo['user_id']; ?>">
                                    <strong><?php echo htmlspecialchars($convo['user_name']); ?></strong>
                                    <small><?php echo htmlspecialchars(substr($convo['last_message'], 0, 25)); ?>...</small>
                                </li>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>No conversations found.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="message-view-container">
                    <h3 id="current-convo-title"><?php echo $selected_user_name; ?></h3>
                    <div id="admin-conversation-thread">
                        <?php if (!empty($selected_user_messages)): ?>
                            <?php foreach($selected_user_messages as $msg): ?>
                                <div class="message-bubble <?php echo $msg['sender_role'] === 'admin' ? 'admin-bubble' : 'user-bubble'; ?>">
                                    <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                    <small><?php echo date('M d, Y h:i A', strtotime($msg['timestamp'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="message-placeholder">Messages will appear here.</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($selected_user_id): ?>
                    <form id="admin-reply-form" action="actions/admin_reply.php" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $selected_user_id; ?>">
                        <div class="form-group">
                            <textarea name="message" id="admin-reply-message" rows="3" placeholder="Type your reply..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm">Send Message</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Support Requests Tab -->
        <div id="support" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'support') ? 'active' : ''; ?>">
            <h2 class="section-title">Initial Support Requests</h2>
            <div id="support-messages-list" class="support-list">
                <?php if ($support_requests_result && $support_requests_result->num_rows > 0): ?>
                    <?php while($req = $support_requests_result->fetch_assoc()): ?>
                        <div class="support-item">
                            <div class="support-item-header">
                                <strong>From: <?php echo htmlspecialchars($req['user_name']); ?> (<?php echo htmlspecialchars($req['user_email']); ?>)</strong>
                                <span><?php echo date('M d, Y h:i A', strtotime($req['timestamp'])); ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($req['message'])); ?></p>
                            <a href="admin.php?tab=messaging&user_id=<?php echo $req['user_id']; ?>" class="btn btn-sm">Reply</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No new support requests found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Login History Tab -->
        <div id="log-history" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'log-history') ? 'active' : ''; ?>">
            <h2 class="section-title">User Login History</h2>
            <div class="admin-table-container">
                <table id="login-history-table">
                    <thead><tr><th>User Name</th><th>Email</th><th>Login Time</th></tr></thead>
                    <tbody>
                        <?php if ($login_history_result && $login_history_result->num_rows > 0): ?>
                            <?php while($log = $login_history_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($log['user_email']); ?></td>
                                    <td><?php echo date('M d, Y h:i:s A', strtotime($log['login_time'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No login history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- This closes the container div -->
</section> <!-- This closes the admin-panel section -->

<!-- ============================================= -->
<!--          MODAL IS PLACED HERE, ONCE           -->
<!-- ============================================= -->
<div id="user-edit-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal">×</span>
        <h2 class="modal-title">Edit User Information</h2>
        <form id="user-edit-form" action="actions/update_user_profile.php" method="POST">
            <input type="hidden" name="user_id" id="edit-user-id">
            <input type="hidden" name="is_admin_edit" value="1">
            <div class="form-group">
                <label for="edit-user-name">Full Name</label>
                <input type="text" name="name" id="edit-user-name" required>
            </div>
            <div class="form-group">
                <label for="edit-user-mobile">Mobile Number</label>
                <input type="tel" name="mobile" id="edit-user-mobile" required>
            </div>
            <div class="form-group">
                <label for="edit-user-address">Address</label>
                <textarea name="address" id="edit-user-address" rows="3" placeholder="Enter user's full address"></textarea>
            </div>
            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<?php
// Close the database connection
$conn->close();

// Include the standard footer
include 'partials/footer.php';
?>

<!-- ============================================= -->
<!--    LINK TO THE DEDICATED ADMIN JAVASCRIPT     -->
<!-- ============================================= -->
<script src="js/admin.js"></script>

</body>
</html>