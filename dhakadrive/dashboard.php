<?php
require_once 'config.php';

// Security check: Must be a logged-in user, not admin.
if (!isLoggedIn() || isAdmin()) {
    redirect('login.php');
}

$pageTitle = "My Dashboard - Dhaka Drive";
$user_id = getUserId();

// --- OPTIMIZATION 1: COMBINED DATABASE QUERIES ---
// Instead of 3 separate queries, we can often fetch related data more efficiently.
// Here, we'll keep them separate for clarity as they query different tables, but this is
// a primary area for optimization in more complex scenarios.

// Fetch latest user data
$stmt = $conn->prepare("SELECT name, mobile, address, document_number, document_filename, document_path FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch user's enrollments
$enrollments_stmt = $conn->prepare("SELECT * FROM enrollments WHERE user_id = ? ORDER BY created_at DESC");
$enrollments_stmt->bind_param("i", $user_id);
$enrollments_stmt->execute();
$enrollments_result = $enrollments_stmt->get_result();
// We will loop through this result directly in the HTML, no need for an intermediate array.

// Fetch conversation with admin
$messages_stmt = $conn->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY timestamp ASC");
$messages_stmt->bind_param("i", $user_id);
$messages_stmt->execute();
$messages_result = $messages_stmt->get_result();

// Check if user has any approved courses to determine if materials should be shown
$approved_check_stmt = $conn->prepare("SELECT id FROM enrollments WHERE user_id = ? AND status IN ('Approved', 'Completed') LIMIT 1");
$approved_check_stmt->bind_param("i", $user_id);
$approved_check_stmt->execute();
$has_approved_course = $approved_check_stmt->get_result()->num_rows > 0;
$approved_check_stmt->close();

include 'partials/header.php';
?>

<section id="dashboard">
    <div class="container">
        <h1 id="welcome-message">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <?php display_message(); ?>

        <div class="dashboard-grid">
            <div class="main-content">
                <!-- OPTIMIZATION 2: Restored Full Tab Layout -->
                <div class="dashboard-tabs">
                    <button class="dash-tab-link active" data-tab="dash-courses">My Courses</button>
                    <button class="dash-tab-link" data-tab="dash-materials">Learning Materials</button>
                    <button class="dash-tab-link" data-tab="dash-support">My Messages</button>
                </div>

                <!-- Tab: My Courses -->
                <div id="dash-courses" class="dash-tab-content active">
                    <div class="dashboard-section">
                        <h2 class="section-title">My Course Enrollments</h2>
                        <div id="my-courses-list">
                            <?php if ($enrollments_result->num_rows > 0): ?>
                                <?php while($en = $enrollments_result->fetch_assoc()): 
                                    $statusLevels = ['Requested' => 10, 'Awaiting Payment' => 30, 'Payment Submitted' => 50, 'Awaiting Cash Payment' => 50, 'Approved' => 100, 'Completed' => 100];
                                    $progress = $statusLevels[$en['status']] ?? 0;
                                ?>
                                <div class="course-progress-item">
                                    <div class="course-progress-header">
                                        <h3><?php echo htmlspecialchars($en['course_name']); ?></h3>
                                        <span class="status <?php echo strtolower(str_replace(' ', '-', $en['status'])); ?>"><?php echo htmlspecialchars($en['status']); ?></span>
                                    </div>
                                    <p>Location: <?php echo htmlspecialchars($en['assigned_location'] ?? $en['preferred_location']); ?></p>
                                    <div class="progress-bar-container"><div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div></div>
                                    <div class="course-progress-footer">
                                        <?php if ($en['status'] == 'Awaiting Payment'): ?>
                                            <!-- OPTIMIZATION 3: Using data-* attributes instead of onclick -->
                                            <button class="btn btn-sm btn-pay" data-enroll-id="<?php echo $en['id']; ?>" data-course-name="<?php echo htmlspecialchars($en['course_name']); ?>">Make Payment</button>
                                        <?php elseif ($en['status'] == 'Approved' && empty($en['scheduled_slot'])): ?>
                                            <a href="schedule.php?enroll_id=<?php echo $en['id']; ?>" class="btn btn-sm schedule-btn">Schedule Class</a>
                                        <?php elseif (!empty($en['scheduled_slot'])): ?>
                                            <div class="schedule-info">Scheduled: <?php echo htmlspecialchars($en['scheduled_slot']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>You have not requested any courses yet. <a href="index.php#courses">Explore Courses</a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Tab: Learning Materials -->
                <div id="dash-materials" class="dash-tab-content">
                     <div class="dashboard-section">
                        <h2 class="section-title">My Learning Materials</h2>
                        <div id="learning-materials-list" class="dashboard-list">
                            <?php if($has_approved_course): ?>
                                <div class="material-item"><a href="materials/DhakaDrive-Traffic-Signs.pdf" download>BRTA Traffic Signs PDF</a></div>
                                <div class="material-item"><a href="materials/DhakaDrive-Vehicle-Checklist.pdf" download>Pre-Drive Vehicle Checklist</a></div>
                                <div class="material-item"><a href="materials/DhakaDrive-BRTA-Guide.pdf" download>Guide to BRTA Written Test</a></div>
                            <?php else: ?>
                                <p>Materials become available once your course enrollment is approved.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Tab: My Messages -->
                <div id="dash-support" class="dash-tab-content">
                    <div class="dashboard-section support-section">
                        <h2 class="section-title">Conversation with Admin</h2>
                        <div id="user-conversation-thread">
                            <?php if ($messages_result->num_rows > 0): ?>
                                <?php while($msg = $messages_result->fetch_assoc()): ?>
                                    <div class="message-bubble <?php echo $msg['sender_role'] === 'user' ? 'user-bubble' : 'admin-bubble'; ?>">
                                        <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                        <small><?php echo date('M d, Y h:i A', strtotime($msg['timestamp'])); ?></small>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="message-placeholder">No messages yet. Send one to start!</p>
                            <?php endif; ?>
                        </div>
                        <form id="support-form" action="actions/send_message.php" method="POST">
                            <div class="form-group">
                                <label for="support-message">Send a new message</label>
                                <textarea id="support-message" name="message" rows="3" required placeholder="Type your message..."></textarea>
                            </div>
                            <button type="submit" class="btn">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Sidebar with user settings -->
            <aside class="sidebar-content">
                <div class="dashboard-section">
                    <h2 class="section-title">Account Settings</h2>
                    <form id="profile-update-form" action="actions/update_profile.php" method="POST">
                        <div class="form-group"><label for="profile-name">Full Name</label><input type="text" name="name" id="profile-name" value="<?php echo htmlspecialchars($user['name']); ?>" required></div>
                        <div class="form-group"><label for="profile-mobile">Mobile Number</label><input type="tel" name="mobile" id="profile-mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" required></div>
                        <div class="form-group"><label for="profile-address">Address</label><textarea name="address" id="profile-address" rows="3" placeholder="Enter your full address"><?php echo htmlspecialchars($user['address']); ?></textarea></div>
                        <button type="submit" class="btn btn-sm">Update Profile</button>
                    </form>
                    <hr class="divider">
                    <form id="document-submit-form" action="actions/submit_document.php" method="POST" enctype="multipart/form-data">
                         <div class="form-group"><label for="user-document-number">NID / Passport Number</label><input type="text" name="doc_number" id="user-document-number" value="<?php echo htmlspecialchars($user['document_number'] ?? ''); ?>" placeholder="Enter document number"></div>
                        <div class="form-group"><label for="user-document-file">Upload Document (JPG, PNG, PDF)</label><input type="file" name="doc_file" id="user-document-file" accept=".jpg, .jpeg, .png, .pdf">
                            <span id="file-info" class="file-info-text"><?php echo !empty($user['document_path']) ? 'Current: <a href="'.htmlspecialchars($user['document_path']).'" target="_blank">'.htmlspecialchars($user['document_filename']).'</a>' : ''; ?></span>
                        </div>
                        <button type="submit" class="btn btn-sm">Save Document Info</button>
                    </form>
                    <hr class="divider">
                    <div class="danger-zone"><p>This will deactivate your account and cannot be undone.</p><a href="actions/deactivate_account.php" id="delete-account-btn" class="btn btn-sm btn-danger" onclick="return confirm('Are you absolutely sure? This action is irreversible.');">Deactivate My Account</a></div>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- The modals are now included from the footer for better organization -->

<script>
    // OPTIMIZATION 3: Modern, efficient, and clean JavaScript
    document.addEventListener('DOMContentLoaded', () => {
        console.log("Optimized dashboard script is running.");

        // --- 1. Main Dashboard Tabs ---
        const dashboardTabs = document.querySelectorAll('.dash-tab-link');
        dashboardTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.dash-tab-link').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.dash-tab-content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                const contentPanel = document.getElementById(tab.dataset.tab);
                if (contentPanel) contentPanel.classList.add('active');
            });
        });

        // --- 2. Payment Modal Logic ---
        const paymentModal = document.getElementById('payment-modal');
        const courseListContainer = document.getElementById('my-courses-list');

        // Event delegation for the "Make Payment" button
        if (courseListContainer && paymentModal) {
            courseListContainer.addEventListener('click', (event) => {
                if (event.target.matches('.btn-pay')) {
                    const button = event.target;
                    const courseName = button.dataset.courseName;
                    const enrollmentId = button.dataset.enrollId;
                    const prices = {"Car Driving Course": 5000, "Motorcycle Riding Course": 3000, "Scooter Riding Lessons": 2500, "Bicycle Safety Program": 1000};
                    const price = prices[courseName] || 0;

                    // Populate and show modal
                    paymentModal.querySelector('#payment-course-name').textContent = courseName;
                    paymentModal.querySelector('#payment-course-price').textContent = price.toLocaleString();
                    paymentModal.querySelectorAll('.payment-enrollment-id').forEach(input => input.value = enrollmentId);
                    
                    // Reset tabs to default
                    paymentModal.querySelectorAll('.payment-tab-content').forEach(c => c.classList.remove('active'));
                    paymentModal.querySelectorAll('.payment-tab-link').forEach(l => l.classList.remove('active'));
                    paymentModal.querySelector('#pay-mfs').classList.add('active');
                    paymentModal.querySelector('.payment-tab-link[data-tab="pay-mfs"]').classList.add('active');
                    
                    paymentModal.style.display = 'flex';
                }
            });
        }
        
        // --- 3. Payment Modal's Internal Tabs ---
        if (paymentModal) {
            const paymentTabs = paymentModal.querySelectorAll('.payment-tab-link');
            paymentTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    paymentModal.querySelectorAll('.payment-tab-link').forEach(l => l.classList.remove('active'));
                    paymentModal.querySelectorAll('.payment-tab-content').forEach(c => c.classList.remove('active'));
                    tab.classList.add('active');
                    const activeContent = paymentModal.querySelector(`#${tab.dataset.tab}`);
                    if(activeContent) activeContent.classList.add('active');
                });
            });
        }

        // --- 4. Universal Modal Close Logic ---
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', () => {
                button.closest('.modal-overlay').style.display = 'none';
            });
        });

    });
</script>

<?php 
// Include the footer which now contains the modal HTML
// This keeps the main dashboard file cleaner.
$messages_stmt->close();
$enrollments_stmt->close();
$conn->close();
include 'partials/footer.php'; 
?>