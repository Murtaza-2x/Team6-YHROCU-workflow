<?php
session_start();  // Start the session to access session variables

/*
This file displays a list of tasks for the currently logged-in user or for all users, depending on the user's clearance level:
1. It includes the connection, header, and dashboard files.
2. Fetches session variables for clearance, user ID, and username, then outputs a welcome message.
3. Based on the user’s clearance:
   - If the user is “User” level, it shows only tasks assigned to them.
   - Otherwise, it shows all tasks in the system.
4. The results are fetched from the `tasks` table (with a join to the `projects` table) and displayed in a table format.
5. If the user is not a regular user (i.e., has higher clearance), they also see an “Add Task” button to create a new task.
*/

// Check if session variables are set
if (!isset($_SESSION['id'])) {
  // Redirect to login if not set
  header("Location: login.php");
  exit();
}

$id = $_SESSION['id'];  // Get the user ID from the session
$title = "ROCU: Dashboard";
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>
<?php include 'INCLUDES/inc_dashboard.php'; ?>

<?php
// Get search input (sanitized)
$search = isset($_GET['search']) ? trim($conn->real_escape_string($_GET['search'])) : '';

// Initialize the WHERE clause for the query
$whereClause = ($clearance === 'User') ? "WHERE tau.user_id = {$id}" : "";

// Add search functionality for different fields
if (!empty($search)) {
    // Numeric search for task ID
    if (is_numeric($search)) {
        $whereClause .= " AND t.id = $search"; // Exact match for task ID
    } else {
        // String search for subject, project, assignee, status, priority
        $whereClause .= " AND (
            t.subject LIKE '%$search%' 
            OR p.project_name LIKE '%$search%' 
            OR c.username LIKE '%$search%' 
            OR t.status LIKE '%$search%' 
            OR t.priority LIKE '%$search%' 
            OR u.username LIKE '%$search%'
        )";
    }
}

// Construct the query with filtering
$sql = "
    SELECT
        t.id,
        t.subject,
        t.project_id,
        p.project_name,
        t.status,
        t.priority,
        c.username AS creator_name,
        u.username AS assigned_user
    FROM tasks AS t
    INNER JOIN projects p ON t.project_id = p.id
    INNER JOIN users c ON t.created_by = c.id
    INNER JOIN task_assigned_users tau ON t.id = tau.task_id
    INNER JOIN users u ON tau.user_id = u.id
    $whereClause
    GROUP BY t.id
";

$result = $conn->query($sql);
?>

<!-- TASK SECTION -->
<div class="TASK-CONTENT">
  <div class="TASK-HEADER">
    <p class="TASK-HEADER-1">Task List</p>
    <p class="TASK-HEADER-2">(<?php echo $result->num_rows; ?>)</p>
  </div>

  <!-- TASK SECTION AREA -->
  <div class="TASK-AREA">

    <!-- TASK SECTION FILTER -->
    <div class="TASK-FILTER">
      <form method="GET" action="">
        <input type="text" name="search" placeholder="Search tasks..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Filter</button>
      </form>
    </div>
    <!-- TASK SECTION FILTER END -->

    <!-- TASK SECTION LIST -->
    <div class="TASK-LIST">
      <?php
      if ($result->num_rows > 0) {
        echo "<table class='TASK-TABLE'>
  <thead>
    <tr>
      <th>ID</th>
      <th>Subject</th>
      <th>Project</th>
      <th>Assignee</th>
      <th>Status</th>
      <th>Priority</th>
    </tr>
  </thead>";

        // It's better to have one <tbody> wrapping all rows.
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
          $taskId      = $row["id"];
          $subject     = htmlspecialchars($row["subject"]);
          $projectName = $row["project_name"] ? htmlspecialchars($row["project_name"]) : 'N/A';
          $creator     = isset($row["creator_name"]) ? htmlspecialchars($row["creator_name"]) : "";
          $status      = $row["status"];
          $priority    = $row["priority"];
          $assignedUser = isset($row["assigned_user"]) ? htmlspecialchars($row["assigned_user"]) : "";

          // Status Pill
          $statusPill = match ($status) {
            'New' => "<button class='PILL-NEW'>New</button>",
            'In Progress' => "<button class='PILL-IN-PROGRESS'>In Progress</button>",
            'Complete' => "<button class='PILL-COMPLETE'>Complete</button>",
            default => "<button class='PILL-INACTIVE'>$status</button>",
          };

           // Build priority pill.
          $priorityPill = match ($priority) {
            'Urgent' => "<button class='PILL-URGENT'>Urgent</button>",
            'Moderate' => "<button class='PILL-MODERATE'>Moderate</button>",
            'Low' => "<button class='PILL-LOW'>Low</button>",
            default => "<button class='PILL-INACTIVE'>$priority</button>",
          };

          echo "<tr>
                <td>$taskId</td>
                <td><a href='view-task-page.php?id=$taskId'>$subject</a></td>
                <td>$projectName</td>
                <td>$assignedUser</td>
                <td>$statusPill</td>
                <td>$priorityPill</td>
              </tr>";
        }
        echo "</tbody></table>";

        if ($_SESSION["clearance"] != 'User') {
          echo "<button class='CREATE-TASK-BUTTON' onclick=\"document.location='create-task-page.php'\">Create Task</button>";
        }
      } else {
        echo "<h1 class='USER-MESSAGE'>No tasks found!</h1>";
      }
      ?>
    </div>
    <!-- TASK SECTION LIST END -->
  </div>
  <!-- TASK SECTION AREA END -->
</div>
<!-- TASK SECTION END -->

</div>
<!-- MIDDLE SECTION END -->

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>
