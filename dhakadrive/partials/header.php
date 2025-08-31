<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dhaka Drive'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">Dhaka Drive</a>
            <nav id="main-nav">
                <a href="index.php#courses">Courses</a>
                <a href="team.php">Our Team</a>
                <a href="index.php#faq">FAQ</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Admin Panel</a>
                    <?php else: 
                        $unread_count = 0;
                        if (isset($_SESSION['user_id'])) {
                            $stmt_nav = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
                            $stmt_nav->bind_param("i", $_SESSION['user_id']);
                            $stmt_nav->execute();
                            $result_nav = $stmt_nav->get_result()->fetch_assoc();
                            $unread_count = $result_nav['count'];
                            $stmt_nav->close();
                        }
                    ?>
                        <a href="dashboard.php">Dashboard</a>
                        <!-- THE FIX IS HERE: The link now points directly to notifications.php -->
                        <a href="notifications.php" id="notification-btn">Notifications
                            <?php if ($unread_count > 0): ?>
                                <span class="notif-count"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <a href="actions/logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php" class="btn btn-nav">Sign Up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>