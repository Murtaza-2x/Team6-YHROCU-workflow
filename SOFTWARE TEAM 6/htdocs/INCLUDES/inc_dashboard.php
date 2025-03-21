<head>
  <title><?php echo $title; ?></title>
  <link href="CSS/pill_styles.css" rel="stylesheet">
  <link href="CSS/dropdown_styles.css" rel="stylesheet">
  <link href="CSS/dashboard_styles.css" rel="stylesheet">
  <link href="CSS/tasklist_styles.css" rel="stylesheet">
</head>

<!-- DASHBOARD SECTION -->
<div class="DASH-CONTENT">
  <div class="DASH-HEADER">
    <p class="DASH-HEADER-1">Dashboard -</p>
    <?php
    $clearance = $_SESSION["clearance"];
    $id = $_SESSION["id"];
    $username = $_SESSION["username"];
    echo "<p class='DASH-HEADER-2'>Welcome " . $clearance . " " . $username . " - ID " . $id . "</p>";
    ?>
  </div>
  <div class="DASH-AREA">
  </div>
</div>
<!-- DASHBOARD SECTION END -->