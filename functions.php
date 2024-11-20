<?php
$servername = "localhost"; // Default server for local development
$username = "root";       // Default username for phpMyAdmin
$password = "";           // Default password (leave empty for XAMPP/WAMP)
$dbname = "dct-ccs-finals"; // Replace with your database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connected successfully!";
?>
