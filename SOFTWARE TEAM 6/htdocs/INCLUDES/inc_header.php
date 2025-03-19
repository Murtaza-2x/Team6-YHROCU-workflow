<?php if (!isset($title)) $title = "ROCU"; ?>

<?php
session_start();
$loggedInUserId = $_SESSION['id'];
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
  <!-- TOP SECTION END -->

  <!-- MIDDLE SECTION -->
  <div class="MIDDLE-SECTION">