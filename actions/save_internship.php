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
        $student_id   = trim($_POST['student_id']);
        $company_name = trim($_POST['company_name']);
        $assessor_id  = intval($_POST['assessor_id']);

        // Pre-check: student already assigned?
        $check = $conn->prepare("SELECT internship_id FROM internships WHERE student_id = ?");
        $check->bind_param("s", $student_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['message'] = "Error: This student already has an internship assigned.";
            $_SESSION['message_type'] = "error";
            $check->close();
            header("Location: ../admin/assign_internships.php");
            exit();
        }
        $check->close();

        $stmt = $conn->prepare("INSERT INTO internships (student_id, assessor_id, company_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $student_id, $assessor_id, $company_name);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Internship successfully assigned!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Database Error: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    // --- UPDATE INTERNSHIP ---
    if ($action === 'update') {
        $internship_id = intval($_POST['internship_id']);
        $company_name  = trim($_POST['company_name']);
        $assessor_id   = intval($_POST['assessor_id']);

        if (empty($company_name)) {
            $_SESSION['message'] = "Error: Company name cannot be empty.";
            $_SESSION['message_type'] = "error";
            header("Location: ../admin/assign_internships.php");
            exit();
        }

        $stmt = $conn->prepare("UPDATE internships SET company_name = ?, assessor_id = ? WHERE internship_id = ?");
        $stmt->bind_param("sii", $company_name, $assessor_id, $internship_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Internship updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Database Error: " . $conn->error;
            $_SESSION['message_type'] = "error";
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
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error removing assignment: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: ../admin/assign_internships.php");
    exit();

} else {
    header("Location: ../admin/assign_internships.php");
    exit();
}
?>