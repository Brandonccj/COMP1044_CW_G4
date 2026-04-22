<?php
// This file will be included at the very top of protected pages
session_start();

// If they are not logged in, kick them back to the login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    
    // Dynamically fix the path depending on which subfolder the page is in
    $is_subfolder = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/assessor/') !== false);
    $redirect_path = $is_subfolder ? '../index.php' : 'index.php';
    
    header("Location: " . $redirect_path);
    exit();
}
?>