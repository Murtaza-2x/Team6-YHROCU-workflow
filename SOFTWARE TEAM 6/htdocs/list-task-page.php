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

$title = "List Tasks";
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
      -- For a user, we can use an INNER JOIN to ensure tasks actually have an assigned user row
      JOIN task_assigned_users AS tau
        ON t.id = tau.task_id
      JOIN users AS u
        ON tau.user_id = u.id
      -- Task's creator (still LEFT JOIN is okay)
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
        echo "<table>"
          . "  <tr>"
          . "    <th>ID</th>"
          . "    <th>Subject</th>"
          . "    <th>Project</th>"
          . "    <th>Assignee</th>"
          . "    <th>Status</th>"
          . "    <th>Priority</th>"
          . "  </tr>";

        while ($row = $result->fetch_assoc()) {

          echo "<tr>
        <td>" . $row["id"] . "</td>
        <td class='VIEW-TASK'><a href=\"view-task-page.php?id=" . $row["id"] . "\" title=\"Detailed view\">" . $row["subject"] . "<a></td>
        <td>" . $row["project"] . "</td>
        <td>" . (isset($row["creator_name"]) ? $row["creator_name"] : "") . "</td>
        <td>" . $row["status"] . "</td>
        <td>" . $row["priority"] . "</td>
      </tr>";
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