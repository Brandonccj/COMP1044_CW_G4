<?php
session_start();

// Enable the real security and database connections
require_once 'includes/auth_check.php'; 
require_once 'includes/db_connect.php'; 

// Kick out anyone who isn't an Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// Bring in the styling and navigation bar
include 'includes/header.php'; 
?>

<main class="dashboard-container">
    <header class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Manage system users, records, and assignments.</p>
    </header>

    <div class="admin-grid">
        <section class="admin-card">
            <h2>Student Records</h2>
            <p>Add new student profiles, update existing details, and remove records.</p>
            <a href="admin/manage_students.php" class="action-btn">Manage Students</a>
        </section>

        <section class="admin-card">
            <h2>Assessor Accounts</h2>
            <p>Create and manage authorized accounts for lecturers and supervisors.</p>
            <a href="admin/manage_assessors.php" class="action-btn">Manage Assessors</a>
        </section>

        <section class="admin-card">
            <h2>Internship Management</h2>
            <p>Assign students to assessors and record internship company details.</p>
            <a href="admin/assign_internships.php" class="action-btn">Assign Internships</a>
        </section>
        
        <section class="admin-card">
            <h2>System Results</h2>
            <p>Access the final internship results and mark breakdowns for all students.</p>
            <a href="admin/view_results.php" class="action-btn">View All Results</a>
        </section>
    </div>
</main>

<?php 
// Close the HTML with the shared footer
include 'includes/footer.php'; 
?>