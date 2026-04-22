<?php
require_once 'includes/auth_check.php';

if ($_SESSION['role'] !== 'Assessor') {
    header("Location: index.php");
    exit();
}

require_once 'includes/db_connect.php';

$assessor_id = $_SESSION['user_id'];

$query = "SELECT i.internship_id, i.company_name, s.student_id, s.student_name, s.programme,
                 (SELECT COUNT(*) FROM assessments a WHERE a.internship_id = i.internship_id) AS is_graded
          FROM internships i
          JOIN students s ON i.student_id = s.student_id
          WHERE i.assessor_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $assessor_id);
$stmt->execute();
$result = $stmt->get_result();
$assigned_students = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

require_once 'includes/header.php';
?>

<div class="dashboard-container">
    <h2>Assessor Dashboard</h2>
    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Here are the students assigned to you.</p>

    <div style="margin-bottom: 16px; text-align: right;">
        <a href="assessor/view_results.php" class="action-btn" style="display:inline-block; width:auto; padding:10px 20px;">
            View My Results
        </a>
    </div>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Programme</th>
                <th>Company</th>
                <th>Status</th>
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
                            <?php if ($student['is_graded']): ?>
                                <span style="color:#27ae60; font-weight:bold;">&#10003; Graded</span>
                            <?php else: ?>
                                <span style="color:#e67e22;">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="assessor/evaluate_student.php?internship_id=<?php echo $student['internship_id']; ?>">
                                <button class="evaluate-btn">
                                    <?php echo $student['is_graded'] ? 'Update' : 'Evaluate'; ?>
                                </button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="empty-message">No students are currently assigned to you.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>