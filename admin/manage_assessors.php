<?php
// This page allows admins to create new assessor accounts and view existing ones

require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php'; 

// Security check: Only Admins allowed
if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

include '../includes/header.php'; 
?>
<main class="dashboard-container">
    <header>
        <h1>Manage Assessor Accounts</h1>
        <a href="../admin_dashboard.php" style="text-decoration: none; color: #3498db;">← Back to Dashboard</a>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; margin: 15px 0; border-radius: 5px;">
            <?php 
                echo htmlspecialchars($_SESSION['message']); 
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <section class="admin-card" style="margin-top: 20px;">
        <h2>Create New Assessor Account</h2>
        <form action="../actions/save_assessor.php" method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="form-group">
                <label for="full_name">Full Name (e.g., Dr. Alan Smith):</label>
                <input type="text" name="full_name" id="full_name" required>
            </div>

            <div class="form-group">
                <label for="username">Login Username:</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="password">Login Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" style="background-color: #2ecc71;">Create Account</button>
        </form>
    </section>

    <section class="admin-card" style="margin-top: 40px;">
        <h2>Registered Assessors</h2>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Assessor ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT assessor_id, full_name, username FROM assessors ORDER BY assessor_id ASC";
                $result = mysqli_query($conn, $query);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['assessor_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>
                                <form action='../actions/save_assessor.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='action' value='delete'>
                                    <input type='hidden' name='assessor_id' value='" . htmlspecialchars($row['assessor_id']) . "'>
                                    <button type='submit' style='background-color: #e74c3c; padding: 5px 10px;' onclick='return confirm(\"Delete this assessor account?\")'>Delete</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='empty-message'>No assessors found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</main>
<?php include '../includes/footer.php'; ?>