<?php
// This script securely hashes the password using PHP's built-in password_hash() before saving it to the database, which matches how Member 1's login script verifies passwords password_verify()

require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

// Security check
if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- ADD ASSESSOR ---
    if ($action === 'add') {
        $full_name = trim($_POST['full_name']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Securely hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO assessors (username, password_hash, full_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $full_name);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Assessor account created successfully!";
        } else {
            // Error 1062 is MySQL's duplicate entry error (e.g., username already taken)
            if ($conn->errno == 1062) {
                $_SESSION['message'] = "Error: That username is already taken.";
            } else {
                $_SESSION['message'] = "Database Error: " . $conn->error;
            }
        }
        $stmt->close();
    }

    // --- DELETE ASSESSOR ---
    if ($action === 'delete') {
        $assessor_id = intval($_POST['assessor_id']);

        $stmt = $conn->prepare("DELETE FROM assessors WHERE assessor_id = ?");
        $stmt->bind_param("i", $assessor_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Assessor account deleted.";
        } else {
            $_SESSION['message'] = "Error deleting account.";
        }
        $stmt->close();
    }

    header("Location: ../admin/manage_assessors.php");
    exit();
} else {
    header("Location: ../admin/manage_assessors.php");
    exit();
}
?>