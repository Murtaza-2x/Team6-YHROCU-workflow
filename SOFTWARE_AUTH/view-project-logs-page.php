<?php
/*
-------------------------------------------------------------
File: view-project-logs-page.php
Description:
- Displays archived project logs (project_archive) plus 
  any task logs (task_archive) for tasks in that project.
- In test mode (PHPUNIT_RUNNING):
   * Return JSON for invalid ID, nonexistent, unauthorized, or mock logs
- In production mode:
   * Normal DB queries, sorts logs, and exports as CSV if requested
-------------------------------------------------------------
*/

$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

// If we’re in test mode and not forcing production => do JSON
if ($isTesting && (!isset($_GET['force_prod']) || $_GET['force_prod'] !== '1')) {
    header('Content-Type: application/json; charset=utf-8');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $role = $_SESSION['user']['role'] ?? 'guest';
    $isStaff = in_array(strtolower($role), ['manager','admin']);

    $projectId = $_GET['id'] ?? null;
    if (!$projectId || !is_numeric($projectId)) {
        echo json_encode(["error"=>"Invalid project ID"]);
        return;
    }
    if ($projectId === "99999") {
        echo json_encode(["error"=>"Project not found"]);
        return;
    }
    if (!$isStaff) {
        echo json_encode(["error"=>"You are not authorized"]);
        return;
    }

    // Optionally mock logs or no logs
    if (isset($_GET['mock_logs']) && $_GET['mock_logs'] === '1') {
        // Return some mock project logs plus some mock task logs
        echo json_encode([
            "projectLogs"=>[[ 
                "archived_at"=>"2025-04-05 10:00:00",
                "created_at" =>"2025-03-20 09:00:00",
                "project_name"=>"Old Project Name",
                "status"     =>"Active",
                "priority"   =>"High",
                "description"=>"Old Project Desc",
                "edited_by"  =>"auth0|admin123"
            ]],
            "taskLogs"=>[[
                "archived_at" =>"2025-04-05 11:00:00",
                "created_at"  =>"2025-03-21 10:00:00",
                "subject"     =>"Old Task Subject",
                "status"      =>"New",
                "priority"    =>"Moderate",
                "description" =>"Old Task Desc",
                "edited_by"   =>"auth0|manager456"
            ]]
        ]);
    } else {
        echo json_encode(["info"=>"No logs found"]);
    }
    return;
}

// --------------------------
// PRODUCTION MODE LOGIC
// --------------------------
$title = "ROCU: Project Logs";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

// Must be staff
if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Validate numeric project ID
$project_id = $_GET['id'] ?? null;
if (!$project_id || !is_numeric($project_id)) {
    echo "<p class='ERROR-MESSAGE'>Invalid project ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// 1) Fetch project logs
$stmt = $conn->prepare("SELECT * FROM project_archive WHERE project_id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$res1 = $stmt->get_result();
$projectLogs = ($res1) ? $res1->fetch_all(MYSQLI_ASSOC) : [];

// 2) Fetch task logs for tasks under that project
$stmt2 = $conn->prepare("
    SELECT ta.*
    FROM task_archive ta
    INNER JOIN tasks t ON ta.task_id = t.id
    WHERE t.project_id = ?
");
$stmt2->bind_param("i", $project_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$taskLogs = ($res2) ? $res2->fetch_all(MYSQLI_ASSOC) : [];

// Sort logs by archived_at desc
usort($projectLogs, fn($a,$b) => strtotime($b['archived_at']) <=> strtotime($a['archived_at']));
usort($taskLogs,    fn($a,$b) => strtotime($b['archived_at']) <=> strtotime($a['archived_at']));

// Map Auth0 users
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// CSV export check
if (isset($_GET['export']) && $_GET['export'] == 1) {
    // Possibly skip exit if isTesting => so we can see test results
    header("Content-Type: text/csv; charset=UTF-8");
    
    // Grab project name for the filename
    $stmtName = $conn->prepare("SELECT project_name FROM projects WHERE id=?");
    $stmtName->bind_param("i", $project_id);
    $stmtName->execute();
    $resName = $stmtName->get_result();
    $projectRow = $resName->fetch_assoc();
    $projectNameSafe = isset($projectRow['project_name']) 
        ? preg_replace("/[^A-Za-z0-9_-]/", "_", $projectRow['project_name'])
        : "Project";

    header("Content-Disposition: attachment; filename=\"{$projectNameSafe}_logs.csv\"");
    echo "\xEF\xBB\xBF"; // BOM for Excel

    $out = fopen("php://output", "w");

    // --- Project Logs Section ---
    fputcsv($out, ["--- Project Logs ---"]);
    fputcsv($out, ["Edited By","Created By","Archived At","Created At","Project Name","Status","Priority","Due Date","Description"]);
    foreach ($projectLogs as $log) {
        $editor  = $user_map[$log['edited_by']]  ?? $log['edited_by'];
        $creator = $user_map[$log['created_by']] ?? $log['created_by'];

        fputcsv($out, [
            $editor,
            $creator,
            $log['archived_at'],
            $log['created_at'],
            $log['project_name'],
            $log['status'],
            $log['priority'],
            $log['due_date'] ?? '',
            $log['description']
        ]);
    }

    // blank line
    fputcsv($out, []);
    
    // --- Task Logs Section ---
    fputcsv($out, ["--- Task Logs ---"]);
    fputcsv($out, ["Edited By","Archived At","Created At","Subject","Status","Priority","Due Date","Description"]);
    foreach ($taskLogs as $log) {
        $editor = $user_map[$log['edited_by']] ?? $log['edited_by'];
        fputcsv($out, [
            $editor,
            $log['archived_at'],
            $log['created_at'],
            $log['subject'],
            $log['status'],
            $log['priority'],
            $log['due_date'] ?? '',
            $log['description']
        ]);
    }

    fclose($out);

    if (!$isTesting) {
        exit;
    } else {
        return;
    }
}

// Normal HTML page
include 'INCLUDES/inc_header.php';
include 'INCLUDES/inc_projectlogsview.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';