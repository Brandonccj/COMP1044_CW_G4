<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- ASSIGN INTERNSHIP ---
    if ($action === 'assign') {
        $student_id = trim($_POST['student_id']);
        $company_name = trim($_POST['company_name']);
        $assessor_id = intval($_POST['assessor_id']);

        $stmt = $conn->prepare("INSERT INTO internships (student_id, assessor_id, company_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $student_id, $assessor_id, $company_name);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Internship successfully assigned!";
        } else {
            // Error 1062 means the student already has an internship (UNIQUE constraint)
            if ($conn->errno == 1062) {
                $_SESSION['message'] = "Error: This student has already been assigned an internship.";
            } else {
                $_SESSION['message'] = "Database Error: " . $conn->error;
            }
        }
        $stmt->close();
    }

    // --- REMOVE INTERNSHIP ---
    if ($action === 'delete') {
        $internship_id = intval($_POST['internship_id']);

        $stmt = $conn->prepare("DELETE FROM internships WHERE internship_id = ?");
        $stmt->bind_param("i", $internship_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Internship assignment removed.";
        } else {
            $_SESSION['message'] = "Error removing assignment.";
        }
        $stmt->close();
    }

    header("Location: ../admin/assign_internships.php");
    exit();
} else {
    header("Location: ../admin/assign_internships.php");
    exit();
}
?>