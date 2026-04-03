<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Management System - Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-wrapper">
        <h1>Internship Result Management System</h1>

        <div class="login-container">
            <h2>System Login</h2>

            <form action="actions/login_action.php" method="POST">
                
                <div class="form-group">
                    <label for="role">Login As:</label>
                    <select name="role" id="role" required>
                        <option value="Admin">Admin</option>
                        <option value="Assessor">Assessor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <button type="submit" name="login_submit">Log In</button>
                
            </form>
        </div>
    </div>
</body>
</html>