<?php
session_start();
// If admin is already logged in, redirect to the dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-g">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GCE E-Notice Board</title>
    </head>
<body>
    <h2>Admin Login</h2>

    <?php
    // Show an error message if login fails
    if (isset($_GET['error'])) {
        echo '<p style="color:red;">Invalid username or password!</p>';
    }
    ?>

    <form action="handle_login.php" method="POST">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <br>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>