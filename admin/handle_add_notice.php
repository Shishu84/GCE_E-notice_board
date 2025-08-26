<?php
session_start();
// --- Security Check ---
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize it
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);
    $admin_id = $_SESSION['admin_id'];
    $attachment_path = NULL;

    // --- Handle File Upload ---
    // Check if a file was uploaded without errors
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $upload_dir = '../uploads/';
        // Create a unique filename to prevent overwriting
        $file_name = time() . '_' . basename($_FILES['attachment']['name']);
        $target_file = $upload_dir . $file_name;

        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $attachment_path = 'uploads/' . $file_name; // Store relative path
        } else {
            // Handle file upload error
            header("Location: dashboard.php?error=fileupload");
            exit();
        }
    }

    // --- Insert into Database ---
    $sql = "INSERT INTO notices (title, content, category_id, admin_id, attachment_path) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // 'ssiis' means String, String, Integer, Integer, String
    $stmt->bind_param("ssiis", $title, $content, $category_id, $admin_id, $attachment_path);

    if ($stmt->execute()) {
        // Success: Redirect back to dashboard with a success message
        header("Location: dashboard.php?success=1");
    } else {
        // Error: Redirect back with an error message
        header("Location: dashboard.php?error=dberror");
    }

    $stmt->close();
    $conn->close();
} else {
    // If accessed directly, redirect to dashboard
    header("Location: dashboard.php");
    exit();
}
?>