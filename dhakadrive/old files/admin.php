<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dhaka Drive</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">Dhaka Drive Admin</a>
            <nav>
                <a href="#" id="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <section id="admin-panel">
        <div class="container">
            <h1 class="admin-title">Admin Dashboard</h1>
            
            <div class="admin-tabs">
                <button class="tab-link active" data-tab="enrollments">Enrollments</button>
                <button class="tab-link" data-tab="users">User Management</button>
                <button class="tab-link" data-tab="support">Support Messages</button>
            </div>

            <div id="enrollments" class="tab-content active">
                <h2 class="section-title">All Course Enrollments</h2>
                <div class="admin-table-container">
                    <table id="enrollments-table">
                        <thead>
                            <tr>
                                <th>User Info</th>
                                <th>Course</th>
                                <th>Payment Method</th>
                                <th>TrxID/Ref</th>
                                <th>Preferred Location</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="users" class="tab-content">
                <h2 class="section-title">All Registered Users</h2>
                <div class="admin-table-container">
                    <table id="users-table">
                         <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Document Info</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <div id="support" class="tab-content">
                <h2 class="section-title">User Support Messages</h2>
                <div id="support-messages-list" class="support-list">
                    <p>No support messages found.</p>
                </div>
            </div>

        </div>
    </section>

    <div id="user-edit-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h2 class="modal-title">Edit User Information</h2>
            <form id="user-edit-form">
                <input type="hidden" id="edit-user-id">
                <div class="form-group">
                    <label for="edit-user-name">Full Name</label>
                    <input type="text" id="edit-user-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-user-mobile">Mobile Number</label>
                    <input type="tel" id="edit-user-mobile" required>
                </div>
                <div class="form-group">
                    <label for="edit-user-account-status">Account Status</label>
                    <select id="edit-user-account-status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
</body>

</html>