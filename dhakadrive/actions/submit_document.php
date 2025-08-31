<?php
// --- 1. INITIAL SETUP ---

// Include the core configuration file.
require_once '../config.php';


// --- 2. SCRIPT EXECUTION GUARD & AUTHENTICATION ---

// This script must be accessed via POST and the user must be logged in.
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isLoggedIn()) {
    redirect('../login.php');
}


// --- 3. DATA RETRIEVAL ---

$user_id = getUserId();
// Retrieve the document number from the form.
$doc_number = isset($_POST['doc_number']) ? trim($_POST['doc_number']) : '';


// --- 4. FILE UPLOAD HANDLING ---

$upload_path = null;      // This will store the final path to the file in the database.
$upload_filename = null;  // This will store the final name of the file.

// Check if a file was actually uploaded. The error code UPLOAD_ERR_NO_FILE means the user submitted the form without choosing a file.
if (isset($_FILES['doc_file']) && $_FILES['doc_file']['error'] === UPLOAD_ERR_OK) {

    $file = $_FILES['doc_file'];

    // --- 4a. File Validation ---
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
    $max_file_size = 5 * 1024 * 1024; // 5 MB

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Check file extension
    if (!in_array($file_extension, $allowed_extensions)) {
        set_message("Invalid file type. Only JPG, PNG, and PDF files are allowed.", "error");
        redirect('../dashboard.php');
    }

    // Check file size
    if ($file['size'] > $max_file_size) {
        set_message("File is too large. The maximum allowed size is 5 MB.", "error");
        redirect('../dashboard.php');
    }

    // --- 4b. Create a Secure, Unique Filename ---
    // This is crucial to prevent users from overwriting each other's files or uploading malicious files.
    // We create a name like: user_1678886400_document.pdf
    $new_filename = 'user_' . $user_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;

    // Define the destination path.
    $destination_folder = '../uploads/'; // The physical folder on the server
    $destination_path = $destination_folder . $new_filename;

    // --- 4c. Move the Uploaded File ---
    // move_uploaded_file() safely moves the temporary file to our permanent uploads directory.
    if (move_uploaded_file($file['tmp_name'], $destination_path)) {
        // If the move is successful, we store the database-friendly path and the original filename.
        $upload_path = 'uploads/' . $new_filename; // The path to store in the DB
        $upload_filename = $file['name']; // The original filename for display purposes
    } else {
        // If the move fails, it's likely a server permissions issue.
        set_message("There was an error saving your file. Please contact support.", "error");
        redirect('../dashboard.php');
    }
}


// --- 5. DATABASE UPDATE ---

// We need to fetch the user's existing document path to avoid overwriting it if no new file was uploaded.
$stmt_fetch = $conn->prepare("SELECT document_path, document_filename FROM users WHERE id = ?");
$stmt_fetch->bind_param("i", $user_id);
$stmt_fetch->execute();
$existing_doc = $stmt_fetch->get_result()->fetch_assoc();
$stmt_fetch->close();

// Determine the final values to save. If a new file was uploaded, use the new path.
// Otherwise, keep the existing path.
$final_doc_path = $upload_path ?? $existing_doc['document_path'];
$final_doc_filename = $upload_filename ?? $existing_doc['document_filename'];

// Prepare the SQL query to update the user's document information.
$sql = "UPDATE users SET document_number = ?, document_path = ?, document_filename = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql);
$stmt_update->bind_param("sssi", $doc_number, $final_doc_path, $final_doc_filename, $user_id);


// --- 6. EXECUTE AND REDIRECT ---

if ($stmt_update->execute()) {
    set_message("Your document information has been updated successfully.", "success");
} else {
    set_message("A database error occurred while updating your information.", "error");
    // For debugging: error_log("Document update failed: " . $stmt_update->error);
}

// Cleanup
$stmt_update->close();
$conn->close();

// Redirect back to the dashboard.
redirect('../dashboard.php');
?>