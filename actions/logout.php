<?php
session_start();

// Unset all secure session variables
$_SESSION = array();

// Destroy the session completely
session_destroy();

// Start a fresh, empty session just to send the Toast message
session_start();
$_SESSION['message'] = "You have successfully logged out.";
$_SESSION['message_type'] = "success";

// Send them back to the login page
header("Location: ../index.php");
exit();
?>