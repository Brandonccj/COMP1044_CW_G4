<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Management System - Login</title>
</head>
<body>

    <h1>Internship Result Management System</h1>
    <h2>System Login</h2>

    <form action="actions/login_action.php" method="POST">
        
        <div>
            <label for="role">Login As:</label>
            <select name="role" id="role" required>
                <option value="Admin">Admin</option>
                <option value="Assessor">Assessor</option>
            </select>
        </div>
        <br>

        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
        </div>
        <br>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <br>

        <button type="submit" name="login_submit">Log In</button>
        
    </form>

</body>
</html>