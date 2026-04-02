<?php
// This file will be included at the very top of protected pages
session_start();

// If they are not logged in, kick them back to the index page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
?>