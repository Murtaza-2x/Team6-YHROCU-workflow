<?php
/*
This file provides a detailed view of a single task by retrieving its details from the `tasks` table:
1. It includes the database connection and header files for the page.
2. The `id` of the task is retrieved from the query string and used to run a SELECT query against the `tasks` table.
3. If a matching record is found, the task’s details (ID, subject, project, assignee, status, priority) are displayed in an HTML table.
4. Users with a higher clearance (not ‘user’) can also see an “Edit” button that links to the edit page for that task.
5. A “Back” button is available to return to the list of tasks.
*/

$title = "ROCU: Task View";
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>

<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
      echo "Invalid task ID.";
      exit;
}

$sql = "
  SELECT t.*,
         GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
  FROM tasks t
  LEFT JOIN task_assigned_users tau ON t.id = tau.task_id
  LEFT JOIN users u ON tau.user_id = u.id
  WHERE t.id = $id
  GROUP BY t.id
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row         = $result->fetch_assoc();
    $subject     = $row['subject'];
    $project     = $row['project'];
    $status      = $row['status'];
    $priority    = $row['priority'];
    $description = $row['description'];
    $assignedUsers = $row['assigned_users'] ?? 'No Users Assigned'; 
}

?>

<?php include 'INCLUDES/inc_taskview.php'; ?>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>