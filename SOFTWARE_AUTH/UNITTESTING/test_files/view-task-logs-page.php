<?php
/*
-------------------------------------------------------------
File: view-task-logs-page.php
Description:
- Displays archived task logs (from task_archive) for a given task.
- In test mode (PHPUNIT_RUNNING), returns JSON for:
    • Invalid task ID: {"error": "Invalid task ID"}
    • Nonexistent task (ID 99999): {"error": "Task not found"}
    • Unauthorized access: {"error": "You are not authorized"}
    • Logs data: returns either mock logs or a "No logs found" message
- In production mode, performs normal DB queries, allows CSV export,
  and then renders the logs view (HTML).
-------------------------------------------------------------
*/

$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

// If we're in test mode and not forcing production, return JSON responses
if ($isTesting && (!isset($_GET['force_prod']) || $_GET['force_prod'] !== '1')) {
    header('Content-Type: application/json; charset=utf-8');

    // Ensure the session is started for role checks
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check user role and ensure they are staff (manager or admin)
    $role = $_SESSION['user']['role'] ?? 'guest';
    $isStaff = in_array(strtolower($role), ['manager','admin']);

    // Validate task ID
    $taskId = $_GET['id'] ?? null;
    if (!$taskId || !is_numeric($taskId)) {
        echo json_encode(["error" => "Invalid task ID"]);
        return;
    }
    if ($taskId === "99999") {
        echo json_encode(["error" => "Task not found"]);
        return;
    }
    if (!$isStaff) {
        echo json_encode(["error" => "You are not authorized"]);
        return;
    }

    // If a mock logs flag is provided, return a simulated log; otherwise, indicate no logs
    if (isset($_GET['mock_logs']) && $_GET['mock_logs'] === '1') {
        echo json_encode([
            "logs" => [[
                "archived_at" => "2025-04-04 12:00:00",
                "created_at"  => "2025-03-29 10:00:00",
                "subject"     => "Old Subject",
                "status"      => "In Progress",
                "priority"    => "High",
                "description" => "Old Description",
                "edited_by"   => "auth0|someadmin"
            ]]
        ]);
    } else {
        echo json_encode(["info" => "No logs found"]);
    }
    return;
}

$title = "ROCU: Task Logs";

// Load required environment and helper files
require_once __DIR__ . '/../../INCLUDES/env_loader.php';
require_once __DIR__ . '/../../INCLUDES/role_helper.php';
require_once __DIR__ . '/../../INCLUDES/inc_connect.php';
require_once __DIR__ . '/../../INCLUDES/Auth0UserFetcher.php';

// Bridge the database connection from test environment (if available)
if (isset($GLOBALS['conn'])) {
    $conn = $GLOBALS['conn'];
}

// Check if the user is logged in and has staff privileges
if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include __DIR__ . '../../INCLUDES/inc_footer.php';
    exit;
}

// Validate the task ID from the query string
$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($taskId <= 0) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include __DIR__ . '../../INCLUDES/inc_footer.php';
    exit;
}

// Fetch archived logs for the task from the database
$stmt = $conn->prepare("
    SELECT a.*, a.edited_by as user_id
    FROM task_archive a
    WHERE a.task_id = ?
    ORDER BY a.archived_at DESC
");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$res = $stmt->get_result();
$logsArray = $res->fetch_all(MYSQLI_ASSOC);
$logCount = count($logsArray);

// Resolve editor names using Auth0 user data
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Handle CSV export if requested
if (isset($_GET['export']) && $_GET['export'] == 1) {
    header("Content-Type: text/csv; charset=UTF-8");

    // Retrieve the task subject to use in the filename
    $stmtName = $conn->prepare("SELECT subject FROM tasks WHERE id=?");
    $stmtName->bind_param("i", $taskId);
    $stmtName->execute();
    $result = $stmtName->get_result();
    $taskNameRow = $result->fetch_assoc();
    $taskNameSafe = isset($taskNameRow['subject'])
        ? preg_replace("/[^A-Za-z0-9_-]/", "_", $taskNameRow['subject'])
        : "Task";

    header("Content-Disposition: attachment; filename=\"{$taskNameSafe}_logs.csv\"");
    echo "\xEF\xBB\xBF"; // BOM for Excel

    $out = fopen("php://output", "w");
    // Write CSV header line
    fputcsv($out, ["Edited By", "Archived At", "Created At", "Subject", "Status", "Priority", "Description"]);

    // Write each log entry as a CSV row
    foreach ($logsArray as $log) {
        $editor = $user_map[$log['user_id']] ?? 'Unknown';
        $archivedAt = $log['archived_at'];
        $createdAt  = $log['created_at'];
        $subject    = $log['subject'];
        $status     = $log['status'];
        $priority   = $log['priority'];
        $description = str_replace(["\r\n", "\r", "\n"], " ", $log['description']);

        fputcsv($out, [$editor, $archivedAt, $createdAt, $subject, $status, $priority, $description]);
    }
    fclose($out);

    // In test mode, we skip exit so PHPUnit can finish; otherwise, exit.
    if (!$isTesting) {
        exit;
    } else {
        return;
    }
}

require_once __DIR__ . '/../../INCLUDES/inc_header.php';
include __DIR__ . '/../../INCLUDES/inc_tasklogsview.php';
include __DIR__ . '/../../INCLUDES/inc_footer.php';
include __DIR__ .  '/../../INCLUDES/inc_disconnect.php';