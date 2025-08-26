<?php
// --- Database Connection --- //

// Database credentials
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "notice_board_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>