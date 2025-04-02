<?php
/*
This file displays the log history for a given task in HTML, but can also export
those logs as a CSV file if "?export=1" is passed. Each log entry shows:
- Who edited the task (username)
- The time it was archived
- Original creation timestamp, subject, status, priority, and description.
*/

$title = "ROCU: Task Logs";
include 'INCLUDES/inc_connect.php';

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

$logsArray = [];
if ($result_logs && $result_logs->num_rows > 0) {
    while ($row = $result_logs->fetch_assoc()) {
        $logsArray[] = $row;
    }
}
$logCount = count($logsArray);

if (isset($_GET['export']) && $_GET['export'] == 1) {
    $sql_taskName = "SELECT subject FROM tasks WHERE id = $id";
    $taskNameResult = $conn->query($sql_taskName);
    $taskName = "TaskLogs";
    if ($taskNameResult && $taskNameResult->num_rows > 0) {
        $tRow = $taskNameResult->fetch_assoc();
        $taskName = preg_replace("/[^A-Za-z0-9_-]/", "_", $tRow['subject']) . "_logs";
    }

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"{$taskName}.csv\"");

    $out = fopen("php://output", "w");

    fputcsv($out, ["Edited By", "Archived At", "Created At", "Subject", "Status", "Priority", "Description"]);

    foreach ($logsArray as $log) {
        $editor      = $log['username']     ?? 'Unknown';
        $archivedAt  = $log['archived_at'];
        $createdAt   = $log['created_at'];
        $subject     = $log['subject'];
        $status      = $log['status'];
        $priority    = $log['priority'];
        $description = $log['description'];

        fputcsv($out, [$editor, $archivedAt, $createdAt, $subject, $status, $priority, $description]);
    }
    fclose($out);
    exit;
}

include 'INCLUDES/inc_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

include 'INCLUDES/inc_tasklogsview.php';

include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
