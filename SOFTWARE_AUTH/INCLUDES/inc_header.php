<?php
if (!isset($title)) { $title = "ROCU";
}

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
    <title><?php echo htmlspecialchars($title); ?></title>

    <link rel="shortcut icon" type="image/png" href="IMAGES/ROCU_FAVICON.png">

    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/default_styles.css" rel="stylesheet">
    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/root_colors.css" rel="stylesheet">
    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/pill_styles.css" rel="stylesheet">
    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/body_styles.css" rel="stylesheet">
    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/dropdown_styles.css" rel="stylesheet">
    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/error_styles.css" rel="stylesheet">
    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/switch_styles.css" rel="stylesheet">
    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/filter_styles.css" rel="stylesheet">
    <link href="/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/CSS/header_footer_styles.css" rel="stylesheet">

    <script src="JS/TOGGLE-THEME.js"></script>
</head>

<body>
    <!-- TOP SECTION -->
    <div class="TOP-SECTION">
        <img src="IMAGES/ROCU.png" class="TOP-HERO-IMAGE no-invert">
    </div>
    <!-- TOP SECTION END -->

    <div class="BUTTON-CONTAINER">
        <div class="THEME-SWITCH-CONTAINER">
            <label class="THEME-SWITCH">
                <input type="checkbox" id="theme-toggle" onchange="toggleTheme()" />
                <span class="SLIDER"></span>
            </label>
        </div>
    </div>

    <?php if ($user) : ?>
        <!-- BUTTONS SECTION -->
        <div class="BUTTON-CONTAINER">
            <button onclick="window.location.href='/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/list-task-page.php'" class="HOME-BUTTON">Home</button>
            <?php if (has_role('Admin')) : ?>
                <button onclick="window.location.href='/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/admin-page.php'" class="ADMIN-BUTTON">Admin Panel</button>
            <?php endif; ?>
            <button onclick="window.location.href='/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/auth0_logout.php'" class="LOGOUT-BUTTON">Logout</button>
        </div>
        <!-- BUTTONS SECTION END -->
    <?php endif; ?>

    <!-- MIDDLE SECTION -->
    <div class="MIDDLE-SECTION">
