<?php
// Dynamically fix the paths depending on which subfolder the page is in
$is_subfolder = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/assessor/') !== false);

$css_path    = $is_subfolder ? '../assets/style.css?v=' . time() : 'assets/style.css?v=' . time();
$logout_path = $is_subfolder ? '../actions/logout.php' : 'actions/logout.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
    
    <script>
        var savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>
    <nav class="site-nav">
        <div class="nav-brand">Internship System</div>
        <div class="nav-right">
            <span class="nav-user">
                <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                &nbsp;·&nbsp;
                <?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>
            </span>
            <button class="theme-toggle" id="themeToggle" title="Toggle dark mode"></button>
            <a href="<?php echo $logout_path; ?>" class="nav-logout">Logout</a>
        </div>
    </nav>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="toast-notification alert-<?php echo ($_SESSION['message_type'] ?? '') === 'error' ? 'danger' : 'success'; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>
    
    <div style="flex-grow:1; display:flex; flex-direction:column;">