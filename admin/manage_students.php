<?php
session_start();

// Enable real security and database connection
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php'; 

// Additional security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Include the shared header (brings in the navbar and style.css)
include '../includes/header.php'; 
?>

<main class="dashboard-container">
    
    <a href="../admin_dashboard.php" class="back-link">← Back to Dashboard</a>

    <header class="dashboard-header">
        <h1>Manage Student Records</h1>
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
            <h2>Add New Student</h2>
            <form action="../actions/save_student.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="student_id">Student ID:</label>
                    <input type="text" name="student_id" id="student_id" required placeholder="e.g., S1004">
                </div>

                <div class="form-group">
                    <label for="student_name">Full Name:</label>
                    <input type="text" name="student_name" id="student_name" required>
                </div>

                <div class="form-group">
                    <label for="programme">Programme:</label>
                    <input type="text" name="programme" id="programme" required placeholder="e.g., BSc Computer Science">
                </div>

                <button type="submit" class="btn-success">Add Student</button>
            </form>
        </section>

        <section class="admin-card">
            <h2>Registered Students</h2>
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Programme</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch real data from the database
                        $query = "SELECT student_id, student_name, programme FROM students ORDER BY student_id ASC";
                        $result = mysqli_query($conn, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['programme']) . "</td>";
                                echo "<td>
                                        <form action='../actions/save_student.php' method='POST' style='display:inline;'>
                                            <input type='hidden' name='action' value='delete'>
                                            <input type='hidden' name='student_id' value='" . htmlspecialchars($row['student_id']) . "'>
                                            <button type='submit' class='btn-danger' onclick='return confirm(\"Are you sure you want to delete this student?\")'>Delete</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='empty-message'>No students found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div> 
</main>

<?php 
// Include the shared footer
include '../includes/footer.php'; 
?>