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
        <div class="alert alert-success">
            <?php 
                echo htmlspecialchars($_SESSION['message']); 
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="management-layout">
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
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Login Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <button type="submit" class="btn-success">Create Account</button>
            </form>
        </section>

        <section class="admin-card">
            <h2>Registered Assessors</h2>
            <div class="table-responsive">
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
                                            <button type='submit' class='btn-danger' onclick='return confirm(\"Delete this assessor account?\")'>Delete</button>
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
            </div>
        </section>
    </div>

</main>
<?php include '../includes/footer.php'; ?>