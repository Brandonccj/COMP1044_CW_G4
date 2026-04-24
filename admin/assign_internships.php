<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

include '../includes/header.php';

// Only show students who do NOT already have an internship assigned
$students_result = mysqli_query($conn, "
    SELECT student_id, student_name FROM students 
    WHERE student_id NOT IN (SELECT student_id FROM internships)
    ORDER BY student_name ASC
");
$assessors_result = mysqli_query($conn, "SELECT assessor_id, full_name FROM assessors ORDER BY full_name ASC");

// For the edit modal, we need the full assessor list separately
$assessors_for_edit = mysqli_query($conn, "SELECT assessor_id, full_name FROM assessors ORDER BY full_name ASC");
?>

<main class="dashboard-container">
    <a href="../admin_dashboard.php" class="back-link">← Back to Dashboard</a>

    <header class="dashboard-header">
        <h1>Internship Management</h1>
    </header>

    <div class="management-layout">
        <section class="admin-card">
            <h2>Assign Student to Internship</h2>

            <?php if (mysqli_num_rows($students_result) === 0): ?>
                <p style="color: var(--text-muted); font-style: italic; padding: 10px 0;">
                    All registered students have already been assigned an internship.
                </p>
            <?php else: ?>
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
            <?php endif; ?>
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
                        $query = "SELECT i.internship_id, s.student_name, s.student_id, i.company_name, 
                                         a.full_name AS assessor_name, i.assessor_id
                                  FROM internships i
                                  JOIN students s ON i.student_id = s.student_id
                                  JOIN assessors a ON i.assessor_id = a.assessor_id
                                  ORDER BY i.internship_id DESC";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $iid         = htmlspecialchars($row['internship_id']);
                                $sname       = htmlspecialchars($row['student_name']);
                                $sid         = htmlspecialchars($row['student_id']);
                                $company     = htmlspecialchars($row['company_name']);
                                $aname       = htmlspecialchars($row['assessor_name']);
                                $assessor_id = htmlspecialchars($row['assessor_id']);

                                echo "<tr>
                                    <td>{$sname} ({$sid})</td>
                                    <td>{$company}</td>
                                    <td>{$aname}</td>
                                    <td style='display:flex; gap:6px; flex-wrap:wrap;'>
                                        <button class='btn-edit' onclick=\"openEditModal('{$iid}', '{$company}', '{$assessor_id}')\">Edit</button>
                                        <form action='../actions/save_internship.php' method='POST' style='display:inline;' class='delete-form' data-confirm-msg='Remove internship assignment for {$sname}?'>
                                            <input type='hidden' name='action' value='delete'>
                                            <input type='hidden' name='internship_id' value='{$iid}'>
                                            <button type='submit' class='btn-danger'>Remove</button>
                                        </form>
                                    </td>
                                </tr>";
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

    <!-- EDIT INTERNSHIP MODAL -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:var(--bg-card); padding:30px; border-radius:var(--radius-lg); width:90%; max-width:480px; box-shadow:var(--shadow-lg); border:1px solid var(--border);">
            <h2 style="margin-bottom:20px; color:var(--text-primary);">Edit Internship</h2>
            <form action="../actions/save_internship.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="edit_internship_id" name="internship_id">

                <div class="form-group">
                    <label for="edit_company_name">Company Name:</label>
                    <input type="text" id="edit_company_name" name="company_name" required>
                </div>

                <div class="form-group">
                    <label for="edit_assessor_id">Assigned Assessor:</label>
                    <select id="edit_assessor_id" name="assessor_id" required>
                        <?php while ($a = mysqli_fetch_assoc($assessors_for_edit)): ?>
                            <option value="<?php echo htmlspecialchars($a['assessor_id']); ?>">
                                <?php echo htmlspecialchars($a['full_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="display:flex; gap:10px; margin-top:10px;">
                    <button type="submit" class="btn-success" style="flex:1;">Save Changes</button>
                    <button type="button" onclick="closeEditModal()" class="btn-danger" style="flex:1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
function openEditModal(internshipId, company, assessorId) {
    document.getElementById('edit_internship_id').value = internshipId;
    document.getElementById('edit_company_name').value = company;
    document.getElementById('edit_assessor_id').value = assessorId;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

<?php include '../includes/footer.php'; ?>
