<?php
/*
This file displays a list of tasks for the currently logged-in user or for all users, depending on the user's clearance level:
1. It starts by including the connection, header, and dashboard files.
2. Fetches session variables for clearance, user ID, and username, then outputs a welcome message.
3. Based on the user’s clearance:
   - If the user is “user” level, it shows only tasks assigned to them.
   - Otherwise, it shows all tasks in the system.
4. The results are fetched from the `tasks` table and displayed in a table format.
5. If the user is not a regular user (i.e., has higher clearance), they also see an “Add Task” button to create a new task.
*/

$title = "ROCU: Dashboard";
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>
<?php include 'INCLUDES/inc_dashboard.php'; ?>


<?php
if ($clearance === 'User') {
  $sql = "
      SELECT
          t.id,
          t.subject,
          t.project,
          t.status,
          t.priority,
          c.username AS creator_name,
          GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
      FROM tasks AS t
      JOIN task_assigned_users AS tau
        ON t.id = tau.task_id
      JOIN users AS u
        ON tau.user_id = u.id
      LEFT JOIN users AS c
        ON t.created_by = c.id
      WHERE tau.user_id = {$id}
      GROUP BY t.id
  ";
} else {
  $sql = "
      SELECT
          t.id,
          t.subject,
          t.project,
          t.status,
          t.priority,
          c.username AS creator_name,
          GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
      FROM tasks AS t
      LEFT JOIN task_assigned_users AS tau
        ON t.id = tau.task_id
      LEFT JOIN users AS u
        ON tau.user_id = u.id
      LEFT JOIN users AS c
        ON t.created_by = c.id
      GROUP BY t.id
  ";
}

$result = $conn->query($sql);
?>

<!-- TASK SECTION -->
<div class="TASK-CONTENT">
  <div class="TASK-HEADER">
    <p class="TASK-HEADER-1">Task List</p>
    <p class="TASK-HEADER-2">(5)</p>
  </div>

  <!-- TASK SECTION AREA -->
  <div class="TASK-AREA">

    <!-- TASK SECTION FILTER -->
    <div class="TASK-FILTER">
      <input type="text" placeholder="Search tasks...">
      <button>Filter</button>
    </div>
    <!-- TASK SECTION FILTER END -->

    <!-- TASK SECTION LIST -->
    <div class="TASK-LIST">

      <?php
      if ($result->num_rows > 0) {
        echo "<table class='TASK-TABLE'>
  <thead>
  <tr>
    <th>
      ID
        <img src='ICONS/filter-filled.png' class='filter'/>
    </th>
    <th>
      Subject
        <img src='ICONS/filter-filled.png' class='filter'/>
    </th>
    <th>
      Project
        <img src='ICONS/filter-filled.png' class='filter'/>
    </th>
    <th>
      Assignee
        <img src='ICONS/filter-filled.png' class='filter'/>
    </th>
    <th>
      Status
        <img src='ICONS/filter-filled.png' class='filter'/>
    </th>
    <th>
      Priority
        <img src='ICONS/filter-filled.png' class='filter'/>
    </th>
  </tr>
  </thead>";

        while ($row = $result->fetch_assoc()) {
          $taskId   = $row["id"];
          $subject  = $row["subject"];
          $project  = $row["project"];
          $creator  = isset($row["creator_name"]) ? $row["creator_name"] : "";
          $status   = $row["status"];
          $priority = $row["priority"];

          // Build a single pill for Status
          $statusPill = '';
          switch ($status) {
            case 'New':
              $statusPill = "<button class='PILL-NEW' id='PILL-ACTIVE'>New</button>";
              break;
            case 'In Progress':
              $statusPill = "<button class='PILL-IN-PROGRESS' id='PILL-ACTIVE'>In Progress</button>";
              break;
            case 'Complete':
              $statusPill = "<button class='PILL-COMPLETE' id='PILL-ACTIVE'>Complete</button>";
              break;
            default:
              $statusPill = "<button class='PILL-INACTIVE'>$status</button>";
              break;
          }

          $priorityPill = '';
          switch ($priority) {
            case 'Urgent':
              $priorityPill = "<button class='PILL-URGENT' id='PILL-ACTIVE'>Urgent</button>";
              break;
            case 'Moderate':
              $priorityPill = "<button class='PILL-MODERATE' id='PILL-ACTIVE'>Moderate</button>";
              break;
            case 'Low':
              $priorityPill = "<button class='PILL-LOW' id='PILL-ACTIVE'>Low</button>";
              break;
            default:
              $priorityPill = "<button id='PILL-INACTIVE'>$priority</button>";
              break;
          }

          echo "<tbody>
              <tr>
                <td>$taskId</td>
                <td class='VIEW-TASK'>
                  <a href='view-task-page.php?id=$taskId' title='Detailed view'>$subject</a>
                </td>
                <td>$project</td>
                <td>$creator</td>
                <td>$statusPill</td>
                <td>$priorityPill</td>
              </tr>
                </tbody>
            ";
        }
        echo "</table>";

        if ($_SESSION["clearance"] != 'User') {
          echo "<button class='CREATE-TASK-BUTTON' onclick=\"document.location='create-task-page.php'\">Create Task</button>";
        }
      } else {
        echo "<h1 class='USER-MESSAGE'>There are No Tasks Assigned to you!</h1>";
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