<?php
session_start();
// This page contains the form to add new students 
// and a table to view, edit, or delete existing records.

// Additional security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
</head>
<body>
    <main class="dashboard-container">
        <header>
            <h1>Manage Student Records</h1>
            <a href="admin_dashboard.php">← Back to Dashboard</a>
        </header>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert">
                <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <section class="admin-card">
            <h2>Add New Student</h2>
            <form action="../actions/save_student.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="student_id">Student ID:</label>
                    <input type="text" name="student_id" id="student_id" required placeholder="e.g., 20456789">
                </div>

                <div class="form-group">
                    <label for="student_name">Full Name:</label>
                    <input type="text" name="student_name" id="student_name" required>
                </div>

                <div class="form-group">
                    <label for="programme">Programme:</label>
                    <input type="text" name="programme" id="programme" required placeholder="e.g., BSc Computer Science">
                </div>

                <button type="submit">Add Student</button>
            </form>
        </section>

        <section class="admin-card">
            <h2>Registered Students</h2>
            <table border="1" style="width:100%; text-align:left;">
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
                    ?>
                    <tr>
                        <td>Dummy-ID-01</td>
                        <td>Jane Doe</td>
                        <td>BSc Software Engineering</td>
                        <td><button disabled>Delete (Waiting on DB)</button></td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
<?php 
?>