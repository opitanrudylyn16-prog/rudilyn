<?php
// Database Configuration

$servername = "localhost";
$username = "root";
$password = ""; // Leave empty for default XAMPP setup
$database = "employee_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Define base URL
define('BASE_URL', 'http://localhost/rudilyn/');

?>