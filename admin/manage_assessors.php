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
        <h1>Manage Assessor Accounts</h1>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo ($_SESSION['message_type'] ?? '') === 'error' ? 'danger' : 'success'; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <div class="management-layout">

        <!-- CREATE ASSESSOR FORM -->
        <section class="admin-card">
            <h2>Create New Assessor Account</h2>
            <form action="../actions/save_assessor.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" name="full_name" id="full_name" required placeholder="e.g., Dr. Alan Smith">
                </div>
                <div class="form-group">
                    <label for="username">Login Username:</label>
                    <input type="text" name="username" id="username" required placeholder="e.g., dr.smith">
                </div>
                <div class="form-group">
                    <label for="password">Login Password:</label>
                    <input type="password" name="password" id="password" required minlength="6" placeholder="Min. 6 characters">
                </div>
                <button type="submit" class="btn-success">Create Account</button>
            </form>
        </section>

        <!-- ASSESSOR TABLE -->
        <section class="admin-card">
            <h2>Registered Assessors</h2>
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT assessor_id, full_name, username FROM assessors ORDER BY assessor_id ASC");
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $aid  = htmlspecialchars($row['assessor_id']);
                                $name = htmlspecialchars($row['full_name']);
                                $user = htmlspecialchars($row['username']);
                                echo "<tr>
                                    <td>{$aid}</td>
                                    <td>{$name}</td>
                                    <td>{$user}</td>
                                    <td style='display:flex;gap:6px;flex-wrap:wrap;'>
                                        <button class='btn-edit' onclick=\"openEditModal('{$aid}', '{$name}', '{$user}')\">Edit</button>
                                        <form action='../actions/save_assessor.php' method='POST' style='display:inline;'>
                                            <input type='hidden' name='action' value='delete'>
                                            <input type='hidden' name='assessor_id' value='{$aid}'>
                                            <button type='submit' class='btn-danger' onclick='return confirm(\"Delete assessor {$name}? Their internship assignments will also be removed.\")'>Delete</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='empty-message'>No assessors found.</td></tr>";
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
            <h2 style="margin-bottom:20px; color:#2c3e50;">Edit Assessor</h2>
            <form action="../actions/save_assessor.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="edit_assessor_id" name="assessor_id">
                <div class="form-group">
                    <label>Current Username:</label>
                    <input type="text" id="edit_username_display" readonly style="background:#f0f0f0; cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label for="edit_full_name">Full Name:</label>
                    <input type="text" id="edit_full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_password">New Password <span style="color:#999;font-weight:normal;">(leave blank to keep current)</span>:</label>
                    <input type="password" id="edit_password" name="password" minlength="6" placeholder="Leave blank to keep unchanged">
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
function openEditModal(id, name, username) {
    document.getElementById('edit_assessor_id').value = id;
    document.getElementById('edit_full_name').value = name;
    document.getElementById('edit_username_display').value = username;
    document.getElementById('edit_password').value = '';
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

<?php include '../includes/footer.php'; ?>