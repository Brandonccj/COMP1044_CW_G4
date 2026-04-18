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

    // --- ADD STUDENT ---
    if ($action === 'add') {
        $student_id   = trim($_POST['student_id']);
        $student_name = trim($_POST['student_name']);
        $programme    = trim($_POST['programme']);

        // Basic server-side validation
        if (empty($student_id) || empty($student_name) || empty($programme)) {
            $_SESSION['message'] = "Error: All fields are required.";
            $_SESSION['message_type'] = "error";
            header("Location: ../admin/manage_students.php");
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, programme) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $student_id, $student_name, $programme);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student '{$student_name}' added successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            if ($conn->errno == 1062) {
                $_SESSION['message'] = "Error: Student ID '{$student_id}' already exists.";
            } else {
                $_SESSION['message'] = "Database Error: " . $conn->error;
            }
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    // --- UPDATE STUDENT ---
    if ($action === 'update') {
        $student_id   = trim($_POST['student_id']);
        $student_name = trim($_POST['student_name']);
        $programme    = trim($_POST['programme']);

        if (empty($student_name) || empty($programme)) {
            $_SESSION['message'] = "Error: Name and programme cannot be empty.";
            $_SESSION['message_type'] = "error";
            header("Location: ../admin/manage_students.php");
            exit();
        }

        $stmt = $conn->prepare("UPDATE students SET student_name = ?, programme = ? WHERE student_id = ?");
        $stmt->bind_param("sss", $student_name, $programme, $student_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student '{$student_id}' updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Database Error: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    // --- DELETE STUDENT ---
    if ($action === 'delete') {
        $student_id = trim($_POST['student_id']);

        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student '{$student_id}' deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting student: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: ../admin/manage_students.php");
    exit();

} else {
    header("Location: ../admin/manage_students.php");
    exit();
}
?>