<?php
session_start();

// Require the database connection
require_once 'includes/db_connect.php';

// Authentication Check 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Assessor') {
    header("Location: index.php"); 
    exit();
}

// Get the logged-in assessor's ID from the session
$assessor_id = $_SESSION['user_id'];

// The SQL JOIN Query
$query = "SELECT i.internship_id, i.company_name, s.student_id, s.student_name, s.programme 
          FROM internships i
          JOIN students s ON i.student_id = s.student_id
          WHERE i.assessor_id = :assessor_id";

// Prepare and execute securely using PDO
$stmt = $pdo->prepare($query);
$stmt->execute(['assessor_id' => $assessor_id]);
$assigned_students = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessor Dashboard</title>
    <link rel="stylesheet" href="assets/style.css"> 
</head>
<body>

    <div class="dashboard-container">
        <h2>Assessor Dashboard</h2>
        <p>Welcome! Here are the students assigned to you for assessment.</p>

        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Programme</th>
                    <th>Company Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($assigned_students) > 0): ?>
                    <?php foreach ($assigned_students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['programme']); ?></td>
                            <td><?php echo htmlspecialchars($student['company_name']); ?></td>
                            <td>
                                <a href="assessor/evaluate_student.php?internship_id=<?php echo $student['internship_id']; ?>">
                                    <button class="evaluate-btn">Evaluate</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-message">No students are currently assigned to you.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>