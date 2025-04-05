<?php
/*
-------------------------------------------------------------
File: create-project-page.php
Description:
- Allows Admins to create new projects.
- Collects:
    > Project title, status, priority, description, due date.
- Supports PHPUnit test mode via JSON.
-------------------------------------------------------------
*/

$title = "ROCU: Create Project";

require_once __DIR__ . '/../../INCLUDES/role_helper.php';

// Detect if running in PHPUnit
$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

if ($isTesting && session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

// Early exit for unauthorized test mode
if ($isTesting && (!$user || strtolower($user['role'] ?? '') !== 'admin')) {
    echo json_encode(['error' => 'Not authorized']);
    return;
}

require_once __DIR__ . '/../../INCLUDES/env_loader.php';
require_once __DIR__ . '/../../INCLUDES/inc_connect.php';

if ($isTesting && isset($GLOBALS['conn'])) {
    $conn = $GLOBALS['conn'];
}

if (!$isTesting && (!is_logged_in() || !is_staff())) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include '/../../INCLUDES/inc_footer.php';
    exit;
}

$errorMsg = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = trim($_POST['project_name'] ?? '');
    $status       = trim($_POST['status'] ?? '');
    $priority     = trim($_POST['priority'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $due_date     = trim($_POST['due_date'] ?? '');
    $created_by   = $_SESSION['user']['user_id'] ?? '';

    if (!$project_name || !$status || !$priority || !$description || !$due_date) {
        $errorMsg = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO projects (project_name, status, priority, description, due_date, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $project_name, $status, $priority, $description, $due_date, $created_by);

        if ($stmt->execute()) {
            $newProjectId = $stmt->insert_id;
            $successMsg = "Project created successfully.";
        } else {
            $errorMsg = "Failed to create project. Please try again.";
        }
    }

    if ($isTesting) {
        echo json_encode([$successMsg ? 'success' : 'error' => $successMsg ?: $errorMsg]);
        return;
    }

    if ($successMsg) {
        echo "<p class='SUCCESS-MESSAGE'>Project created successfully. Redirecting...</p>";
        echo "<script>setTimeout(function(){ window.location.href='../../view-project-page.php?id=" . urlencode($newProjectId) . "'; }, 1500);</script>";
        exit;
    }
}

if (!$isTesting) {
    require_once __DIR__ .'/../../INCLUDES/inc_header.php';
    include __DIR__ . '../../INCLUDES/inc_projectcreate.php';
    include __DIR__ . '/../../INCLUDES/inc_footer.php';
    include __DIR__ . '/../../INCLUDES/inc_disconnect.php';
}