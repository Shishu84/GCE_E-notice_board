<?php
session_start();
// --- Security Check ---
// If the admin is not logged in, redirect them to the login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include '../includes/db_connect.php';

// Fetch all categories for the dropdown
$category_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$category_result = $conn->query($category_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: sans-serif; }
        .container { padding: 20px; }
        .notice-form { margin-bottom: 30px; border: 1px solid #ccc; padding: 15px; }
        .notice-list { border: 1px solid #ccc; padding: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>! | <a href="logout.php">Logout</a></p>

        <hr>

        <div class="notice-form">
            <h3>Add New Notice</h3>
            <?php
            if (isset($_GET['success'])) {
                echo '<p style="color:green;">Notice added successfully!</p>';
            }
            if (isset($_GET['error'])) {
                echo '<p style="color:red;">Failed to add notice. Please try again.</p>';
            }
            ?>
            <form action="handle_add_notice.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="title">Notice Title:</label><br>
                    <input type="text" id="title" name="title" required style="width: 100%;">
                </div>
                <br>
                <div>
                    <label for="content">Content:</label><br>
                    <textarea id="content" name="content" rows="6" required style="width: 100%;"></textarea>
                </div>
                <br>
                <div>
                    <label for="category">Category:</label><br>
                    <select id="category" name="category_id" required>
                        <option value="">-- Select a Category --</option>
                        <?php
                        // Dynamically populate categories from the database
                        if ($category_result->num_rows > 0) {
                            while($row = $category_result->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <br>
                <div>
                    <label for="attachment">Attachment (PDF, Image, etc.):</label><br>
                    <input type="file" id="attachment" name="attachment">
                </div>
                <br>
                <button type="submit">Post Notice</button>
            </form>
        </div>

        <div class="notice-list">
    <h3>Existing Notices</h3>
    
    <?php
    // Fetch all existing notices to display them
    $notice_sql = "SELECT id, title, created_at FROM notices ORDER BY created_at DESC";
    $notice_result = $conn->query($notice_sql);

    if ($notice_result->num_rows > 0) {
        while($row = $notice_result->fetch_assoc()) {
    ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee;">
                <span><?php echo htmlspecialchars($row['title']); ?></span>
                <div>
                    <a href="edit_notice.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; margin-right: 10px;">✏️ Edit</a>
                    <a href="handle_delete_notice.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this notice?');" style="text-decoration: none; color: red;">🗑️ Delete</a>
                </div>
            </div>
    <?php
        }
    } else {
        echo "<p>No notices found.</p>";
    }
    ?>
</div>
    </div>
</body>
</html>