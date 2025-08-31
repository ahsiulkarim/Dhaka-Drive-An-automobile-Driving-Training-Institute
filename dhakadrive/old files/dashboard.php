<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Dhaka Drive</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">Dhaka Drive</a>
            <nav id="main-nav">
                
            </nav>
        </div>
    </header>
    <section id="dashboard">
        <div class="container">
            <h1 id="welcome-message">Welcome, User!</h1>
            <div class="dashboard-grid">
                <div class="main-content">
                    <div class="dashboard-section"><h2 class="section-title">My Courses</h2><div id="my-courses-list" class="dashboard-list"><p>You have not requested any courses yet. <a href="index.php#courses">Explore Courses</a></p></div></div>
                    
                    <div class="dashboard-section">
                        <h2 class="section-title">My Notifications</h2>
                        <div id="notification-list" class="dashboard-list">
                            <p>No new notifications.</p>
                        </div>
                    </div>

                    <div class="dashboard-section support-section"><h2 class="section-title">Contact Admin / Support</h2><form id="support-form"><div class="form-group"><label for="support-message">Your Message</label><textarea id="support-message" rows="4" required placeholder="Describe your issue or question..."></textarea></div><button type="submit" class="btn">Send Message</button></form></div>
                </div>
                <aside class="sidebar-content">
                    <div class="dashboard-section">
                        <h2 class="section-title">Account Settings</h2>
                        <form id="profile-update-form">
                            <div class="form-group"><label for="profile-name">Full Name</label><input type="text" id="profile-name" required></div>
                            <div class="form-group"><label for="profile-mobile">Mobile Number</label><input type="tel" id="profile-mobile" required></div>
                            <button type="submit" class="btn btn-sm">Update Profile</button>
                        </form>
                        <hr class="divider">
                        <form id="document-submit-form">
                             <div class="form-group"><label for="user-document-number">NID / Passport Number</label><input type="text" id="user-document-number" placeholder="Enter document number"></div>
                            <div class="form-group"><label for="user-document-file">Upload Document (JPG, PNG, PDF)</label><input type="file" id="user-document-file" accept=".jpg, .jpeg, .png, .pdf"><span id="file-info" class="file-info-text"></span></div>
                            <button type="submit" class="btn btn-sm">Save Document Info</button>
                        </form>
                        <hr class="divider">
                        <div class="danger-zone"><p>This will deactivate your account. You will not be able to log in again.</p><button id="delete-account-btn" class="btn btn-sm btn-danger">Deactivate My Account</button></div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    <div id="payment-modal" class="modal-overlay"></div>
    <script src="app.js"></script>
</body>
</html>