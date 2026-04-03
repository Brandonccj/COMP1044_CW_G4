<?php
// Dynamically fix the paths depending on which subfolder the page is in
$is_subfolder = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/assessor/') !== false);

$css_path = $is_subfolder ? '../assets/style.css' : 'assets/style.css';
$logout_path = $is_subfolder ? '../actions/logout.php' : 'actions/logout.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Management System</title>
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
</head>
<body>
    <nav style="background-color: #2c3e50; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div style="color: white; font-weight: bold; font-size: 1.2em;">Internship System</div>
        <div>
            <span style="color: white; margin-right: 20px;">
                Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?> 
                (<?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>)
            </span>
            <a href="<?php echo $logout_path; ?>" style="color: #e74c3c; text-decoration: none; font-weight: bold; padding: 5px 10px; background-color: rgba(231, 76, 60, 0.1); border-radius: 4px;">Logout</a>
        </div>
    </nav>
    
    <div style="flex-grow: 1;">