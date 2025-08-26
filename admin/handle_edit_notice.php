<?php
session_start();
// --- Security Check ---
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $notice_id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);

    // --- Update Database ---
    $sql = "UPDATE notices SET title = ?, content = ?, category_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $title, $content, $category_id, $notice_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?success=updated");
    } else {
        header("Location: edit_notice.php?id=" . $notice_id . "&error=update_failed");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: dashboard.php");
    exit();
}
?>