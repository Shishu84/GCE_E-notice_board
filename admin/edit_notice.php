<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db_connect.php';

// Check for notice ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Notice ID.");
}

$notice_id = intval($_GET['id']);

// Fetch the notice details
$stmt = $conn->prepare("SELECT * FROM notices WHERE id = ?");
$stmt->bind_param("i", $notice_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Notice not found.");
}
$notice = $result->fetch_assoc();
$stmt->close();

// Fetch all categories for the dropdown
$category_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Notice</title>
</head>
<body>
    <h2>Edit Notice</h2>
    <form action="handle_edit_notice.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $notice['id']; ?>">

        <div>
            <label for="title">Notice Title:</label><br>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($notice['title']); ?>" required style="width: 50%;">
        </div>
        <br>
        <div>
            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="6" required style="width: 50%;"><?php echo htmlspecialchars($notice['content']); ?></textarea>
        </div>
        <br>
        <div>
            <label for="category">Category:</label><br>
            <select id="category" name="category_id" required>
                <?php
                while($row = $category_result->fetch_assoc()) {
                    // If the category ID matches the notice's category, mark it as selected
                    $selected = ($row['id'] == $notice['category_id']) ? 'selected' : '';
                    echo '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                }
                ?>
            </select>
        </div>
        <br>
        <p>Current Attachment: 
            <?php 
            if(!empty($notice['attachment_path'])) {
                echo '<a href="../' . $notice['attachment_path'] . '" target="_blank">' . basename($notice['attachment_path']) . '</a>';
            } else {
                echo 'None';
            }
            ?>
        </p>
        <small>Note: File attachments cannot be edited. To change the attachment, please delete this notice and create a new one.</small>
        <br><br>
        <button type="submit">Update Notice</button>
        <a href="dashboard.php">Cancel</a>
    </form>
</body>
</html>