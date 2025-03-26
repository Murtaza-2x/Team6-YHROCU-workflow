<?php
/*
This file displays the log history for a given task.
It retrieves archived versions from the archive table and displays them using the same layout classes as the view-task page.
Each log entry shows who edited the task, the time it was archived, the original creation timestamp, and the task details.
*/

$title = "ROCU: Task Logs";
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid task ID.";
    exit;
}

$sql_logs = "
    SELECT a.*, u.username
    FROM archive a
    LEFT JOIN users u ON a.edited_by = u.id
    WHERE a.task_id = $id
    ORDER BY a.archived_at DESC
";
$result_logs = $conn->query($sql_logs);
$logCount = $result_logs ? $result_logs->num_rows : 0;
?>

<?php
include 'INCLUDES/inc_logsview.php';

include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
