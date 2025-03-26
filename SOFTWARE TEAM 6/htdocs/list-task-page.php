<?php
/*
This file displays a list of tasks for the currently logged-in user or for all users,
depending on the user's clearance level.
It retrieves tasks along with their associated project names (from the projects table)
and displays them in a table. The project column now shows the project name as a clickable link
that takes you to view-project-page.php with the project id.
*/

$title = "ROCU: Dashboard";
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>
<?php include 'INCLUDES/inc_dashboard.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="JS/SEARCH-TABLE.js"></script>

<?php
if ($clearance === 'User') {
  $sql = "
      SELECT
          t.id,
          t.subject,
          t.project_id,
          p.project_name,
          t.status,
          t.priority,
          c.username AS creator_name,
          GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
      FROM tasks AS t
      LEFT JOIN projects p ON t.project_id = p.id
      JOIN task_assigned_users AS tau ON t.id = tau.task_id
      JOIN users AS u ON tau.user_id = u.id
      LEFT JOIN users AS c ON t.created_by = c.id
      WHERE tau.user_id = {$id}
      GROUP BY t.id
  ";
} else {
  $sql = "
      SELECT
          t.id,
          t.subject,
          t.project_id,
          p.project_name,
          t.status,
          t.priority,
          c.username AS creator_name,
          GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
      FROM tasks AS t
      LEFT JOIN projects p ON t.project_id = p.id
      LEFT JOIN task_assigned_users AS tau ON t.id = tau.task_id
      LEFT JOIN users AS u ON tau.user_id = u.id
      LEFT JOIN users AS c ON t.created_by = c.id
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
      <input type="text" id="searchInput" placeholder="Search tasks...">
      <button type="button" id="filterButton">Filter</button>
    </div>
    <!-- TASK SECTION FILTER END -->

    <!-- TASK SECTION LIST -->
    <div class="TASK-LIST">
      <?php
      if ($result->num_rows > 0) {
        echo "<table class='TASK-TABLE' id='TASK-TABLE'>
  <thead>
    <tr>
      <th>
        Check
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
  </thead>
  <tbody>";

        while ($row = $result->fetch_assoc()) {
          $taskId      = $row["id"];
          $subject     = $row["subject"];
          $project_id  = $row["project_id"];
          $projectName = $row["project_name"] ? htmlspecialchars($row["project_name"]) : 'N/A';
          $creator     = isset($row["creator_name"]) ? $row["creator_name"] : "";
          $status      = $row["status"];
          $priority    = $row["priority"];

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

          echo "<tr>
                <td></td>
                <td class='VIEW-TASK'>
                  <a href='view-task-page.php?id=$taskId' title='Detailed view'>$subject</a>
                </td>
                <td>
                  <a href='view-project-page.php?id=$project_id' title='View Project'>$projectName</a>
                </td>
                <td>$creator</td>
                <td>$statusPill</td>
                <td>$priorityPill</td>
              </tr>";
        }
        echo "</tbody></table>";

        if ($_SESSION["clearance"] != 'User') {
          echo "<button class='CREATE-TASK-BUTTON' onclick=\"document.location='create-task-page.php'\">Create Task</button>";
          echo "<button class='CREATE-PROJECT-BUTTON' onclick=\"document.location='create-project-page.php'\">Create Project</button>";
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