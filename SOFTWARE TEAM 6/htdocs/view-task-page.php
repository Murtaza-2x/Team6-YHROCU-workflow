<?php
/*
This file provides a detailed view of a single task by retrieving its details from the `tasks` table:
1. It includes the database connection and header files for the page.
2. The `id` of the task is retrieved from the query string and used to run a SELECT query against the `tasks` table.
3. If a matching record is found, the task’s details (ID, subject, project, assignee, status, priority) are displayed in an HTML table.
4. Users with a higher clearance (not ‘user’) can also see an “Edit” button that links to the edit page for that task.
5. A “Back” button is available to return to the list of tasks.
*/

$title = "Detailed Task View";
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>
<?php include 'INCLUDES/inc_taskview.php'; ?>

<?php
$id = $_GET['id'];

if ($clearance == 'user') {
  $sql = "
  SELECT t.id,
         t.subject,
         t.project,
         t.status,
         t.priority,
         u.username AS creator_name
  FROM tasks AS t
  LEFT JOIN users AS u
    ON t.created_by = u.id
  WHERE t.assignee = " . $id;
} else {
  $sql = "
    SELECT t.id,
           t.subject,
           t.project,
           t.status,
           t.priority,
           u.username AS creator_name
    FROM tasks AS t
    LEFT JOIN users AS u
      ON t.created_by = u.id
  ";
}

$result = $conn->query($sql);
?>

<!-- TASK SECTION -->
<div class="TASK-CONTENT">

  <!-- TASK SECTION AREA -->
  <div class="TASK-AREA">
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

        $row = $result->fetch_assoc();

          echo "<tr>
        <td>" . $row["id"] . "</td>
        <td class='VIEW-TASK'><a href=\"view-task-page.php?id=" . $row["id"] . "\" title=\"Detailed view\">" . $row["subject"] . "<a></td>
        <td>" . $row["project"] . "</td>
        <td>" . (isset($row["creator_name"]) ? $row["creator_name"] : "") . "</td>
        <td>" . $row["status"] . "</td>
        <td>" . $row["priority"] . "</td>
      </tr>";
        echo "</table>";

        if ($_SESSION["clearance"] != 'user') {
          echo "<button onclick=\"document.location='edit-task-page.php?id=" . $row["id"] . "'\">Edit</button><br>";
        }
        echo "<button onclick=\"document.location='list-task-page.php'\">Back</button>";
      } else {
        echo "0 results";
      }
      ?>

      <?php include 'INCLUDES/inc_footer.php'; ?>
      <?php include 'INCLUDES/inc_disconnect.php'; ?>