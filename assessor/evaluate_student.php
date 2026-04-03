<?php
session_start();
require_once '../includes/db_connect.php';

// Security: Only Assessors allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Assessor') {
    header("Location: ../index.php");
    exit();
}

// Ensure an internship ID was passed in the URL
if (!isset($_GET['internship_id'])) {
    header("Location: ../assessor_dashboard.php");
    exit();
}

$internship_id = intval($_GET['internship_id']);
$assessor_id = $_SESSION['user_id'];

// Fetch the student's details to display on the form
$query = "SELECT s.student_name, s.student_id, i.company_name 
          FROM internships i 
          JOIN students s ON i.student_id = s.student_id 
          WHERE i.internship_id = ? AND i.assessor_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $internship_id, $assessor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../assessor_dashboard.php");
    exit();
}
$student_data = $result->fetch_assoc();
$stmt->close();

include '../includes/header.php';
?>

<main class="dashboard-container">
    <header class="page-header">
        <h1>Evaluate Student</h1>
        <a href="../assessor_dashboard.php" class="back-link">← Back to Dashboard</a>
    </header>

    <div class="evaluation-header">
        <p><strong>Student:</strong> <?php echo htmlspecialchars($student_data['student_name']) . " (" . htmlspecialchars($student_data['student_id']) . ")"; ?></p>
        <p><strong>Company:</strong> <?php echo htmlspecialchars($student_data['company_name']); ?></p>
    </div>

    <form action="../actions/save_marks.php" method="POST" id="evaluationForm" class="admin-card">
        <input type="hidden" name="internship_id" value="<?php echo $internship_id; ?>">
        <input type="hidden" name="internship_id" value="<?php echo $internship_id; ?>">
        <input type="hidden" name="student_name" value="<?php echo htmlspecialchars($student_data['student_name']); ?>">

        <h3>Assessment Criteria</h3>
        <p class="form-subtitle">Enter the marks based on the specific weightage for each category.</p>

        <div class="form-grid">
            <div class="form-group">
                <label for="tasks_score">Undertaking Tasks/Projects (Max 10):</label>
                <input type="number" name="tasks_score" id="tasks_score" min="0" max="10" step="0.1" oninput="if(value>10)value=10;if(value<0)value=0;" required>
            </div>
            
            <div class="form-group">
                <label for="health_safety_score">Health & Safety Requirements (Max 10):</label>
                <input type="number" name="health_safety_score" id="health_safety_score" min="0" max="10" step="0.1" oninput="if(value>10)value=10;if(value<0)value=0;" required>
            </div>

            <div class="form-group">
                <label for="theory_score">Connectivity & Use of Theory (Max 10):</label>
                <input type="number" name="theory_score" id="theory_score" min="0" max="10" step="0.1" oninput="if(value>10)value=10;if(value<0)value=0;" required>
            </div>

            <div class="form-group">
                <label for="presentation_score">Presentation of Written Report (Max 15):</label>
                <input type="number" name="presentation_score" id="presentation_score" min="0" max="15" step="0.1" oninput="if(value>15)value=15;if(value<0)value=0;" required>
            </div>

            <div class="form-group">
                <label for="clarity_score">Clarity of Language & Illustration (Max 10):</label>
                <input type="number" name="clarity_score" id="clarity_score" min="0" max="10" step="0.1" oninput="if(value>10)value=10;if(value<0)value=0;" required>
            </div>

            <div class="form-group">
                <label for="lifelong_learning_score">Lifelong Learning Activities (Max 15):</label>
                <input type="number" name="lifelong_learning_score" id="lifelong_learning_score" min="0" max="15" step="0.1" oninput="if(value>15)value=15;if(value<0)value=0;" required>
            </div>

            <div class="form-group">
                <label for="project_management_score">Project Management (Max 15):</label>
                <input type="number" name="project_management_score" id="project_management_score" min="0" max="15" step="0.1" oninput="if(value>15)value=15;if(value<0)value=0;" required>
            </div>

            <div class="form-group">
                <label for="time_management_score">Time Management (Max 15):</label>
                <input type="number" name="time_management_score" id="time_management_score" min="0" max="15" step="0.1" oninput="if(value>15)value=15;if(value<0)value=0;" required>
            </div>
        </div>

        <div class="form-group comment-section">
            <label for="qualitative_comments">Qualitative Comments & Feedback:</label>
            <textarea name="qualitative_comments" id="qualitative_comments" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn-submit">Submit Evaluation</button>
    </form>
</main>

<script src="../assets/script.js"></script>

<?php include '../includes/footer.php'; ?>