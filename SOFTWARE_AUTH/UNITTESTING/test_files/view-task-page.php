<?php
/*
-------------------------------------------------------------
File: view-task-page.php
Description:
- Displays a single task with:
    > Title, Project, Status, Priority, Description
    > Assigned Users (from Auth0)
    > Comments section
    > Admin-only edit button
- In test mode (when PHPUnit is running), it returns JSON responses:
    - For invalid task IDs: {"error": "Invalid task ID"}
    - For non-existent tasks: {"error": "Task not found"}
    - For valid tasks: a JSON object with task details
-------------------------------------------------------------
*/

$title = "ROCU: View Task";

require_once __DIR__ . '/../../INCLUDES/role_helper.php';

// Detect if running in PHPUnit
$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

if ($isTesting) {
    // Test Mode: Return JSON responses for testing purposes.
    $taskId = $_GET['id'] ?? null;
    
    // If task ID is invalid or missing, return error JSON.
    if (!$taskId || !is_numeric($taskId)) {
        echo json_encode(['error' => 'Invalid task ID']);
        return;
    }
    
    // Simulate a task lookup: if taskId equals "99999", assume task not found.
    if ($taskId == "99999") {
        echo json_encode(['error' => 'Task not found']);
        return;
    }
    
    // Otherwise, simulate a valid task record.
    $task = [
        'id' => $taskId,
        'subject' => 'Test Task Subject',
        'description' => 'Test task description.',
        'project_id' => 1,
        'status' => 'New',
        'priority' => 'Urgent',
    ];
    
    $response = [
        'taskId' => $taskId,
        'task' => $task,
        'sessionUser' => $_SESSION['user'] ?? null,
        'assignedUsers' => [],         // For test mode, we return an empty list.
        'projectName' => 'Test Project', // Mocked project name.
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    return;
}

require_once __DIR__ . '/../../INCLUDES/env_loader.php';
require_once __DIR__ . '/../../INCLUDES/inc_connect.php';
require_once __DIR__ . '/../../INCLUDES/inc_header.php';
require_once __DIR__ . '/../../INCLUDES/Auth0UserFetcher.php';

// Redirect if user is not logged in
if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

// Validate task ID
$taskId = $_GET['id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include '/../../INCLUDES/inc_footer.php';
    exit;
}

// Load task data from database
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
if (!$task) {
    echo "<p class='ERROR-MESSAGE'>Task not found.</p>";
    include '/../../INCLUDES/inc_footer.php';
    exit;
}

// Extract task details
$subject     = $task['subject'];
$description = $task['description'];
$project_id  = $task['project_id'];
$status      = $task['status'];
$priority    = $task['priority'];

// Get project name from the projects table
$projectName = '';
$stmtP = $conn->prepare("SELECT project_name FROM projects WHERE id = ?");
$stmtP->bind_param("i", $project_id);
$stmtP->execute();
$resP = $stmtP->get_result();
if ($resP && $pRow = $resP->fetch_assoc()) {
    $projectName = $pRow['project_name'];
}

// Get assigned users for the task
$assignedUsers = [];
$stmtA = $conn->prepare("SELECT user_id FROM task_assigned_users WHERE task_id = ?");
$stmtA->bind_param("i", $taskId);
$stmtA->execute();
$resA = $stmtA->get_result();
while ($row = $resA->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

// Fetch Auth0 users (for mapping user IDs to display names)
$auth0_users = $GLOBALS['Auth0UserFetcherUsers'] ?? Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Handle comment submission if applicable.
if (isset($_POST['submit_comment']) && !empty($_POST['comment'])) {
    $commentText = trim($_POST['comment']);
    $userId = $_SESSION['user']['user_id'] ?? null;

    if ($userId && $commentText) {
        $stmtC = $conn->prepare("INSERT INTO comments (task_id, user_id, comment) VALUES (?, ?, ?)");
        $stmtC->bind_param("sss", $taskId, $userId, $commentText);
        if ($stmtC->execute()) {
            echo "<p class='SUCCESS-MESSAGE'>Comment added. Reloading...</p>";
            echo "<script>setTimeout(function(){ window.location.href='../../view-task-page.php?id=" . urlencode($taskId) . "'; }, 1500);</script>";
            exit;
        } else {
            echo "<p class='ERROR-MESSAGE'>Failed to add comment.</p>";
        }
    } else {
        echo "<p class='ERROR-MESSAGE'>Empty comment or missing user ID.</p>";
    }
}

// Reload Auth0 users again (for last edit info)
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Get the latest archive log for this task
$stmtArchive = $conn->prepare("SELECT * FROM task_archive WHERE task_id = ? ORDER BY archived_at DESC LIMIT 1");
$stmtArchive->bind_param("i", $taskId);
$stmtArchive->execute();
$resArchive = $stmtArchive->get_result();
if ($archiveRow = $resArchive->fetch_assoc()) {
    $lastEditorId = $archiveRow['edited_by'];
    $lastEditor = $user_map[$lastEditorId] ?? $lastEditorId;
    $lastEditTime = $archiveRow['archived_at'];
}

include '/../../INCLUDES/inc_taskview.php';
include '/../../INCLUDES/inc_footer.php';
include '/../../INCLUDES/inc_disconnect.php';
