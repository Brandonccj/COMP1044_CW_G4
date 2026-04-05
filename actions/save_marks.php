<?php
session_start();
require_once '../includes/db_connect.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Assessor') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Capture Data
    $internship_id = intval($_POST['internship_id']);
    
    // Catch the student name passed from the hidden input field
    $student_name = trim($_POST['student_name']);
    
    // Ensure inputs are treated as floats for accurate decimal math
    $tasks = floatval($_POST['tasks_score']);
    $health = floatval($_POST['health_safety_score']);
    $theory = floatval($_POST['theory_score']);
    $presentation = floatval($_POST['presentation_score']);
    $clarity = floatval($_POST['clarity_score']);
    $lifelong = floatval($_POST['lifelong_learning_score']);
    $project = floatval($_POST['project_management_score']);
    $time = floatval($_POST['time_management_score']);
    
    // Sanitize text input
    $comments = trim($_POST['qualitative_comments']);

    // 2. Execute Math (Since inputs are already strictly bounded out of 10 or 15, we just add them up)
    $final_score = $tasks + $health + $theory + $presentation + $clarity + $lifelong + $project + $time;

    // Round to 2 decimal places for clean storage
    $final_score = round($final_score, 2);

    // 3. Save to Database
    $query = "INSERT INTO assessments (
                internship_id, tasks_score, health_safety_score, theory_score, 
                presentation_score, clarity_score, lifelong_learning_score, 
                project_management_score, time_management_score, qualitative_comments, final_score
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE 
                tasks_score = VALUES(tasks_score),
                health_safety_score = VALUES(health_safety_score),
                theory_score = VALUES(theory_score),
                presentation_score = VALUES(presentation_score),
                clarity_score = VALUES(clarity_score),
                lifelong_learning_score = VALUES(lifelong_learning_score),
                project_management_score = VALUES(project_management_score),
                time_management_score = VALUES(time_management_score),
                qualitative_comments = VALUES(qualitative_comments),
                final_score = VALUES(final_score)";

    $stmt = $conn->prepare($query);
    
    $stmt->bind_param(
        "iddddddddsd", 
        $internship_id, $tasks, $health, $theory, $presentation, 
        $clarity, $lifelong, $project, $time, $comments, $final_score
    );

    if ($stmt->execute()) {
        // Updated success message that includes the student's name
        $_SESSION['message'] = "Success! Assessment for " . htmlspecialchars($student_name) . " saved. Final Score: " . $final_score . "%";
    } else {
        $_SESSION['message'] = "Database Error: " . $conn->error;
    }

    $stmt->close();
    
    // Redirect back to dashboard
    header("Location: ../assessor_dashboard.php");
    exit();
} else {
    // If accessed directly without submitting the form
    header("Location: ../assessor_dashboard.php");
    exit();
}
?>