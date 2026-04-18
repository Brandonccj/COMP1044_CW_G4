<?php
session_start();
require_once 'includes/auth_check.php';
require_once 'includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// Summary stats for the dashboard
$stats = [];

$r = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM students");
$stats['students'] = mysqli_fetch_assoc($r)['cnt'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM assessors");
$stats['assessors'] = mysqli_fetch_assoc($r)['cnt'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM internships");
$stats['internships'] = mysqli_fetch_assoc($r)['cnt'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM assessments");
$stats['graded'] = mysqli_fetch_assoc($r)['cnt'];

include 'includes/header.php';
?>

<main class="dashboard-container">
    <header class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Manage system users, records, and assignments.</p>
    </header>

    <!-- Summary Stats -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px,1fr)); gap:14px; margin-bottom:30px;">
        <div style="background:#f0f9f4; border:1px solid #c3e6cb; border-radius:8px; padding:16px; text-align:center;">
            <div style="font-size:2em; font-weight:bold; color:#27ae60;"><?php echo $stats['students']; ?></div>
            <div style="color:#555; font-size:13px;">Students</div>
        </div>
        <div style="background:#e8f4fd; border:1px solid #b6d8f2; border-radius:8px; padding:16px; text-align:center;">
            <div style="font-size:2em; font-weight:bold; color:#2980b9;"><?php echo $stats['assessors']; ?></div>
            <div style="color:#555; font-size:13px;">Assessors</div>
        </div>
        <div style="background:#fef9e7; border:1px solid #f9e4b7; border-radius:8px; padding:16px; text-align:center;">
            <div style="font-size:2em; font-weight:bold; color:#e67e22;"><?php echo $stats['internships']; ?></div>
            <div style="color:#555; font-size:13px;">Internships</div>
        </div>
        <div style="background:#f4ecf7; border:1px solid #d7bde2; border-radius:8px; padding:16px; text-align:center;">
            <div style="font-size:2em; font-weight:bold; color:#8e44ad;"><?php echo $stats['graded']; ?> / <?php echo $stats['internships']; ?></div>
            <div style="color:#555; font-size:13px;">Graded</div>
        </div>
    </div>

    <div class="admin-grid">
        <section class="admin-card">
            <h2>Student Records</h2>
            <p>Add, edit, or remove student profiles.</p>
            <a href="admin/manage_students.php" class="action-btn">Manage Students</a>
        </section>

        <section class="admin-card">
            <h2>Assessor Accounts</h2>
            <p>Create and manage authorized assessor accounts.</p>
            <a href="admin/manage_assessors.php" class="action-btn">Manage Assessors</a>
        </section>

        <section class="admin-card">
            <h2>Internship Management</h2>
            <p>Assign students to assessors and record company details.</p>
            <a href="admin/assign_internships.php" class="action-btn">Assign Internships</a>
        </section>

        <section class="admin-card">
            <h2>System Results</h2>
            <p>View final scores and detailed mark breakdowns for all students.</p>
            <a href="admin/view_results.php" class="action-btn">View All Results</a>
        </section>
    </div>
</main>

<?php include 'includes/footer.php'; ?>