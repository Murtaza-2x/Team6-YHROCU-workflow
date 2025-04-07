<?php
/*
-------------------------------------------------------------
File: view-project-logs-page.php
Description:
- Displays and exports both project logs and task logs assigned to the project.
- Displayed separately in inc_projectlogsview.php.
-------------------------------------------------------------
*/

$title = "ROCU: Project Logs";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

// Validate project ID
$project_id = $_GET['id'] ?? null;
if (!$project_id || !is_numeric($project_id)) {
    echo "<p class='ERROR-MESSAGE'>Invalid project ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Fetch project logs from database
$stmt = $conn->prepare("SELECT * FROM project_archive WHERE project_id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$res1 = $stmt->get_result();
$projectLogs = ($res1) ? $res1->fetch_all(MYSQLI_ASSOC) : [];

// Fetch task logs for tasks inside this project
$stmt2 = $conn->prepare(
    "
    SELECT ta.*
    FROM task_archive ta
    INNER JOIN tasks t ON ta.task_id = t.id
    WHERE t.project_id = ?
"
);
$stmt2->bind_param("i", $project_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$taskLogs = ($res2) ? $res2->fetch_all(MYSQLI_ASSOC) : [];

// Sort logs by archived_at date
usort(
    $projectLogs, function ($a, $b) {
        return strtotime($b['archived_at']) <=> strtotime($a['archived_at']);
    }
);

usort(
    $taskLogs, function ($a, $b) {
        return strtotime($b['archived_at']) <=> strtotime($a['archived_at']);
    }
);

// Map Auth0 users to their nicknames or emails
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] == 1) {
    header("Content-Type: text/csv; charset=UTF-8");
    
    // Fetch project name safely for the filename
    $stmtName = $conn->prepare("SELECT project_name FROM projects WHERE id = ?");
    $stmtName->bind_param("i", $project_id);
    $stmtName->execute();
    $resName = $stmtName->get_result();
    $projectRow = $resName->fetch_assoc();
    $projectNameSafe = isset($projectRow['project_name']) ? preg_replace("/[^A-Za-z0-9_-]/", "_", $projectRow['project_name']) : "Project";

    // Set CSV headers
    header("Content-Disposition: attachment; filename=\"{$projectNameSafe}_logs.csv\"");
    $out = fopen("php://output", "w");

    // Project Logs Section
    fputcsv($out, ["--- Project Logs ---"]);
    fputcsv($out, ["Edited By", "Created By", "Archived At", "Created At", "Project Name", "Status", "Priority", "Due Date", "Description"]);
    foreach ($projectLogs as $log) {
        $editor  = $user_map[$log['edited_by']] ?? $log['edited_by'];
        $creator = $user_map[$log['created_by']] ?? $log['created_by'];
        fputcsv(
            $out, [
            $editor,
            $creator,
            $log['archived_at'],
            $log['created_at'],
            $log['project_name'],
            $log['status'],
            $log['priority'],
            $log['due_date'] ?? '',
            $log['description']
            ]
        );
    }
    
    // Empty row between sections
    fputcsv($out, []);

    // Task Logs Section
    fputcsv($out, ["--- Task Logs ---"]);
    fputcsv($out, ["Edited By", "Archived At", "Created At", "Subject", "Status", "Priority", "Due Date", "Description"]);
    foreach ($taskLogs as $log) {
        $editor = $user_map[$log['edited_by']] ?? $log['edited_by'];
        fputcsv(
            $out, [
            $editor,
            $log['archived_at'],
            $log['created_at'],
            $log['subject'],
            $log['status'],
            $log['priority'],
            $log['description']
            ]
        );
    }

    fclose($out);
    exit;
}

require __DIR__ .  '/INCLUDES/inc_header.php';

// Check if the user is logged in and authorized to edit
if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

require __DIR__ .  '/INCLUDES/inc_projectlogsview.php';
require __DIR__ .  '/INCLUDES/inc_footer.php';
require __DIR__ .  '/INCLUDES/inc_disconnect.php';
?>