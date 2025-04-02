<head>
  <!-- CHART.JS FOR DIAGRAMS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    // GET LOGGED IN USER DETAILS
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

    // ADDITIONAL DASHBOARD LOGIC (CHART DATA FETCHING)
    $activeTaskCount = 0;
    $projectSummary = [];

    if ($clearance === 'User') {
      // USER: COUNT ACTIVE TASKS (NEW OR IN PROGRESS)
      $sql = "SELECT COUNT(*) AS count FROM tasks 
              JOIN task_assigned_users tau ON tasks.id = tau.task_id
              WHERE tau.user_id = $userId AND tasks.status IN ('New', 'In Progress')";
      $result = $conn->query($sql);
      if ($result) {
        $row = $result->fetch_assoc();
        $activeTaskCount = $row['count'] ?? 0;
      }

    } elseif ($clearance === 'Manager') {
      // MANAGER: FILTER PROJECTS BY PRIORITY AND DUE DATE RANGE
      $filterPriority = $_GET['priority'] ?? 'All';
      $filterStart = $_GET['start_date'] ?? '';
      $filterEnd = $_GET['end_date'] ?? '';

      $sql = "SELECT status, COUNT(*) AS total FROM projects WHERE 1";

      if ($filterPriority !== 'All') {
        $sql .= " AND priority = '" . $conn->real_escape_string($filterPriority) . "'";
      }

      if (!empty($filterStart) && !empty($filterEnd)) {
        $sql .= " AND DUE_DATE BETWEEN '" . $conn->real_escape_string($filterStart) . "' AND '" . $conn->real_escape_string($filterEnd) . "'";
      }

      $sql .= " GROUP BY status";
      $result = $conn->query($sql);
      while ($row = $result->fetch_assoc()) {
        $projectSummary[] = $row;
      }
    }

    ?>
  </div>

  <div class="DASH-AREA">
    <div class="DASH-SECTION-CONTAINER">

      <div class="DASH-SECTION-1">

        <!-- USER DASHBOARD VIEW -->
        <?php if ($clearance === 'User'): ?>
          <div style="max-width: 400px; margin: 30px auto;">
            <canvas id="userTaskChart"></canvas>
          </div>

          <script>
            const ctx = document.getElementById('userTaskChart').getContext('2d');
            new Chart(ctx, {
              type: 'doughnut',
              data: {
                labels: ['Active Tasks'],
                datasets: [{
                  data: [<?php echo $activeTaskCount; ?>],
                  backgroundColor: ['#4bc0c0']
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Your Active Tasks (New or In Progress)'
                  }
                }
              }
            });
          </script>

        <!-- MANAGER DASHBOARD VIEW WITH FILTERS -->
        <?php elseif ($clearance === 'Manager'): ?>

          <!-- MANAGER FILTER SECTION -->
          <form method="GET" style="text-align: center; margin-bottom: 20px;">
            <label for="priority">Priority:</label>
            <select name="priority" id="priority">
              <option value="All" <?= $filterPriority === 'All' ? 'selected' : '' ?>>All</option>
              <option value="Urgent" <?= $filterPriority === 'Urgent' ? 'selected' : '' ?>>Urgent</option>
              <option value="Moderate" <?= $filterPriority === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
              <option value="Low" <?= $filterPriority === 'Low' ? 'selected' : '' ?>>Low</option>
            </select>

            <label for="start_date">From:</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($filterStart) ?>">

            <label for="end_date">To:</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($filterEnd) ?>">

            <button type="submit">Apply Filters</button>
          </form>

          <!-- MANAGER BAR CHART -->
          <div style="max-width: 700px; margin: 0 auto;">
            <canvas id="managerFilteredChart"></canvas>
          </div>

          <script>
            const filteredData = <?= json_encode($projectSummary); ?>;
            const labels = filteredData.map(row => row.status);
            const values = filteredData.map(row => parseInt(row.total));

            new Chart(document.getElementById('managerFilteredChart'), {
              type: 'bar',
              data: {
                labels: labels,
                datasets: [{
                  label: 'Projects',
                  data: values,
                  backgroundColor: '#36a2eb'
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Filtered Project Status Summary'
                  }
                },
                scales: {
                  y: { beginAtZero: true }
                }
              }
            });
          </script>

        <?php endif; ?>

      </div>

    </div>
  </div>
</div>
<!-- DASHBOARD SECTION END -->
