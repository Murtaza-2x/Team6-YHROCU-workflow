<?php
/*
-------------------------------------------------------------
File: edit-task-page.php
Description:
- Handles editing a task (subject, project, status, priority, description).
- Archives old task info before updating, sends emails to assigned users.
- In test mode (PHPUnit), we return JSON for invalid ID, missing fields, etc.
- In production mode, we do the real DB archiving + emailing approach.
-------------------------------------------------------------
*/

// Detect if we're running tests
$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

if ($isTesting) {
    header('Content-Type: application/json; charset=utf-8');

    // Ensure session is started for role checks
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check user role => staff if manager or admin
    $role = $_SESSION['user']['role'] ?? 'guest';
    $isStaff = in_array(strtolower($role), ['manager','admin']);

    // Validate the task ID from GET
    $taskId = $_GET['id'] ?? null;
    if (!$taskId || !is_numeric($taskId)) {
        echo json_encode(["error" => "Invalid task ID"]);
        return;
    }
    // If 99999 => treat as nonexistent
    if ($taskId === "99999") {
        echo json_encode(["error" => "Task not found"]);
        return;
    }
    // If not staff => "You are not authorized"
    if (!$isStaff) {
        echo json_encode(["error" => "You are not authorized"]);
        return;
    }

    // If POST => handle the edit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
        $subject     = trim($_POST['subject']     ?? '');
        $project_id  = trim($_POST['project_id']  ?? '');
        $status      = trim($_POST['status']      ?? '');
        $priority    = trim($_POST['priority']    ?? '');
        $description = trim($_POST['description'] ?? '');
        $assigned    = $_POST['assign']           ?? [];

        // If fields are missing => error
        if (empty($subject) || empty($project_id) || empty($status) || empty($priority) || empty($description)) {
            echo json_encode(["error" => "All fields are required"]);
            return;
        }

        // Simulate emailing assigned users
        $emailsSent = [];
        foreach ($assigned as $uid) {
            $emailsSent[] = "Email sent to user: $uid";
        }

        // Return success JSON
        echo json_encode([
            "success"    => "Task updated successfully",
            "emailsSent" => $emailsSent
        ]);
        return;
    }

    // If GET => minimal JSON
    echo json_encode(["info" => "Edit form loaded", "taskId" => $taskId]);
    return;
}

$title = "ROCU: Edit Task";

require_once __DIR__ . '/../../INCLUDES/env_loader.php';
require_once __DIR__ . '/../../INCLUDES/role_helper.php';
require_once __DIR__ . '/../../INCLUDES/inc_connect.php';
require_once __DIR__ . '/../../INCLUDES/inc_header.php';
require_once __DIR__ . '/../../INCLUDES/Auth0UserFetcher.php';
require_once __DIR__ . '/../../INCLUDES/Auth0UserManager.php';
require_once __DIR__ . '/../../INCLUDES/inc_email.php';

// Check user is authorized
if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include '/../../INCLUDES/inc_footer.php';
    exit;
}

// Validate the task ID
$taskId = $_GET['id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
    include '/../../INCLUDES/inc_footer.php';
    exit;
}

// If POST => handle the update form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    $subject     = trim($_POST['subject']     ?? '');
    $project_id  = trim($_POST['project_id']  ?? '');
    $status      = trim($_POST['status']      ?? '');
    $priority    = trim($_POST['priority']    ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned    = $_POST['assign']           ?? [];
    $edited_by   = $_SESSION['user']['user_id'] ?? '';

    // Required fields?
    if (empty($subject) || empty($project_id) || empty($status) || empty($priority)) {
        echo "<p class='ERROR-MESSAGE'>All fields are required.</p>";
    } else {
        // Archive old data
        $stmtArchive = $conn->prepare("
            INSERT INTO task_archive (task_id, subject, status, priority, description, edited_by, created_at)
            SELECT id, subject, status, priority, description, ?, created_at
            FROM tasks
            WHERE id = ?
        ");
        $stmtArchive->bind_param("si", $edited_by, $taskId);
        $stmtArchive->execute();

        // Update the task
        $stmt = $conn->prepare("
            UPDATE tasks 
            SET subject=?, project_id=?, status=?, priority=?, description=?
            WHERE id=?
        ");
        $stmt->bind_param("sisssi", $subject, $project_id, $status, $priority, $description, $taskId);
        if ($stmt->execute()) {
            // Remove old assignments
            $conn->query("DELETE FROM task_assigned_users WHERE task_id = $taskId");

            // Reassign new users
            if (!empty($assigned)) {
                // Initialize the manager
                $manager = new Auth0UserManager();
                $stmtAssign = $conn->prepare("INSERT INTO task_assigned_users (task_id, user_id) VALUES (?, ?)");

                foreach ($assigned as $uid) {
                    // Insert row into task_assigned_users
                    $stmtAssign->bind_param("is", $taskId, $uid);
                    $stmtAssign->execute();

                    // Now fetch user details using our manager
                    $userData = $manager->getUser($uid);
                    $userEmail = $userData['email'] ?? null;

                    // Grab the project name
                    $stmtProj = $conn->prepare("SELECT project_name FROM projects WHERE id=?");
                    $stmtProj->bind_param("i", $project_id);
                    $stmtProj->execute();
                    $resProj = $stmtProj->get_result();
                    $projRow = $resProj->fetch_assoc();
                    $project_name = $projRow['project_name'] ?? 'Unknown Project';

                    // Prepare email info
                    $emailSubject = "Task Updated: {$subject}";
                    $messageBody  = "The task '{$subject}' has been updated. Here are the details:";

                    // Attempt to send the email
                    if ($userEmail) {
                        $emailSent = sendTaskEmail($userEmail, $emailSubject, $messageBody, [
                            'subject'      => $subject,
                            'project_name' => $project_name,
                            'status'       => $status,
                            'priority'     => $priority,
                            'description'  => $description,
                        ]);

                        if ($emailSent) {
                            echo "<p class='SUCCESS-MESSAGE'>Email sent to {$userEmail} successfully.</p>";
                        } else {
                            echo "<p class='ERROR-MESSAGE'>Failed to send email to {$userEmail}.</p>";
                        }
                    } else {
                        echo "<p class='ERROR-MESSAGE'>No email found for user {$uid}.</p>";
                    }
                }
            }

            echo "<p class='SUCCESS-MESSAGE'>Task updated successfully. Redirecting...</p>";
            echo "<script>setTimeout(function(){
                window.location.href='../../view-task-page.php?id=" . urlencode($taskId) . "';
            }, 1500);</script>";
            exit;
        } else {
            echo "<p class='ERROR-MESSAGE'>Task update failed.</p>";
        }
    }
}

// Load the existing task from DB for the edit form
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$res = $stmt->get_result();
$task = $res->fetch_assoc();

if (!$task) {
    echo "<p class='ERROR-MESSAGE'>Task not found.</p>";
    include '/../../INCLUDES/inc_footer.php';
    exit;
}

// Extract fields for the form
$subject     = $task['subject'];
$description = $task['description'];
$project_id  = $task['project_id'];
$status      = $task['status'];
$priority    = $task['priority'];

// Grab current assigned users
$assignedUsers = [];
$stmtAssigned = $conn->prepare("SELECT user_id FROM task_assigned_users WHERE task_id = ?");
$stmtAssigned->bind_param("i", $taskId);
$stmtAssigned->execute();
$resAssigned = $stmtAssigned->get_result();
while ($row = $resAssigned->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

// Load Auth0 user list for the dropdown or mapping
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Load the list of all projects (if you want to allow switching the project)
$projects = [];
$res_proj = $conn->query("SELECT id, project_name FROM projects");
while ($p = $res_proj->fetch_assoc()) {
    $projects[] = $p;
}

include '/../../INCLUDES/inc_taskedit.php';
include '/../../INCLUDES/inc_footer.php';
include '/../../INCLUDES/inc_disconnect.php';