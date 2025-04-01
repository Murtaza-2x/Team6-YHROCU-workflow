<?php
/*
-------------------------------------------------------------
File: view-task-page.php
Description:
- Displays a detailed view of a single task.
- Shows:
    > Task information (Title, Project, Status, Priority, Description)
    > Assigned users (via Auth0)
    > Comments with Auth0 user IDs or nicknames
    > Allows adding new comments
    > Provides edit and cancel buttons (Admin only sees edit)
-------------------------------------------------------------
*/

$title = "ROCU: View Task";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

$user = $_SESSION['user'];
$taskId = $_GET['id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Task Info
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

$subject = $task['subject'];
$description = $task['description'];
$project_id = $task['project_id'];
$status = $task['status'];
$priority = $task['priority'];

// Project Name
$projectName = '';
$projQuery = $conn->prepare("SELECT project_name FROM projects WHERE id = ?");
$projQuery->bind_param("i", $project_id);
$projQuery->execute();
$projResult = $projQuery->get_result();
if ($projResult && $row = $projResult->fetch_assoc()) {
    $projectName = $row['project_name'];
}

// Assigned Users
$assignedUsers = [];
$stmtAssigned = $conn->prepare("SELECT user_id FROM task_assigned_users WHERE task_id = ?");
$stmtAssigned->bind_param("i", $taskId);
$stmtAssigned->execute();
$assignedResult = $stmtAssigned->get_result();
while ($row = $assignedResult->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

// Auth0 Users
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

if (isset($_POST['submit_comment']) && !empty($_POST['comment'])) {
    $commentText = trim($_POST['comment']);
    $userId = $_SESSION['user']['user_id'] ?? null;

    if ($userId && $commentText) {
        $stmt = $conn->prepare("INSERT INTO comments (task_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $taskId, $userId, $commentText);
        if ($stmt->execute()) {
            echo "<p class='SUCCESS-MESSAGE'>Comment added. Reloading...</p>";
            echo "<script>setTimeout(function(){ window.location.href='view-task-page.php?id=" . urlencode($taskId) . "'; }, 1000);</script>";
            exit;
        } else {
            echo "<p class='ERROR-MESSAGE'>Failed to add comment.</p>";
        }
    } else {
        echo "<p class='ERROR-MESSAGE'>Empty comment or missing user ID.</p>";
    }
}

// Render View
include 'INCLUDES/inc_taskview.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
