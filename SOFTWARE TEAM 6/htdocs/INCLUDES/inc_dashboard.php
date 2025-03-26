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


    $userId = $_SESSION['id'] ?? 0;
    $username = '';

    if ($userId > 0) {
      $sql = "SELECT username FROM users WHERE id = $userId LIMIT 1";
      $result = $conn->query($sql);
      if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['username'];
      }
    }

    $clearance  = $_SESSION["clearance"] ?? 'User';
    $displayName = $username ?: 'User';

    // Convert clearance to display label
    switch ($clearance) {
      case 'User':
        $clearanceLabel = 'Staff';
        break;
      case 'Manager':
        $clearanceLabel = 'Manager';
        break;
      case 'Admin':
        $clearanceLabel = 'Admin';
        break;
      default:
        $clearanceLabel = 'Staff';
        break;
    }

    echo "<p class='DASH-HEADER-2'>Welcome {$clearanceLabel} {$displayName}</p>";
    ?>
  </div>

  <div class="DASH-AREA">
    <div class="DASH-SECTION-CONTAINER">

      <div class="DASH-SECTION-1">
      </div>

    </div>

  </div>
</div>
<!-- DASHBOARD SECTION END -->