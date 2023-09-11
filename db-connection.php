<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "avm";

// Database Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking the Connecction
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
  
?>