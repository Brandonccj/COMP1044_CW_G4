<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

include '../includes/header.php'; 

// Fetch data for the dropdowns
$students_result = mysqli_query($conn, "SELECT student_id, student_name FROM students ORDER BY student_name ASC");
$assessors_result = mysqli_query($conn, "SELECT assessor_id, full_name FROM assessors ORDER BY full_name ASC");
?>

<main class="dashboard-container">
    <header class="dashboard-header">
        <h1>Internship Management</h1>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php 
                echo htmlspecialchars($_SESSION['message']); 
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="management-layout">
        <section class="admin-card">
            <h2>Assign Student to Internship</h2>
            <form action="../actions/save_internship.php" method="POST">
                <input type="hidden" name="action" value="assign">
                
                <div class="form-group">
                    <label for="student_id">Select Student:</label>
                    <select name="student_id" id="student_id" required>
                        <option value="">-- Choose a Student --</option>
                        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                            <option value="<?php echo htmlspecialchars($student['student_id']); ?>">
                                <?php echo htmlspecialchars($student['student_name']) . " (" . htmlspecialchars($student['student_id']) . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="company_name">Internship Company Name:</label>
                    <input type="text" name="company_name" id="company_name" required placeholder="e.g., Tech Innovations Inc.">
                </div>

                <div class="form-group">
                    <label for="assessor_id">Assign to Assessor:</label>
                    <select name="assessor_id" id="assessor_id" required>
                        <option value="">-- Choose an Assessor --</option>
                        <?php while ($assessor = mysqli_fetch_assoc($assessors_result)): ?>
                            <option value="<?php echo htmlspecialchars($assessor['assessor_id']); ?>">
                                <?php echo htmlspecialchars($assessor['full_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="btn-success">Save Assignment</button>
            </form>
        </section>

        <section class="admin-card">
            <h2>Current Assignments</h2>
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Company</th>
                            <th>Assessor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Join the 3 tables to get readable names instead of just IDs
                        $query = "SELECT i.internship_id, s.student_name, s.student_id, i.company_name, a.full_name AS assessor_name 
                                  FROM internships i 
                                  JOIN students s ON i.student_id = s.student_id 
                                  JOIN assessors a ON i.assessor_id = a.assessor_id
                                  ORDER BY i.internship_id DESC";
                        $result = mysqli_query($conn, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['student_name']) . " (" . htmlspecialchars($row['student_id']) . ")</td>";
                                echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['assessor_name']) . "</td>";
                                echo "<td>
                                        <form action='../actions/save_internship.php' method='POST' style='display:inline;'>
                                            <input type='hidden' name='action' value='delete'>
                                            <input type='hidden' name='internship_id' value='" . htmlspecialchars($row['internship_id']) . "'>
                                            <button type='submit' class='btn-danger' onclick='return confirm(\"Remove this internship assignment?\")'>Remove</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='empty-message'>No internships assigned yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <a href="../admin_dashboard.php" class="back-link">← Back to Dashboard</a>
</main>
<?php include '../includes/footer.php'; ?>