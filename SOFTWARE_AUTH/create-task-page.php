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

$errorMsg = '';
$successMsg = '';

// When the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject     = trim($_POST['subject'] ?? '');
    $project_id  = trim($_POST['project_id'] ?? '');
    $status      = trim($_POST['status'] ?? '');
    $priority    = trim($_POST['priority'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned    = $_POST['assign'] ?? [];

    // Pull the Auth0 user_id of the currently logged in user
    // This will be stored in the `created_by` column
    $created_by = $_SESSION['user']['user_id'] ?? '';

    // Basic validation
    if (empty($subject) || empty($project_id) || empty($status) || empty($priority)
        || empty($description) || empty($assigned) || empty($created_by)) {
        $errorMsg = "All fields are required, you must assign at least one user, and you must be logged in.";
    } else {
        // Insert into tasks table
        $stmt = $conn->prepare("
            INSERT INTO tasks (created_by, subject, project_id, status, priority, description)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        // created_by (string), subject (string), project_id (int), status (string), priority (string), description (string)
        $stmt->bind_param("ssisss", $created_by, $subject, $project_id, $status, $priority, $description);

        if ($stmt->execute()) {
            $newTaskId = $stmt->insert_id;

            // Assign multiple users
            $stmtAssign = $conn->prepare("
                INSERT INTO task_assigned_users (task_id, user_id) 
                VALUES (?, ?)
            ");
            foreach ($assigned as $uid) {
                $stmtAssign->bind_param("is", $newTaskId, $uid);
                $stmtAssign->execute();
            }

            // Success message & redirect
            echo "<p class='SUCCESS-MESSAGE'>Task created successfully. Redirecting...</p>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href='view-task-page.php?id=" . urlencode($newTaskId) . "';
                    }, 1500);
                  </script>";
            exit;
        } else {
            $errorMsg = "Failed to create task. Please try again.";
        }
    }
}

// Load projects for dropdown
$projects = [];
$res_proj = $conn->query("SELECT id, project_name FROM projects");
while ($p = $res_proj->fetch_assoc()) {
    $projects[] = $p;
}

// Load Auth0 users for assignment
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    // Use nickname or email as display name
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'] ?? 'Unknown';
}

include 'INCLUDES/inc_taskcreate.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
