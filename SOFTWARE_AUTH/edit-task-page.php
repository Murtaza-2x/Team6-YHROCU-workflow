<?php
/*
-------------------------------------------------------------
File: edit-task-page.php
Description:
- Displays the edit task page for Admins or authorized users.
- Loads task data from the database.
- Loads assigned Auth0 users.
- Passes data to inc_taskedit.php for rendering.
-------------------------------------------------------------
*/

$title = "ROCU: Edit Task";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

if (!has_role('Admin')) {
    header('Location: index.php?error=1&msg=Not authorized.');
    exit;
}

$taskId = $_GET['id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Handle update
if (isset($_POST['update_task'])) {
    $subject = $_POST['subject'] ?? '';
    $project_id = $_POST['project_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $priority = $_POST['priority'] ?? '';
    $description = $_POST['description'] ?? '';
    $assigned = $_POST['assign'] ?? [];

    if (empty($subject) || empty($project_id) || empty($status) || empty($priority)) {
        $errorMsg = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE tasks SET subject=?, project_id=?, status=?, priority=?, description=? WHERE id=?");
        $stmt->bind_param("sisssi", $subject, $project_id, $status, $priority, $description, $taskId);
        if ($stmt->execute()) {
            // update assigned users
            $conn->query("DELETE FROM task_assigned_users WHERE task_id=$taskId");
            if (!empty($assigned)) {
                $stmtAssign = $conn->prepare("INSERT INTO task_assigned_users (task_id, user_id) VALUES (?, ?)");
                foreach ($assigned as $uid) {
                    $stmtAssign->bind_param("is", $taskId, $uid);
                    $stmtAssign->execute();
                }
            }
            echo "<p class='SUCCESS-MESSAGE'>Task updated successfully. Redirecting...</p>";
            echo "<script>setTimeout(function(){ window.location.href='view-task-page.php?id=" . urlencode($taskId) . "'; }, 1500);</script>";
            exit;
        } else {
            echo "<p class='ERROR-MESSAGE'>Failed to update task. Please try again.</p>";
        }
    }
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

$subject = $task['subject'];
$description = $task['description'];
$project_id = $task['project_id'];
$status = $task['status'];
$priority = $task['priority'];

// Assigned Users
$assignedUsers = [];
$stmtAssigned = $conn->prepare("SELECT user_id FROM task_assigned_users WHERE task_id = ?");
$stmtAssigned->bind_param("i", $taskId);
$stmtAssigned->execute();
$resAssigned = $stmtAssigned->get_result();
while ($row = $resAssigned->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

// Auth0 Users
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Projects
$projects = [];
$res_proj = $conn->query("SELECT id, project_name FROM projects");
while ($p = $res_proj->fetch_assoc()) {
    $projects[] = $p;
}

include 'INCLUDES/inc_taskedit.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
