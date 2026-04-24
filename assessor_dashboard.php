<?php
require_once 'includes/auth_check.php';

if ($_SESSION['role'] !== 'Assessor') {
    header("Location: index.php");
    exit();
}

require_once 'includes/db_connect.php';

$assessor_id = $_SESSION['user_id'];

$search_query = "";
$search_param = "%";
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    $search_param = "%" . $search_query . "%";
}

$base_query = "SELECT i.internship_id, i.company_name, s.student_id, s.student_name, s.programme,
                      (SELECT COUNT(*) FROM assessments a WHERE a.internship_id = i.internship_id) AS is_graded
               FROM internships i
               JOIN students s ON i.student_id = s.student_id
               WHERE i.assessor_id = ?
               AND (s.student_id LIKE ? OR s.student_name LIKE ?)";

if ($filter_status === 'graded') {
    $base_query .= " AND (SELECT COUNT(*) FROM assessments a WHERE a.internship_id = i.internship_id) > 0";
} elseif ($filter_status === 'pending') {
    $base_query .= " AND (SELECT COUNT(*) FROM assessments a WHERE a.internship_id = i.internship_id) = 0";
}

$base_query .= " ORDER BY s.student_id ASC";

$stmt = $conn->prepare($base_query);
$stmt->bind_param("iss", $assessor_id, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$assigned_students = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

require_once 'includes/header.php';
?>

<main class="dashboard-container">
    <header class="dashboard-header">
        <h1>Assessor Dashboard</h1>
        <p>Manage and review assigned student evaluations.</p>
    </header>

    <div class="management-layout">
        <section class="admin-card">
            
            <div class="results-header">
                <form action="assessor_dashboard.php" method="GET" class="search-form">
                    <select name="filter_status" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="graded" <?php echo $filter_status === 'graded' ? 'selected' : ''; ?>>Graded</option>
                        <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    </select>
                    <input type="text" name="search" placeholder="Search by ID or Name..." value="<?php echo htmlspecialchars($search_query); ?>">
                </form>
                <a href="assessor/view_results.php" class="btn-success" style="background-color: var(--accent);color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: 500;">
                    View My Results →
                </a>
            </div>

            <div class="table-responsive">
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
                                    <td><strong><?php echo htmlspecialchars($student['student_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['programme']); ?></td>
                                    <td><?php echo htmlspecialchars($student['company_name']); ?></td>
                                    <td>
                                        <?php if ($student['is_graded']): ?>
                                            <span class="score-graded" style="padding: 4px 8px; border-radius: 4px; display: inline-block;">&#10003; Graded</span>
                                        <?php else: ?>
                                            <span class="score-pending" style="padding: 4px 8px; border-radius: 4px; display: inline-block;">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="assessor/evaluate_student.php?internship_id=<?php echo $student['internship_id']; ?>">
                                            <button class="btn-edit" style="width: 100%;">
                                                <?php echo $student['is_graded'] ? 'Edit Evaluation' : 'Evaluate'; ?>
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-message">No students found matching your search.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>