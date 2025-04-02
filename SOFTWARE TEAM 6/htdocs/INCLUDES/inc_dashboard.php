<?php
/*
This is the dashboard page shown after login.
It displays different content based on the user's clearance level:
- For regular users (Staff), it shows a pie chart with their active tasks by priority.
- For managers, it shows:
  1. A bar chart filtered by priority and due dates for all projects.
  2. A pie chart showing all active tasks (New or In Progress).
*/
?>

<head>
  <!-- Chart.js for graphs -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title><?php echo $title; ?></title>

  <!-- Styles -->
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

    $clearance = $_SESSION["clearance"] ?? 'User';
    $displayName = $username ?: 'User';

    switch ($clearance) {
      case 'User': $clearanceLabel = 'Staff'; break;
      case 'Manager': $clearanceLabel = 'Manager'; break;
      case 'Admin': $clearanceLabel = 'Admin'; break;
      default: $clearanceLabel = 'Staff'; break;
    }

    echo "<p class='DASH-HEADER-2'>Welcome {$clearanceLabel} {$displayName}</p>";

    // Variables to store chart data
    $projectSummary = [];
    $activeTaskStatusBreakdown = [];
    $userPriorityBreakdown = [];

    // Pre-define filter variables to avoid undefined warning for Admins/Managers
    $filterPriority = $_GET['priority'] ?? 'All';
    $filterStart = $_GET['start_date'] ?? '';
    $filterEnd = $_GET['end_date'] ?? '';

    if ($clearance === 'User') {
      // Count user's active tasks grouped by priority (Urgent, Moderate, Low)
      $sql = "SELECT priority, COUNT(*) AS total 
              FROM tasks 
              JOIN task_assigned_users tau ON tasks.id = tau.task_id
              WHERE tau.user_id = $userId AND status IN ('New', 'In Progress')
              GROUP BY priority";
      $result = $conn->query($sql);
      if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $userPriorityBreakdown[] = $row;
        }
      }
    }

    elseif (in_array($clearance, ['Manager', 'Admin'])) {
      // Project status summary for bar chart
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

      // Active task status breakdown for pie chart
      $sqlActive = "SELECT status, COUNT(*) AS total FROM tasks WHERE status IN ('New', 'In Progress') GROUP BY status";
      $resultActive = $conn->query($sqlActive);
      while ($row = $resultActive->fetch_assoc()) {
        $activeTaskStatusBreakdown[] = $row;
      }
    }
    ?>
  </div>

  <div class="DASH-AREA">
    <div class="DASH-SECTION-CONTAINER">
      <div class="DASH-SECTION-1">

        <!-- STAFF VIEW -->
        <?php if ($clearance === 'User'): ?>
          <div class="USER-CHART-BOX">
            <canvas id="userPriorityChart"></canvas>
          </div>

          <script>
            // User pie chart: breakdown of assigned tasks by priority
            const userPriorityData = <?= json_encode($userPriorityBreakdown); ?>;
            const userLabels = userPriorityData.map(row => row.priority);
            const userCounts = userPriorityData.map(row => parseInt(row.total));

            new Chart(document.getElementById('userPriorityChart'), {
              type: 'pie',
              data: {
                labels: userLabels,
                datasets: [{
                  label: 'Your Active Tasks by Priority',
                  data: userCounts,
                  backgroundColor: ['#e74c3c', '#f39c12', '#2ecc71'] // Urgent, Moderate, Low
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Your Active Tasks by Priority'
                  }
                }
              }
            });
          </script>

        <!-- MANAGER VIEW & ADMIN VIEW -->
        <?php elseif (in_array($clearance, ['Manager', 'Admin'])): ?>
          <div class="MANAGER-CHART-WRAPPER">

            <!-- Filter Form -->
            <form method="GET" class="MANAGER-FILTER-FORM">
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

            <!-- Manager's bar + pie charts -->
            <div class="CHARTS-ROW">
              <div class="DASH-CHART-BOX">
                <canvas id="managerFilteredChart"></canvas>
              </div>

              <div class="DASH-PIE-CHART-BOX">
                <canvas id="activeTaskChart"></canvas>
              </div>
            </div>
          </div>

          <script>
            // Bar Chart (Project Status Summary)
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
                    text: 'Project Status Summary'
                  }
                },
                scales: { y: { beginAtZero: true } }
              }
            });

            // Pie Chart (Active Tasks by Status)
            const activeTaskData = <?= json_encode($activeTaskStatusBreakdown); ?>;
            const taskLabels = activeTaskData.map(row => row.status);
            const taskCounts = activeTaskData.map(row => parseInt(row.total));

            new Chart(document.getElementById('activeTaskChart'), {
              type: 'pie',
              data: {
                labels: taskLabels,
                datasets: [{
                  label: 'Active Tasks',
                  data: taskCounts,
                  backgroundColor: ['#ff6384', '#ffcd56'] // New, In Progress
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Current Active Tasks (All Projects)'
                  }
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
