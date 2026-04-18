<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- ADD ASSESSOR ---
    if ($action === 'add') {
        $full_name = trim($_POST['full_name']);
        $username  = trim($_POST['username']);
        $password  = $_POST['password'];

        if (empty($full_name) || empty($username) || empty($password)) {
            $_SESSION['message'] = "Error: All fields are required.";
            $_SESSION['message_type'] = "error";
            header("Location: ../admin/manage_assessors.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO assessors (username, password_hash, full_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $full_name);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Assessor account for '{$full_name}' created successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = ($conn->errno == 1062)
                ? "Error: Username '{$username}' is already taken."
                : "Database Error: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    // --- UPDATE ASSESSOR ---
    if ($action === 'update') {
        $assessor_id = intval($_POST['assessor_id']);
        $full_name   = trim($_POST['full_name']);
        $password    = $_POST['password'];

        if (empty($full_name)) {
            $_SESSION['message'] = "Error: Full name cannot be empty.";
            $_SESSION['message_type'] = "error";
            header("Location: ../admin/manage_assessors.php");
            exit();
        }

        // Update name only, or name + password
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE assessors SET full_name = ?, password_hash = ? WHERE assessor_id = ?");
            $stmt->bind_param("ssi", $full_name, $hashed_password, $assessor_id);
        } else {
            $stmt = $conn->prepare("UPDATE assessors SET full_name = ? WHERE assessor_id = ?");
            $stmt->bind_param("si", $full_name, $assessor_id);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Assessor updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Database Error: " . $conn->error;
            $_SESSION['message_type'] = "error";
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
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting account: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: ../admin/manage_assessors.php");
    exit();

} else {
    header("Location: ../admin/manage_assessors.php");
    exit();
}
?>