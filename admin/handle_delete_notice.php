<?php
session_start();
// --- Security Check ---
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db_connect.php';

// Check if an ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php?error=invalid_id");
    exit();
}

$notice_id = intval($_GET['id']);

// --- Important: First, find and delete any associated file ---
$stmt = $conn->prepare("SELECT attachment_path FROM notices WHERE id = ?");
$stmt->bind_param("i", $notice_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    if (!empty($row['attachment_path'])) {
        // The path in DB is 'uploads/filename.ext', we need '../uploads/filename.ext'
        $file_path = '../' . $row['attachment_path'];
        if (file_exists($file_path)) {
            unlink($file_path); // Delete the file
        }
    }
}
$stmt->close();


// --- Now, delete the notice from the database ---
$stmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
$stmt->bind_param("i", $notice_id);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=deleted");
} else {
    header("Location: dashboard.php?error=delete_failed");
}

$stmt->close();
$conn->close();
?>