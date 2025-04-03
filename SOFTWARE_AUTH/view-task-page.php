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
-------------------------------------------------------------
*/

$title = "ROCU: View Task";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

// Redirect if user is not logged in
if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

// Validate task ID
$taskId = $_GET['id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Load task data
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
if (!$task) {
    echo "<p class='ERROR-MESSAGE'>Task not found.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Extract task details
$subject     = $task['subject'];
$description = $task['description'];
$project_id  = $task['project_id'];
$status      = $task['status'];
$priority    = $task['priority'];

// Get project name
$projectName = '';
$stmtP = $conn->prepare("SELECT project_name FROM projects WHERE id = ?");
$stmtP->bind_param("i", $project_id);
$stmtP->execute();
$resP = $stmtP->get_result();
if ($resP && $pRow = $resP->fetch_assoc()) {
    $projectName = $pRow['project_name'];
}

// Get assigned users
$assignedUsers = [];
$stmtA = $conn->prepare("SELECT user_id FROM task_assigned_users WHERE task_id = ?");
$stmtA->bind_param("i", $taskId);
$stmtA->execute();
$resA = $stmtA->get_result();
while ($row = $resA->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

// Fetch Auth0 users
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Handle comment submission
if (isset($_POST['submit_comment']) && !empty($_POST['comment'])) {
    $commentText = trim($_POST['comment']);
    $userId = $_SESSION['user']['user_id'] ?? null;

    if ($userId && $commentText) {
        $stmtC = $conn->prepare("INSERT INTO comments (task_id, user_id, comment) VALUES (?, ?, ?)");
        $stmtC->bind_param("sss", $taskId, $userId, $commentText);
        if ($stmtC->execute()) {
            echo "<p class='SUCCESS-MESSAGE'>Comment added. Reloading...</p>";
            echo "<script>setTimeout(function(){ window.location.href='view-task-page.php?id=" . urlencode($taskId) . "'; }, 1500);</script>";
            exit;
        } else {
            echo "<p class='ERROR-MESSAGE'>Failed to add comment.</p>";
        }
    } else {
        echo "<p class='ERROR-MESSAGE'>Empty comment or missing user ID.</p>";
    }
}

// Reload Auth0 users again for last edit info (Auth0UserFetcher doesn't cache)
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Get latest archive log for this task
$stmtArchive = $conn->prepare("SELECT * FROM task_archive WHERE task_id = ? ORDER BY archived_at DESC LIMIT 1");
$stmtArchive->bind_param("i", $taskId);
$stmtArchive->execute();
$resArchive = $stmtArchive->get_result();
if ($archiveRow = $resArchive->fetch_assoc()) {
    $lastEditorId = $archiveRow['edited_by'];
    $lastEditor = $user_map[$lastEditorId] ?? $lastEditorId;
    $lastEditTime = $archiveRow['archived_at'];
}

// Render task view page
include 'INCLUDES/inc_taskview.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>