<?php
session_start();
// Destroy all session data to log the user out securely
session_unset();
session_destroy();

// Send them back to the login page
header("Location: ../index.php");
exit();
?>