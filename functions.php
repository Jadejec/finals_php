<?php    
// All project functions should be placed here

// Database configuration
define('DB_HOST', 'localhost'); // Usually localhost
define('DB_USER', 'your_username'); // Your database username
define('DB_PASS', 'your_password'); // Your database password
define('DB_NAME', 'dct-ccs-finals'); // Your database name

// Create connection
function dbConnect() {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    return $connection;
}

// Example of a function to close the connection
function closeConnection($connection) {
    $connection->close();
}
?>