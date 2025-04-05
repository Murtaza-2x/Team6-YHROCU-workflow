<?php
/*
-------------------------------------------------------------
File: view-task-logs-page.php
Description:
- Displays archived task logs.
- Allows exporting logs to CSV.
- Shows:
    > Who edited the task (via Auth0).
    > When it was archived.
    > Original creation timestamp (created_at).
    > Subject, Status, Priority, Due Date, Description.
-------------------------------------------------------------
*/

$title = "ROCU: Task Logs";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

// Restrict access to staff members only
if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Validate task ID
$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($taskId <= 0) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Load archived logs
$stmt = $conn->prepare("SELECT a.*, a.edited_by as user_id FROM task_archive a WHERE a.task_id = ? ORDER BY a.archived_at DESC");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$res = $stmt->get_result();
$logsArray = $res->fetch_all(MYSQLI_ASSOC);
$logCount = count($logsArray);

// Load Auth0 users for editor name resolution
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] == 1) {
    $stmtName = $conn->prepare("SELECT subject FROM tasks WHERE id = ?");
    $stmtName->bind_param("i", $taskId);
    $stmtName->execute();
    $result = $stmtName->get_result();
    $taskNameRow = $result->fetch_assoc();
    $taskNameSafe = isset($taskNameRow['subject']) ? preg_replace("/[^A-Za-z0-9_-]/", "_", $taskNameRow['subject']) : "Task";

    header("Content-Disposition: attachment; filename=\"{$taskNameSafe}_logs.csv\"");
    echo "\xEF\xBB\xBF"; // Excel BOM

    $out = fopen("php://output", "w");

    // CSV Header
    fputcsv($out, ["Edited By", "Archived At", "Created At", "Subject", "Status", "Priority", "Due Date", "Description"]);

    // CSV Rows
    foreach ($logsArray as $log) {
        $editor      = $user_map[$log['user_id']] ?? 'Unknown';
        $archivedAt  = $log['archived_at'];
        $createdAt   = $log['created_at'];
        $subject     = $log['subject'];
        $status      = $log['status'];
        $priority    = $log['priority'];
        $description = str_replace(["\r\n", "\r", "\n"], " ", $log['description']);

        fputcsv($out, [
            $editor,
            $archivedAt,
            $createdAt,
            $subject,
            $status,
            $priority,
            $description
        ]);
    }
    fclose($out);
    exit;
}

require_once __DIR__ . '/INCLUDES/inc_header.php';
include __DIR__ . '/INCLUDES/inc_tasklogsview.php';
include __DIR__ . '/INCLUDES/inc_footer.php';
include __DIR__ . '/INCLUDES/inc_disconnect.php';
?>