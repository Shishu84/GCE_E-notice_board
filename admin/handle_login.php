<?php
session_start();
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a statement to find the user
    $sql = "SELECT id, username, full_name, password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $admin['password'])) {
            // Password is correct, start the session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];

            // Redirect to the admin dashboard
            header("Location: dashboard.php");
            exit();
        }
    }

    // If login fails, redirect back to login page with an error
    header("Location: login.php?error=1");
    exit();

} else {
    // If someone tries to access this file directly, redirect them
    header("Location: login.php");
    exit();
}
?>