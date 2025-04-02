<?php
/*
Dashboard page shown after login. Displays different content based on the user's clearance level:
- For regular users (Staff), it shows a pie chart with their active tasks by priority.
- For managers and admins, it shows:
  1. A bar chart filtered by priority and due dates for all projects.
  2. A pie chart showing tasks by status (New, In Progress, Completed).
  3. A summary box showing task counts next to their status labels.
*/
?>

<head>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title><?php echo $title; ?></title>

  <link href="CSS/dashboard_styles.css" rel="stylesheet">
  <link href="CSS/tasklist_styles.css" rel="stylesheet">
</head>

<!-- DASHBOARD SECTION -->
<div class="DASH-CONTENT">
  <div class="DASH-HEADER">
    <p class="DASH-HEADER-1">Dashboard -</p>
    <?php
    if (!is_logged_in()) {
      header("Location: index.php?error=login_required");
      exit;
    }

    $user = $_SESSION['user'];
    $userId = $user['user_id'];
    $clearance = $user['role'];
    $displayName = $user['nickname'] ?? 'User';

    $clearanceLabel = match ($clearance) {
      'Admin' => 'Admin',
      'Manager' => 'Manager',
      default => 'Staff',
    };

    echo "<p class='DASH-HEADER-2'>Welcome {$clearanceLabel} {$displayName}</p>";

    // Variables to store chart data
    $projectSummary = [];
    $activeTaskStatusBreakdown = [];
    $userPriorityBreakdown = [];

    // Pre-define filter variables
    $filterPriority = $_GET['priority'] ?? 'All';
    $filterStart = $_GET['start_date'] ?? '';
    $filterEnd = $_GET['end_date'] ?? '';

    if (has_role('User')) {
      // Staff view - tasks by priority
      $sql = "SELECT priority, COUNT(*) AS total 
                FROM tasks 
                JOIN task_assigned_users tau ON tasks.id = tau.task_id
                WHERE tau.user_id = $userId AND status IN ('New', 'In Progress')
                GROUP BY priority";
      $result = $conn->query($sql);
      while ($row = $result->fetch_assoc()) {
        $userPriorityBreakdown[] = $row;
      }
    } elseif (has_role('Manager') || has_role('Admin')) {
      // Bar chart data for project status
      $sql = "SELECT status, COUNT(*) AS total FROM projects WHERE 1";
      if ($filterPriority !== 'All') {
        $sql .= " AND priority = '" . $conn->real_escape_string($filterPriority) . "'";
      }
      if ($filterStart && $filterEnd) {
        $sql .= " AND due_date BETWEEN '" . $conn->real_escape_string($filterStart) . "' AND '" . $conn->real_escape_string($filterEnd) . "'";
      }
      $sql .= " GROUP BY status";
      $result = $conn->query($sql);
      while ($row = $result->fetch_assoc()) {
        $projectSummary[] = $row;
      }

      // Pie chart + summary counts for task status
      $sqlActive = "SELECT 
                        CASE 
                            WHEN TRIM(LOWER(status)) = 'new' THEN 'New'
                            WHEN TRIM(LOWER(status)) = 'in progress' THEN 'In Progress'
                            WHEN TRIM(LOWER(status)) IN ('complete', 'completed') THEN 'Completed'
                            ELSE 'Other'
                        END AS status,
                        COUNT(*) AS total
                      FROM tasks
                      WHERE TRIM(LOWER(status)) IN ('new', 'in progress', 'complete', 'completed')
                      GROUP BY status";
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
        <?php if (has_role('User')): ?>
          <div class="USER-CHART-BOX">
            <canvas id="userPriorityChart"></canvas>
          </div>

          <script>
            const userPriorityData = <?= json_encode($userPriorityBreakdown ?? []); ?>;
            const userLabels = userPriorityData.map(row => row.priority);
            const userCounts = userPriorityData.map(row => parseInt(row.total));

            new Chart(document.getElementById('userPriorityChart'), {
              type: 'pie',
              data: {
                labels: userLabels,
                datasets: [{
                  label: 'Your Active Tasks by Priority',
                  data: userCounts,
                  backgroundColor: ['#e74c3c', '#f39c12', '#2ecc71']
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Your Active Tasks by Priority'
                  },
                  legend: {
                    position: 'bottom'
                  }
                }
              }
            });
          </script>

          <!-- MANAGER / ADMIN VIEW -->
        <?php elseif (is_staff()): ?>
          <div class="MANAGER-CHART-WRAPPER">
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

            <!-- CHARTS ROW -->
            <div class="CHARTS-ROW">
              <!-- BAR CHART -->
              <div class="DASH-CHART-BOX">
                <canvas id="managerFilteredChart"></canvas>
              </div>

              <!-- TASK SUMMARY BOX -->
              <div class="DASH-MIDDLE-CHART-BOX">
                <div class="TASK-STATUS-LIST">
                  <h3>Task Status Overview</h3>
                  <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                      <?php foreach ($activeTaskStatusBreakdown as $statusRow): ?>
                        <!-- Dynamically setting the class based on status -->
                        <button class="PILL 
                      <?php
                        switch ($statusRow['status']) {
                          case 'New':
                            echo 'PILL-NEW';
                            break;
                          case 'In Progress':
                            echo 'PILL-IN-PROGRESS';
                            break;
                          case 'Completed':
                            echo 'PILL-COMPLETE';
                            break;
                        } ?> 
                      <?php echo $statusRow['status'] == 'New' ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">
                          <strong><?php echo $statusRow['status']; ?>:</strong> <?php echo $statusRow['total']; ?>
                        </button>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </div>


              <!-- PIE CHART -->
              <div class="DASH-PIE-CHART-BOX">
                <canvas id="activeTaskChart"></canvas>
              </div>
            </div>
          </div>

          <script>
            // Bar Chart
            const filteredData = <?= json_encode($projectSummary ?? []); ?>;
            const barLabels = filteredData.map(row => row.status);
            const barValues = filteredData.map(row => parseInt(row.total));

            new Chart(document.getElementById('managerFilteredChart'), {
              type: 'bar',
              data: {
                labels: barLabels,
                datasets: [{
                  label: 'Projects',
                  data: barValues,
                  backgroundColor: '#169bcb'
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Project Status Summary'
                  }
                },
                scales: {
                  y: {
                    beginAtZero: true
                  }
                }
              }
            });

            // Pie Chart
            const pieData = <?= json_encode($activeTaskStatusBreakdown ?? []); ?>;
            const pieLabels = pieData.map(row => row.status);
            const pieValues = pieData.map(row => parseInt(row.total));

            new Chart(document.getElementById('activeTaskChart'), {
              type: 'pie',
              data: {
                labels: pieLabels,
                datasets: [{
                  label: 'Task Status',
                  data: pieValues,
                  backgroundColor: ['#ff6384', '#ffcd56', '#2ecc71']
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Task Breakdown (All Projects)'
                  },
                  legend: {
                    position: 'bottom'
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