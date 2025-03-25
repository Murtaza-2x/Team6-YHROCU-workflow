
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
    // Ensure the session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if session variables are set and not empty 
    $email = isset($_SESSION["email"]) ? $_SESSION["email"] : 'Guest';  // Default to 'Guest' if not set
    $clearance = isset($_SESSION["clearance"]) ? $_SESSION["clearance"] : 'Unknown';  // Default to 'Unknown' if not set


    echo "<p class='DASH-HEADER-2'>Welcome " . $email . " (" . $clearance . ")</p>";
?>
  </div>
  <div class="DASH-AREA">
  </div>
</div>
<!-- DASHBOARD SECTION END -->
