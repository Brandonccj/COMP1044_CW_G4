<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Assessor') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['internship_id'])) {
    header("Location: ../assessor_dashboard.php");
    exit();
}

$internship_id = intval($_GET['internship_id']);
$assessor_id   = $_SESSION['user_id'];

// Fetch student details AND any existing assessment (so we can pre-fill if re-evaluating)
$query = "SELECT s.student_name, s.student_id, i.company_name,
                 a.tasks_score, a.health_safety_score, a.theory_score,
                 a.presentation_score, a.clarity_score, a.lifelong_learning_score,
                 a.project_management_score, a.time_management_score,
                 a.qualitative_comments, a.final_score
          FROM internships i
          JOIN students s ON i.student_id = s.student_id
          LEFT JOIN assessments a ON i.internship_id = a.internship_id
          WHERE i.internship_id = ? AND i.assessor_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $internship_id, $assessor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Not this assessor's student
    header("Location: ../assessor_dashboard.php");
    exit();
}

$data = $result->fetch_assoc();
$stmt->close();

include '../includes/header.php';
?>

<main class="dashboard-container">
    <header class="page-header">
        <h1>Evaluate Student</h1>
        <a href="../assessor_dashboard.php" class="back-link">← Back to Dashboard</a>
    </header>

    <div class="evaluation-header">
        <p><strong>Student:</strong> <?php echo htmlspecialchars($data['student_name']) . " (" . htmlspecialchars($data['student_id']) . ")"; ?></p>
        <p><strong>Company:</strong> <?php echo htmlspecialchars($data['company_name']); ?></p>
        <?php if ($data['final_score'] !== null): ?>
            <p style="color:#27ae60; font-weight:bold;">&#10003; Previously graded — score: <?php echo htmlspecialchars($data['final_score']); ?>%. You can update below.</p>
        <?php endif; ?>
    </div>

    <form action="../actions/save_marks.php" method="POST" id="evaluationForm" class="admin-card">
        <!-- Single internship_id field (duplicate removed) -->
        <input type="hidden" name="internship_id" value="<?php echo $internship_id; ?>">
        <input type="hidden" name="student_name" value="<?php echo htmlspecialchars($data['student_name']); ?>">

        <h3>Assessment Criteria</h3>
        <p class="form-subtitle">Enter marks within each category's allowed maximum.</p>

        <div class="form-grid">
            <div class="form-group">
                <label for="tasks_score">Undertaking Tasks/Projects <span class="max-label">(Max 10)</span>:</label>
                <input type="number" name="tasks_score" id="tasks_score" min="0" max="10" step="0.1" required
                       value="<?php echo $data['tasks_score'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="health_safety_score">Health &amp; Safety Requirements <span class="max-label">(Max 10)</span>:</label>
                <input type="number" name="health_safety_score" id="health_safety_score" min="0" max="10" step="0.1" required
                       value="<?php echo $data['health_safety_score'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="theory_score">Connectivity &amp; Use of Theory <span class="max-label">(Max 10)</span>:</label>
                <input type="number" name="theory_score" id="theory_score" min="0" max="10" step="0.1" required
                       value="<?php echo $data['theory_score'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="presentation_score">Presentation of Written Report <span class="max-label">(Max 15)</span>:</label>
                <input type="number" name="presentation_score" id="presentation_score" min="0" max="15" step="0.1" required
                       value="<?php echo $data['presentation_score'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="clarity_score">Clarity of Language &amp; Illustration <span class="max-label">(Max 10)</span>:</label>
                <input type="number" name="clarity_score" id="clarity_score" min="0" max="10" step="0.1" required
                       value="<?php echo $data['clarity_score'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="lifelong_learning_score">Lifelong Learning Activities <span class="max-label">(Max 15)</span>:</label>
                <input type="number" name="lifelong_learning_score" id="lifelong_learning_score" min="0" max="15" step="0.1" required
                       value="<?php echo $data['lifelong_learning_score'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="project_management_score">Project Management <span class="max-label">(Max 15)</span>:</label>
                <input type="number" name="project_management_score" id="project_management_score" min="0" max="15" step="0.1" required
                       value="<?php echo $data['project_management_score'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="time_management_score">Time Management <span class="max-label">(Max 15)</span>:</label>
                <input type="number" name="time_management_score" id="time_management_score" min="0" max="15" step="0.1" required
                       value="<?php echo $data['time_management_score'] ?? ''; ?>">
            </div>
        </div>

        <!-- Live score preview -->
        <div style="background:#f0f9f4; border:1px solid #c3e6cb; border-radius:6px; padding:12px; margin-bottom:20px; text-align:center;">
            <span style="color:#555;">Estimated Final Score: </span>
            <strong id="liveScore" style="font-size:1.3em; color:#27ae60;">0.00%</strong>
            <span style="color:#999; font-size:0.85em;"> (out of 100)</span>
        </div>

        <div class="form-group comment-section">
            <label for="qualitative_comments">Qualitative Comments &amp; Feedback:</label>
            <textarea name="qualitative_comments" id="qualitative_comments" rows="4" required><?php echo htmlspecialchars($data['qualitative_comments'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn-submit">Submit Evaluation</button>
    </form>
</main>

<script>
// Fix: validate against each field's individual max, not a blanket 100
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('evaluationForm');
    const scoreInputs = form.querySelectorAll('input[type="number"]');
    const liveScore   = document.getElementById('liveScore');

    function calcTotal() {
        let total = 0;
        scoreInputs.forEach(function (input) {
            const val = parseFloat(input.value) || 0;
            total += val;
        });
        liveScore.textContent = total.toFixed(2) + '%';
    }

    scoreInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            const max = parseFloat(this.getAttribute('max'));
            const val = parseFloat(this.value);
            if (!isNaN(val)) {
                if (val > max) this.value = max;
                if (val < 0)   this.value = 0;
            }
            calcTotal();
        });
    });

    // Init live score if re-evaluating (pre-filled values)
    calcTotal();

    form.addEventListener('submit', function (event) {
        let isValid = true;
        scoreInputs.forEach(function (input) {
            const max = parseFloat(input.getAttribute('max'));
            const val = parseFloat(input.value);
            if (isNaN(val) || val < 0 || val > max) {
                isValid = false;
                input.style.borderColor = 'red';
            } else {
                input.style.borderColor = '#ccc';
            }
        });
        if (!isValid) {
            event.preventDefault();
            alert('Validation Error: Check that all scores are within their allowed maximum.');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>