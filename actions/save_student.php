<?php
session_start();
// This file doesn't have a user interface.
// It acts as a traffic controller, 
// catching the form submissions via POST, 
// executing the appropriate SQL commands (INSERT or DELETE), 
// and then redirecting the user back to the management page.

// Check if user is logged in as Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Check if a form was actually submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];

    // --- ADD STUDENT LOGIC ---
    if ($action === 'add') {
        // Sanitize inputs
        $student_id = trim($_POST['student_id']);
        $student_name = trim($_POST['student_name']);
        $programme = trim($_POST['programme']);

        // Mock success for UI testing
        $_SESSION['message'] = "Test Mode: Student $student_name would have been added.";
    }

    // --- DELETE STUDENT LOGIC ---
    if ($action === 'delete') {
        $student_id = trim($_POST['student_id']);
        
        // Mock success for UI testing
        $_SESSION['message'] = "Test Mode: Student ID $student_id would have been deleted.";
    }

    // Redirect back to the UI page
    header("Location: ../admin/manage_students.php");
    exit();
} else {
    // If someone tries to visit this file directly without submitting a form
    header("Location: ../admin/manage_students.php");
    exit();
}
?>