<?php
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Test</title>
    </head>
<body>
    <main class="dashboard-container">
        <header class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </header>

        <div class="admin-grid">
            <section class="admin-card">
                <h2>Student Records</h2>
                <a href="admin/manage_students.php">Manage Students</a>
            </section>
            <section class="admin-card">
                <h2>Assessor Accounts</h2>
                <a href="admin/manage_assessors.php">Manage Assessors</a>
            </section>
            <section class="admin-card">
                <h2>Internship Management</h2>
                <a href="admin/assign_internships.php">Assign Internships</a>
            </section>
            <section class="admin-card">
                <h2>System Results</h2>
                <a href="admin/view_results.php">View All Results</a>
            </section>
        </div>
    </main>
</body>
</html>
<?php 
?>