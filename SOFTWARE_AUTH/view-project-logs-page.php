<?php
/*
-------------------------------------------------------------
File: view-project-logs-page.php
Description:
- Displays and exports both project logs and task logs assigned to the project.
- Combined into one timeline sorted by date.
- Exportable to CSV.
-------------------------------------------------------------
*/

$title = "ROCU: Project Logs";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

$project_id = $_GET['id'] ?? null;
if (!$project_id || !is_numeric($project_id)) {
    echo "<p class='ERROR-MESSAGE'>Invalid project ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Fetch Project Logs
$stmt = $conn->prepare("SELECT pa.*, 'Project Log' AS log_type FROM project_archive pa WHERE pa.project_id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$res1 = $stmt->get_result();
$projectLogs = ($res1) ? $res1->fetch_all(MYSQLI_ASSOC) : [];

// Fetch Task Logs linked to this Project
$stmt2 = $conn->prepare("
    SELECT ta.*, 'Task Log' AS log_type
    FROM task_archive ta
    INNER JOIN tasks t ON ta.task_id = t.id
    WHERE t.project_id = ?
");
$stmt2->bind_param("i", $project_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$taskLogs = ($res2) ? $res2->fetch_all(MYSQLI_ASSOC) : [];

// Merge + Sort Logs by archived_at DESC
$logsArray = array_merge($projectLogs, $taskLogs);
usort($logsArray, fn($a, $b) => strtotime($b['archived_at']) <=> strtotime($a['archived_at']));

$logCount = count($logsArray);

// CSV EXPORT
if (isset($_GET['export']) && $_GET['export'] == 1) {
    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"project_{$project_id}_logs.csv\"");
    $out = fopen("php://output", "w");

    fputcsv($out, ["Log Type", "Edited By", "Archived At", "Created At", "Subject", "Status", "Priority", "Description"]);

    foreach ($logsArray as $log) {
        fputcsv($out, [
            $log['log_type'],
            $log['edited_by'] ?? 'Unknown',
            $log['archived_at'],
            $log['created_at'],
            $log['subject'],
            $log['status'],
            $log['priority'],
            $log['description']
        ]);
    }

    fclose($out);
    exit;
}

// Auth0 User List for Mapping
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

require_once __DIR__ . '/INCLUDES/inc_header.php';

include 'INCLUDES/inc_projectlogsview.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
