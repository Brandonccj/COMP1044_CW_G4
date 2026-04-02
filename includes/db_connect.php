<?php
// db_connect.php
$servername = "localhost";
$username = "root"; // Default is usually 'root' for local development, but change this if you set a different username in phpMyAdmin
$password = "root"; // Default is usually blank
$dbname = "comp1044"; // Change this to the name of your database in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>