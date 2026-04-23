<?php 
session_start(); 

// If they are already logged in, bypass the login screen!
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['role'] === 'Admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: assessor_dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Management System — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
</head>
<body class="login-page-body">

    <?php if (isset($_SESSION['message'])): ?>
        <div class="toast-notification alert-<?php echo ($_SESSION['message_type'] ?? '') === 'error' ? 'danger' : 'success'; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <div class="login-wrapper">
        <p class="login-logo">Internship System</p>
        <h1 class="login-title">Internship Result<br>Management System</h1>

        <div class="login-container">
            <h2>Sign In</h2>

            <form action="actions/login_action.php" method="POST">

                <div class="form-group">
                    <label for="role">Login as</label>
                    <select name="role" id="role" required>
                        <option value="Admin">Admin</option>
                        <option value="Assessor">Assessor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required placeholder="Enter your username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" required placeholder="Enter your password">
                        <button type="button" class="password-toggle" id="togglePassword" title="Show/Hide Password">👁️</button>
                    </div>
                </div>

                <button type="submit" name="login_submit">Sign In</button>

            </form>
        </div>
    </div>

    <script src="assets/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>