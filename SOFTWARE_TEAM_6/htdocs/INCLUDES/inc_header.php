<?php
if (!isset($title)) $title = "ROCU";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/role_helper.php';

$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" type="image/png" href="IMAGES/ROCU_FAVICON.png">
    <link href="CSS/default_styles.css" rel="stylesheet">
    <link href="CSS/root_colors.css" rel="stylesheet">
    <link href="CSS/header_footer_styles.css" rel="stylesheet">
    <title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>
<div class="TOP-SECTION">
    <img src="IMAGES/ROCU.png" class="TOP-HERO-IMAGE" alt="ROCU Logo">
</div>

<?php if ($user): ?>
    <div class="BUTTON-CONTAINER">
        <button onclick="window.location.href='list-task-page.php'" class="HOME-BUTTON">Home</button>
        <?php if (has_role('Admin')): ?>
            <button onclick="window.location.href='admin-page.php'" class="ADMIN-BUTTON">Admin Panel</button>
        <?php endif; ?>
        <button onclick="window.location.href='auth0_logout.php'" class="LOGOUT-BUTTON">Logout</button>
    </div>
<?php endif; ?>
<div class="MIDDLE-SECTION">
