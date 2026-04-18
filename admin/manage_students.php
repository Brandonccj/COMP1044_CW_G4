<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

include '../includes/header.php';
?>

<main class="dashboard-container">

    <a href="../admin_dashboard.php" class="back-link">← Back to Dashboard</a>

    <header class="dashboard-header">
        <h1>Manage Student Records</h1>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type'] === 'error' ? 'danger' : 'success'; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <div class="management-layout">

        <!-- ADD STUDENT FORM -->
        <section class="admin-card">
            <h2>Add New Student</h2>
            <form action="../actions/save_student.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="student_id">Student ID:</label>
                    <input type="text" name="student_id" id="student_id" required placeholder="e.g., S1004" pattern="[A-Za-z0-9]+" title="Letters and numbers only">
                </div>
                <div class="form-group">
                    <label for="student_name">Full Name:</label>
                    <input type="text" name="student_name" id="student_name" required placeholder="e.g., Peter Parker">
                </div>
                <div class="form-group">
                    <label for="programme">Programme:</label>
                    <input type="text" name="programme" id="programme" required placeholder="e.g., BSc Computer Science">
                </div>
                <button type="submit" class="btn-success">Add Student</button>
            </form>
        </section>

        <!-- STUDENT TABLE -->
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
                        $query = "SELECT student_id, student_name, programme FROM students ORDER BY student_id ASC";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $sid  = htmlspecialchars($row['student_id']);
                                $name = htmlspecialchars($row['student_name']);
                                $prog = htmlspecialchars($row['programme']);
                                echo "<tr>";
                                echo "<td>{$sid}</td>";
                                echo "<td>{$name}</td>";
                                echo "<td>{$prog}</td>";
                                echo "<td style='display:flex;gap:6px;flex-wrap:wrap;'>
                                    <button class='btn-edit' onclick=\"openEditModal('{$sid}', '{$name}', '{$prog}')\">Edit</button>
                                    <form action='../actions/save_student.php' method='POST' style='display:inline;'>
                                        <input type='hidden' name='action' value='delete'>
                                        <input type='hidden' name='student_id' value='{$sid}'>
                                        <button type='submit' class='btn-danger' onclick='return confirm(\"Delete {$name}? This will also remove their internship and assessment records.\")'>Delete</button>
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

    <!-- EDIT MODAL -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:var(--modal-bg, #fff); padding:30px; border-radius:10px; width:90%; max-width:460px; box-shadow:0 10px 30px rgba(0,0,0,0.2);">
            <h2 style="margin-bottom:20px; color:#2c3e50;">Edit Student</h2>
            <form action="../actions/save_student.php" method="POST">
                <input type="hidden" name="action" value="update">
                <div class="form-group">
                    <label>Student ID (cannot change):</label>
                    <input type="text" id="edit_student_id" name="student_id" readonly style="background:#f0f0f0; cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label for="edit_student_name">Full Name:</label>
                    <input type="text" id="edit_student_name" name="student_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_programme">Programme:</label>
                    <input type="text" id="edit_programme" name="programme" required>
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
function openEditModal(id, name, programme) {
    document.getElementById('edit_student_id').value = id;
    document.getElementById('edit_student_name').value = name;
    document.getElementById('edit_programme').value = programme;
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
// Close modal when clicking the dark backdrop
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

<?php include '../includes/footer.php'; ?>