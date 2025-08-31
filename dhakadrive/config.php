<?php
// Start the session at the very beginning
session_start();

// Database Configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'dhakadrive_db');

// Establish Connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Helper Functions ---

// Check if a user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if the logged-in user is an admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Get logged-in user's ID
function getUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

// Function to safely redirect
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Function to set a flash message
function set_message($message, $type = 'info') {
    $_SESSION['flash_message'] = ['text' => $message, 'type' => $type];
}

// Function to display a flash message
function display_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        // Use different colors for different message types (optional)
        $color = $message['type'] === 'error' ? 'red' : 'green';
        echo '<p style="color: ' . $color . '; text-align: center; margin-bottom: 1rem; font-weight: bold;">' . htmlspecialchars($message['text']) . '</p>';
        unset($_SESSION['flash_message']);
    }
}

// Course price mapping
$coursePrices = [
    "Car Driving Course" => 5000,
    "Motorcycle Riding Course" => 3000,
    "Scooter Riding Lessons" => 2500,
    "Bicycle Safety Program" => 1000
];
?>