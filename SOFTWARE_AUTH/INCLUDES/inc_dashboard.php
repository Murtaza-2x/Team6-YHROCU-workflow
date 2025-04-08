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

  <link href="CSS/filter_styles.css" rel="stylesheet">
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

    $projectSummary = [];
    $activeTaskStatusBreakdown = [];
    $userPriorityBreakdown = [];
    $userStatusBreakdown = [];

    $filterPriority = $_GET['priority'] ?? 'All';
    $filterStart = $_GET['start_date'] ?? '';
    $filterEnd = $_GET['end_date'] ?? '';

    if (has_role('User')) {
      // Pie Chart: Task priorities
      $sql = "SELECT priority, COUNT(*) AS total 
                FROM tasks 
                JOIN task_assigned_users tau ON tasks.id = tau.task_id
                WHERE tau.user_id = ? AND status IN ('New', 'In Progress', 'Complete', 'Completed')
                GROUP BY priority";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $userId);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {
        $userPriorityBreakdown[] = $row;
      }

      // Bar Chart + Summary Pills: Task statuses
      $sql2 = "SELECT 
                    CASE 
                        WHEN TRIM(LOWER(status)) = 'new' THEN 'New'
                        WHEN TRIM(LOWER(status)) = 'in progress' THEN 'In Progress'
                        WHEN TRIM(LOWER(status)) IN ('complete', 'completed') THEN 'Completed'
                        ELSE 'Other'
                    END AS status,
                    COUNT(*) AS total
                FROM tasks 
                JOIN task_assigned_users tau ON tasks.id = tau.task_id
                WHERE tau.user_id = ?
                GROUP BY status";
      $stmt2 = $conn->prepare($sql2);
      $stmt2->bind_param("s", $userId);
      $stmt2->execute();
      $result2 = $stmt2->get_result();
      while ($row = $result2->fetch_assoc()) {
        $userStatusBreakdown[] = $row;
      }
    } elseif (is_Staff()) {
      // Manager/Admin dashboard logic
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

        <!-- STAFF DASHBOARD -->
        <?php if (has_role('User')) : ?>
          <div class="MANAGER-CHART-WRAPPER">
            <form method="GET" class="MANAGER-FILTER-FORM">
              <label for="priority">Priority:</label>
              <select name="priority" id="priority">
                <option value="All" <?php echo $filterPriority === 'All' ? 'selected' : '' ?>>All</option>
                <option value="Urgent" <?php echo $filterPriority === 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                <option value="Moderate" <?php echo $filterPriority === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
                <option value="Low" <?php echo $filterPriority === 'Low' ? 'selected' : '' ?>>Low</option>
              </select>

              <label for="start_date">From:</label>
              <input type="date" name="start_date" value="<?php echo htmlspecialchars($filterStart) ?>">

              <label for="end_date">To:</label>
              <input type="date" name="end_date" value="<?php echo htmlspecialchars($filterEnd) ?>">

              <button type="submit">Apply Filters</button>
            </form>

            <!-- CHARTS ROW -->
            <div class="CHARTS-ROW">
              <!-- BAR CHART -->
              <div class="DASH-CHART-BOX">
                <canvas id="userPriorityChart"></canvas>
              </div>

              <!-- STATUS SUMMARY -->
              <div class="DASH-MIDDLE-CHART-BOX">
                <div class="TASK-STATUS-LIST">
                  <h3>Your Task Status Overview</h3>
                  <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                      <?php foreach ($userStatusBreakdown as $statusRow): ?>
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
                        }
                        ?>">
                          <strong><?php echo $statusRow['status']; ?>:</strong> <?php echo $statusRow['total']; ?>
                        </button>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </div>

              <!-- PIE CHART -->
              <div class="DASH-PIE-CHART-BOX">
                <canvas id="userPriorityChart2"></canvas>
              </div>
            </div>
          </div>

          <script>
            const statusData = <?php echo json_encode($userStatusBreakdown); ?>;
            const priorityData = <?php echo json_encode($userPriorityBreakdown); ?>;

            // Bar Chart: Priority
            new Chart(document.getElementById('userPriorityChart'), {
              type: 'bar',
              data: {
                labels: priorityData.map(row => row.priority),
                datasets: [{
                  label: 'Your Tasks by Priority',
                  data: priorityData.map(row => parseInt(row.total)),
                  backgroundColor: '#2980b9'
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Your Task Priority Summary'
                  }
                },
                scales: {
                  y: {
                    beginAtZero: true
                  }
                }
              }
            });

            // Pie Chart: Priority
            new Chart(document.getElementById('userPriorityChart2'), {
              type: 'pie',
              data: {
                labels: priorityData.map(row => row.priority),
                datasets: [{
                  label: 'Task Priority',
                  data: priorityData.map(row => parseInt(row.total)),
                  backgroundColor: ['#e74c3c', '#f39c12', '#2ecc71']
                }]
              },
              options: {
                plugins: {
                  title: {
                    display: true,
                    text: 'Your Task Priority Breakdown'
                  },
                  legend: {
                    position: 'bottom'
                  }
                }
              }
            });
          </script>

          <!-- MANAGER / ADMIN VIEW -->
        <?php elseif (is_staff()) : ?>
          <div class="MANAGER-CHART-WRAPPER">
            <form method="GET" class="MANAGER-FILTER-FORM">
              <label for="priority">Priority:</label>
              <select name="priority" id="priority">
                <option value="All" <?php echo $filterPriority === 'All' ? 'selected' : '' ?>>All</option>
                <option value="Urgent" <?php echo $filterPriority === 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                <option value="Moderate" <?php echo $filterPriority === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
                <option value="Low" <?php echo $filterPriority === 'Low' ? 'selected' : '' ?>>Low</option>
              </select>

              <label for="start_date">From:</label>
              <input type="date" name="start_date" value="<?php echo htmlspecialchars($filterStart) ?>">

              <label for="end_date">To:</label>
              <input type="date" name="end_date" value="<?php echo htmlspecialchars($filterEnd) ?>">

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
                            }
                            ?>">
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
            const filteredData = <?php echo json_encode($projectSummary ?? []); ?>;
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
            const pieData = <?php echo json_encode($activeTaskStatusBreakdown ?? []); ?>;
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