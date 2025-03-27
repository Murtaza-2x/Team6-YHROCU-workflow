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

<body>

  <!-- TOP SECTION -->
  <div class="TOP-SECTION">
    <img src="IMAGES/ROCU.png" class="TOP-HERO-IMAGE"></img>
  </div>

  <?php if (isset($_SESSION['id'])): ?>
    <div class="BUTTON-CONTAINER">
      <button onclick="window.location.href='list-task-page.php'" class="HOME-BUTTON">Home</button>
      <?php if (isset($_SESSION['clearance']) && $_SESSION['clearance'] === 'Admin'): ?>
        <button class="ADMIN-BUTTON" onclick="window.location.href='admin-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($_SESSION['id']); ?>'">
          Admin Panel
        </button>
      <?php endif; ?>
      <button onclick="window.location.href='INCLUDES/inc_logout.php'" class="LOGOUT-BUTTON">Logout</button>
    </div>
  <?php endif; ?>

  <!-- TOP SECTION END -->

  <!-- MIDDLE SECTION -->
  <div class="MIDDLE-SECTION">