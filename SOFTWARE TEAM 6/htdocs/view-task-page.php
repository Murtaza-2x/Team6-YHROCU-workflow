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

<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
      echo "Invalid task ID.";
      exit;
}

$sql = "
    SELECT t.*,
           u.username AS assigned_user
    FROM tasks AS t
    LEFT JOIN users u ON t.created_by = u.id
    WHERE t.id = $id
";
$result = $conn->query($sql);

if (!$result || $result->num_rows < 1) {
      echo "Task not found.";
      exit;
}

$row = $result->fetch_assoc();

$subject   = $row['subject'];
$project     = $row['project'];
$status      = $row['status'];
$priority    = $row['priority'];
$assignees   = $row['assigned_user'] ?? '';

?>

<?php include 'INCLUDES/inc_taskview.php'; ?>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>