<?php
/*
view-project-logs.php

This page displays archived logs for all tasks and the project itself.
It also provides an export feature:
 - If the user calls ?id=XX&export=1, the page returns a CSV file.
 - Otherwise, it displays the logs in HTML via inc_projectlogsview.php.
*/

$title = "ROCU: Project Logs";
include 'INCLUDES/inc_connect.php';

// VALIDATE PROJECT ID
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($project_id <= 0) {
    echo "Invalid project ID.";
    exit;
}

// FETCH TASK CHANGE LOGS
// Get all task IDs linked to this project
$sql_tasks = "SELECT id FROM tasks WHERE project_id = $project_id";
$result_tasks = $conn->query($sql_tasks);

$task_ids = [];
if ($result_tasks && $result_tasks->num_rows > 0) {
    while ($row = $result_tasks->fetch_assoc()) {
        $task_ids[] = $row['id'];
    }
}

// If tasks exist, get archive logs for them
if (!empty($task_ids)) {
    $ids = implode(',', $task_ids);
    $sql_logs = "
        SELECT a.*, u.username
        FROM archive a
        LEFT JOIN users u ON a.edited_by = u.id
        WHERE a.task_id IN ($ids)
        ORDER BY a.archived_at DESC
    ";
} else {
    $sql_logs = "SELECT * FROM archive WHERE 0";
}

$result_logs = $conn->query($sql_logs);

$logsArray = [];
if ($result_logs && $result_logs->num_rows > 0) {
    while ($row = $result_logs->fetch_assoc()) {
        $logsArray[] = $row;
    }
}
$logCount = count($logsArray);


// FETCH PROJECT CHANGE LOGS

$projectLogs = [];
$sql_projLogs = "
    SELECT pa.*, u.username
    FROM project_archive pa
    LEFT JOIN users u ON pa.edited_by = u.id
    WHERE pa.project_id = $project_id
    ORDER BY pa.archived_at DESC
";

$result_projLogs = $conn->query($sql_projLogs);
if ($result_projLogs && $result_projLogs->num_rows > 0) {
    while ($row = $result_projLogs->fetch_assoc()) {
        $projectLogs[] = $row;
    }
}

// HANDLE EXPORT (CSV)
if (isset($_GET['export']) && $_GET['export'] == 1) {
    // Fetch the project name for the export filename
    $sql_projName = "SELECT project_name FROM projects WHERE id = $project_id";
    $projNameResult = $conn->query($sql_projName);
    $projectName = "ProjectLogs";
    if ($projNameResult && $projNameResult->num_rows > 0) {
        $pRow = $projNameResult->fetch_assoc();
        $projectName = preg_replace("/[^A-Za-z0-9_-]/", "_", $pRow['project_name']) . "_logs";
    }

    // Output headers for download
    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"{$projectName}.csv\"");

    $out = fopen("php://output", "w");

    // Task logs
    fputcsv($out, ["-- TASK LOGS --"]);
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

    // Project logs
    fputcsv($out, []);
    fputcsv($out, ["-- PROJECT LOGS --"]);
    fputcsv($out, ["Edited By", "Archived At", "Project Name", "Status", "Priority", "Due Date", "Description"]);

    foreach ($projectLogs as $log) {
        $editor      = $log['username']     ?? 'Unknown';
        $archivedAt  = $log['archived_at'];
        $projName    = $log['project_name'];
        $status      = $log['status'];
        $priority    = $log['priority'];
        $dueDate     = $log['due_date'];
        $description = $log['description'];

        fputcsv($out, [$editor, $archivedAt, $projName, $status, $priority, $dueDate, $description]);
    }

    fclose($out);
    exit;
}
include 'INCLUDES/inc_header.php';
include 'INCLUDES/inc_projectlogsview.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
