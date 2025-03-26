<?php if (!isset($title)) $title = "ROCU"; ?>

<?php
session_start();
$id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html>
<link rel="shortcut icon" type="image/png" href="IMAGES/ROCU_FAVICON.png">
<link href="CSS/default_styles.css" rel="stylesheet">
<link href="CSS/root_colors.css" rel="stylesheet">
<link href="CSS/header_footer_styles.css" rel="stylesheet">
<link href="CSS/buttons.css" rel="stylesheet">
<body>

  <!-- TOP SECTION -->
  <div class="TOP-SECTION">
    <img src="IMAGES/ROCU.png" class="TOP-HERO-IMAGE"></img>
  </div>

  <?php if (isset($_SESSION['id'])): ?>
  <div class="logout-button-container">
    <a href="list-task-page.php" class="home-button">Home</a>
    <?php if (isset($_SESSION['clearance']) && $_SESSION['clearance'] === 'Admin'): ?>
      <a href="admin-page.php" class="admin-button">Admin Panel</a>
    <?php endif; ?>
    <a href="INCLUDES/inc_logout.php" class="logout-button">Logout</a>
  </div>
<?php endif; ?>

  <!-- TOP SECTION END -->

  <!-- MIDDLE SECTION -->
  <div class="MIDDLE-SECTION">