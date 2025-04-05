<?php
/*
-------------------------------------------------------------
File: edit-task-page.php
Description:
- Displays the edit task page for Admins or authorized users.
- Loads task data from the database.
- Archives old task info before updating.
- Shows assigned Auth0 users.
- Allows editing subject, project, status, priority, description, assignees.
-------------------------------------------------------------

*/

$title = "ROCU: Edit Task";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';

require_once __DIR__ . '/INCLUDES/inc_email.php';

// Check if the user is logged in and authorized to edit
if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

$taskId = $_GET['id'] ?? null;

// Validate task ID
if (!$taskId || !is_numeric($taskId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Handle task update after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    $subject     = trim($_POST['subject'] ?? '');
    $project_id  = trim($_POST['project_id'] ?? '');
    $status      = trim($_POST['status'] ?? '');
    $priority    = trim($_POST['priority'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned    = $_POST['assign'] ?? [];
    $edited_by   = $_SESSION['user']['user_id'] ?? '';

    // Validate required fields
    if (empty($subject) || empty($project_id) || empty($status) || empty($priority)) {
        echo "<p class='ERROR-MESSAGE'>All fields are required.</p>";
    } else {
        // Archive task before updating
        $stmtArchive = $conn->prepare("INSERT INTO task_archive (task_id, subject, status, priority, description, edited_by, created_at)
        SELECT id, subject, status, priority, description, ?, created_at
        FROM tasks
        WHERE id = ?");
        $stmtArchive->bind_param("si", $edited_by, $taskId);
        $stmtArchive->execute();

        // Update task in the database
        $stmt = $conn->prepare("UPDATE tasks SET subject=?, project_id=?, status=?, priority=?, description=? WHERE id=?");
        $stmt->bind_param("sisssi", $subject, $project_id, $status, $priority, $description, $taskId);
        if ($stmt->execute()) {
            // Delete previous task assignments
            $conn->query("DELETE FROM task_assigned_users WHERE task_id = $taskId");

            // Assign users to the task
            if (!empty($assigned)) {
                $stmtAssign = $conn->prepare("INSERT INTO task_assigned_users (task_id, user_id) VALUES (?, ?)");
                foreach ($assigned as $uid) {
                    $stmtAssign->bind_param("is", $taskId, $uid);
                    $stmtAssign->execute();

                    // Fetch user details for email
                    $user = Auth0UserManager::getUser($uid);
                    $userEmail = $user['email'];

                    // Fetch project name using project_id
                    $stmtProj = $conn->prepare("SELECT project_name FROM projects WHERE id = ?");
                    $stmtProj->bind_param("i", $project_id);
                    $stmtProj->execute();
                    $resProj = $stmtProj->get_result();
                    $projectData = $resProj->fetch_assoc();
                    $project_name = $projectData['project_name'] ?? 'Unknown Project';

                    // Prepare email content
                    $emailSubject = "Task Updated: {$subject}";
                    $messageBody = "The task '{$subject}' has been updated. Here are the details:";

                    // Send the task update email
                    $emailSent = sendTaskEmail($userEmail, $emailSubject, $messageBody, [
                        'subject' => $subject,
                        'project_name' => $project_name,
                        'status' => $status,
                        'priority' => $priority,
                        'description' => $description,
                    ]);

                    if ($emailSent) {
                        echo "<p class='SUCCESS-MESSAGE'>Email sent to {$userEmail} successfully.</p>";
                    } else {
                        echo "<p class='ERROR-MESSAGE'>Failed to send email to {$userEmail}.</p>";
                    }
                }
            }

            echo "<p class='SUCCESS-MESSAGE'>Task updated successfully. Redirecting...</p>";
            echo "<script>setTimeout(function(){ window.location.href='view-task-page.php?id=" . urlencode($taskId) . "'; }, 1500);</script>";
            exit;
        } else {
            echo "<p class='ERROR-MESSAGE'>Task update failed.</p>";
        }
    }
}

// Load task info from database
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$res = $stmt->get_result();
$task = $res->fetch_assoc();

if (!$task) {
    echo "<p class='ERROR-MESSAGE'>Task not found.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

$subject     = $task['subject'];
$description = $task['description'];
$project_id  = $task['project_id'];
$status      = $task['status'];
$priority    = $task['priority'];

// Load assigned users
$assignedUsers = [];
$stmtAssigned = $conn->prepare("SELECT user_id FROM task_assigned_users WHERE task_id = ?");
$stmtAssigned->bind_param("i", $taskId);
$stmtAssigned->execute();
$resAssigned = $stmtAssigned->get_result();
while ($row = $resAssigned->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

// Load Auth0 users
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Load projects
$projects = [];
$res_proj = $conn->query("SELECT id, project_name FROM projects");
while ($p = $res_proj->fetch_assoc()) {
    $projects[] = $p;
}

include 'INCLUDES/inc_taskedit.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
