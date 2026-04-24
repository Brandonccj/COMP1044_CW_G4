<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

include '../includes/header.php';

$search_query = "";
$search_param = "%";
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    $search_param = "%" . $search_query . "%";
}
?>

<main class="dashboard-container">

    <a href="../admin_dashboard.php" class="back-link">← Back to Dashboard</a>

    <header class="dashboard-header">
        <h1>System Results Overview</h1>
    </header>

    <div class="management-layout">
        <section class="admin-card">
            <div class="results-header">
                <h2>All Internship Results</h2>
                <form action="view_results.php" method="GET" class="search-form">
                    <select name="filter_status" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="graded" <?php echo $filter_status === 'graded' ? 'selected' : ''; ?>>Graded</option>
                        <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Evaluation Pending</option>
                    </select>
                    <input type="text" name="search" placeholder="Search by ID or Name..." value="<?php echo htmlspecialchars($search_query); ?>">
                </form>
            </div>

            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Company</th>
                            <th>Assessor</th>
                            <th>Final Score</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $base_query = "SELECT s.student_id, s.student_name, i.company_name, a.full_name AS assessor_name,
                                              asm.final_score, asm.qualitative_comments,
                                              asm.tasks_score, asm.health_safety_score, asm.theory_score,
                                              asm.presentation_score, asm.clarity_score, asm.lifelong_learning_score,
                                              asm.project_management_score, asm.time_management_score
                                       FROM internships i
                                       JOIN students s ON i.student_id = s.student_id
                                       JOIN assessors a ON i.assessor_id = a.assessor_id
                                       LEFT JOIN assessments asm ON i.internship_id = asm.internship_id
                                       WHERE (s.student_id LIKE ? OR s.student_name LIKE ?)";

                        if ($filter_status === 'graded') {
                            $base_query .= " AND asm.final_score IS NOT NULL";
                        } elseif ($filter_status === 'pending') {
                            $base_query .= " AND asm.final_score IS NULL";
                        }

                        $base_query .= " ORDER BY s.student_id ASC";

                        $stmt = $conn->prepare($base_query);
                        $stmt->bind_param("ss", $search_param, $search_param);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $sid = htmlspecialchars($row['student_id']);
                                echo "<tr>";
                                echo "<td><strong>{$sid}</strong><br>" . htmlspecialchars($row['student_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['assessor_name']) . "</td>";

                                if ($row['final_score'] !== null) {
                                    echo "<td class='score-graded'>" . htmlspecialchars($row['final_score']) . "%</td>";
                                    echo "<td>
                                            <button class='btn-edit' onclick=\"toggleDetail('detail-{$sid}')\">View Breakdown</button>
                                          </td>";
                                    echo "</tr>";
                                    echo "<tr id='detail-{$sid}' style='display:none; background:#f8f9fa;'>
                                            <td colspan='5'>
                                                <div style='padding:12px;'>
                                                    <strong>Mark Breakdown:</strong>
                                                    <table style='width:100%; margin-top:8px; font-size:13px;'>
                                                        <tr>
                                                            <th style='text-align:left;padding:4px;'>Criteria</th>
                                                            <th style='text-align:center;padding:4px;'>Score</th>
                                                            <th style='text-align:center;padding:4px;'>Max</th>
                                                        </tr>
                                                        <tr><td style='padding:4px;'>Undertaking Tasks/Projects</td><td style='text-align:center;'>" . htmlspecialchars($row['tasks_score']) . "</td><td style='text-align:center; color:#999;'>10</td></tr>
                                                        <tr><td style='padding:4px;'>Health &amp; Safety Requirements</td><td style='text-align:center;'>" . htmlspecialchars($row['health_safety_score']) . "</td><td style='text-align:center; color:#999;'>10</td></tr>
                                                        <tr><td style='padding:4px;'>Connectivity &amp; Use of Theory</td><td style='text-align:center;'>" . htmlspecialchars($row['theory_score']) . "</td><td style='text-align:center; color:#999;'>10</td></tr>
                                                        <tr><td style='padding:4px;'>Presentation of Written Report</td><td style='text-align:center;'>" . htmlspecialchars($row['presentation_score']) . "</td><td style='text-align:center; color:#999;'>15</td></tr>
                                                        <tr><td style='padding:4px;'>Clarity of Language &amp; Illustration</td><td style='text-align:center;'>" . htmlspecialchars($row['clarity_score']) . "</td><td style='text-align:center; color:#999;'>10</td></tr>
                                                        <tr><td style='padding:4px;'>Lifelong Learning Activities</td><td style='text-align:center;'>" . htmlspecialchars($row['lifelong_learning_score']) . "</td><td style='text-align:center; color:#999;'>15</td></tr>
                                                        <tr><td style='padding:4px;'>Project Management</td><td style='text-align:center;'>" . htmlspecialchars($row['project_management_score']) . "</td><td style='text-align:center; color:#999;'>15</td></tr>
                                                        <tr><td style='padding:4px;'>Time Management</td><td style='text-align:center;'>" . htmlspecialchars($row['time_management_score']) . "</td><td style='text-align:center; color:#999;'>15</td></tr>
                                                        <tr style='border-top:2px solid #ddd;font-weight:bold;'>
                                                            <td style='padding:4px;'>Final Score</td>
                                                            <td style='text-align:center; color:#27ae60;'>" . htmlspecialchars($row['final_score']) . "%</td>
                                                            <td style='text-align:center; color:#999;'>100</td>
                                                        </tr>
                                                    </table>
                                                    <p style='margin-top:10px; color:#555; font-size:13px;'><strong>Comments:</strong> " . htmlspecialchars($row['qualitative_comments']) . "</p>
                                                </div>
                                            </td>
                                          </tr>";
                                } else {
                                    echo "<td colspan='2' class='score-pending'>Evaluation Pending</td>";
                                    echo "</tr>";
                                }
                            }
                        } else {
                            echo "<tr><td colspan='5' class='empty-message'>No results found matching your search.</td></tr>";
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<script>
function toggleDetail(id) {
    const row = document.getElementById(id);
    if (row) {
        row.style.display = (row.style.display === 'none') ? 'table-row' : 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>