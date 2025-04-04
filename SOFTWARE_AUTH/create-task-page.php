<?php
/*
-------------------------------------------------------------
File: create-task-page.php
Description:
- Allows Admins to create new tasks.
- Collects:
    > Subject, project, status, priority, description.
    > Multiple assigned users (from Auth0).
- Shows success or error messages and redirects.
- Also stores `created_by` = the current Auth0 user_id.
-------------------------------------------------------------
*/

$title = "ROCU: Create Task";

require_once __DIR__ . '/INCLUDES/role_helper.php';

// Detect if running in PHPUnit
$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

// Start session for test mode
if ($isTesting && session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Pull current user
$user = $_SESSION['user'] ?? null;

// Handle auth denial early in test mode
if ($isTesting && (!$user || strtolower($user['role'] ?? '') !== 'admin')) {
    echo json_encode(['error' => 'Not authorized']);
    return;
}

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';

$conn = $GLOBALS['conn'] ?? $conn ?? null;

require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';
require_once __DIR__ . '/INCLUDES/inc_email.php';

// If real world and unauthorized, show HTML denial
if (!$isTesting && (!is_logged_in() || !is_staff())) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Dependency injection-compatible
$userManager = $GLOBALS['Auth0UserManager'] ?? new Auth0UserManager();

$errorMsg = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject     = trim($_POST['subject'] ?? '');
    $project_id  = trim($_POST['project_id'] ?? '');
    $status      = trim($_POST['status'] ?? '');
    $priority    = trim($_POST['priority'] ?? '');
    $due_date    = trim($_POST['due_date'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned    = $_POST['assign'] ?? [];
    $creator     = $_SESSION['user']['user_id'] ?? '';

    if (empty($subject) || empty($project_id) || empty($status) || empty($priority)) {
        $errorMsg = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (subject, project_id, status, priority, due_date, description, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sisssss", $subject, $project_id, $status, $priority, $due_date, $description, $creator);

        if ($stmt->execute()) {
            $newTaskId = $stmt->insert_id;

            if (!empty($assigned)) {
                $stmtAssign = $conn->prepare("INSERT INTO task_assigned_users (task_id, user_id) VALUES (?, ?)");
                foreach ($assigned as $uid) {
                    $stmtAssign->bind_param("is", $newTaskId, $uid);
                    $stmtAssign->execute();
                }
            }

            $successMsg = "Task created successfully.";
        } else {
            $errorMsg = "Failed to create task. Please try again.";
        }
    }

    // Output JSON in test mode
    if ($isTesting) {
        echo json_encode([$successMsg ? 'success' : 'error' => $successMsg ?: $errorMsg]);
        return;
    }

    if ($successMsg) {
        echo "<p class='SUCCESS-MESSAGE'>Task created successfully. Redirecting...</p>";
        echo "<script>setTimeout(function(){ window.location.href='view-task-page.php?id=" . urlencode($newTaskId) . "'; }, 1500);</script>";
        exit;
    }
}

// If not test mode, show full UI
if (!$isTesting) {
    // Load project dropdown
    $projects = [];
    $res_proj = $conn->query("SELECT id, project_name FROM projects");
    while ($p = $res_proj->fetch_assoc()) $projects[] = $p;

    // Load users
    $auth0_users = Auth0UserFetcher::getUsers();
    $user_map = [];
    foreach ($auth0_users as $u) {
        $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'] ?? 'Unknown';
    }

    require_once __DIR__ . '/INCLUDES/inc_header.php';
    include 'INCLUDES/inc_taskcreate.php';
    include 'INCLUDES/inc_footer.php';
    include 'INCLUDES/inc_disconnect.php';
}