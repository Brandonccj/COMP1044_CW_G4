<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php'; 

// Security check: Only Admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

include '../includes/header.php'; 

// Handle Search and Filtering
$search_query = "";
$search_param = "%"; 

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
                    <input type="text" name="search" placeholder="Search by ID or Name..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="btn-success">Search</button>
                    <?php if (!empty($search_query)): ?>
                        <a href="view_results.php" class="btn-danger btn-clear">Clear</a>
                    <?php endif; ?>
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
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // The Mega-Join Query
                        // We use LEFT JOIN for assessments so students without marks still appear
                        $query = "SELECT s.student_id, s.student_name, i.company_name, a.full_name AS assessor_name, 
                                         asm.final_score, asm.qualitative_comments 
                                  FROM internships i
                                  JOIN students s ON i.student_id = s.student_id
                                  JOIN assessors a ON i.assessor_id = a.assessor_id
                                  LEFT JOIN assessments asm ON i.internship_id = asm.internship_id
                                  WHERE s.student_id LIKE ? OR s.student_name LIKE ?
                                  ORDER BY s.student_id ASC";
                                  
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("ss", $search_param, $search_param);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><strong>" . htmlspecialchars($row['student_id']) . "</strong><br>" . htmlspecialchars($row['student_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['assessor_name']) . "</td>";
                                
                                // Check if the assessment exists
                                if ($row['final_score'] !== null) {
                                    // Replaced inline styles with .score-graded and .score-comments
                                    echo "<td class='score-graded'>" . htmlspecialchars($row['final_score']) . "%</td>";
                                    echo "<td class='score-comments'>" . htmlspecialchars($row['qualitative_comments']) . "</td>";
                                } else {
                                    // Replaced inline style with .score-pending
                                    echo "<td colspan='2' class='score-pending'>Evaluation Pending</td>";
                                }
                                echo "</tr>";
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

<?php include '../includes/footer.php'; ?>