<?php
/*
-------------------------------------------------------------
File: view-task-logs-page.php
Description:
- Displays archived task logs
- Allows exporting logs to CSV
- Shows archived snapshots with:
    > Who edited the task
    > When it was archived
    > Task data at the time (Subject, Status, Priority, Description)
-------------------------------------------------------------
*/

$title = "ROCU: Task Logs";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($taskId <= 0) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Load logs
$stmt = $conn->prepare("SELECT a.*, a.edited_by as user_id FROM task_archive a WHERE a.task_id = ? ORDER BY a.archived_at DESC");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$res = $stmt->get_result();
$logsArray = $res->fetch_all(MYSQLI_ASSOC);
$logCount = count($logsArray);

// Load Auth0 Users
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] == 1) {

    $stmtName = $conn->prepare("SELECT subject FROM tasks WHERE id = ?");
    $stmtName->bind_param("i", $taskId);
    $stmtName->execute();
    $result = $stmtName->get_result();
    $taskNameRow = $result->fetch_assoc();
    $taskName = isset($taskNameRow['subject']) ? preg_replace("/[^A-Za-z0-9_-]/", "_", $taskNameRow['subject']) . "_logs" : "TaskLogs";

    // Excel-Friendly UTF-8 BOM
    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"{$taskName}.csv\"");
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    $out = fopen("php://output", "w");

    // CSV Header
    fputcsv($out, ["Edited By", "Archived At", "Created At", "Subject", "Status", "Priority", "Description"]);

    // Rows
    foreach ($logsArray as $log) {
        $editor = $user_map[$log['user_id']] ?? 'Unknown';

        // Clean data
        $archivedAt  = $log['archived_at'];
        $createdAt   = $log['created_at'];
        $subject     = $log['subject'];
        $status      = $log['status'];
        $priority    = $log['priority'];
        $description = str_replace(["\r\n", "\r", "\n"], " ", $log['description']); // Avoid line breaks breaking rows

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

include 'INCLUDES/inc_tasklogsview.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
