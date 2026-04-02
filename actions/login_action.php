<?php
// Start the session to remember the user across pages
session_start();

// Connect to the database
require_once '../includes/db_connect.php';

// Check if the form was actually submitted
if (isset($_POST['login_submit'])) {
    
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Figure out which table to check
    $table = ($role === 'Admin') ? 'Admins' : 'Assessors';

    // 1. Prepare the SQL statement (Prevents SQL Injection!)
    $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // 2. Check if the user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 3. Verify the password
        if (password_verify($password, $user['password_hash'])) {
            
            // Success! Store user info in session
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $role;
            // Grab the correct ID based on role
            $_SESSION['user_id'] = ($role === 'Admin') ? $user['admin_id'] : $user['assessor_id'];
            
            // Redirect to the correct dashboard
            if ($role === 'Admin') {
                header("Location: ../admin_dashboard.php");
            } else {
                header("Location: ../assessor_dashboard.php");
            }
            exit();
        } else {
            // Wrong password popup and redirect back
            echo "<script>alert('Login Failed: Incorrect password.'); window.location.href='../index.php';</script>";
        }
    } else {
        // User not found popup and redirect back
        echo "<script>alert('Login Failed: User not found.'); window.location.href='../index.php';</script>";
    }
    $stmt->close();
}
$conn->close();
?>