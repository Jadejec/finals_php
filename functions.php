<?php
session_start();
$email = "";
$password = "";
$errors = array();

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

//Login user
if (isset($_POST['login_user'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Initialize an array for error messages
    $errors = [];

    if (empty($email)) {
        array_push($errors, "Email is required");
    }

    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        // Hash the password
        $password = md5($password);
        $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $results = mysqli_query($conn, $query);

        // Use '==' for comparison
        if (mysqli_num_rows($results) == 1) {
            $_SESSION['email'] = $email;
            $_SESSION['success'] = "You are now logged in";
            header('location: ./admin/dashboard.php');

        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}
?>
