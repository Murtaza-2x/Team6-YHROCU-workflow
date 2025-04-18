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
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';

require_once __DIR__ . '/INCLUDES/inc_email.php';

if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

$errorMsg = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject     = trim($_POST['subject'] ?? '');
    $project_id  = trim($_POST['project_id'] ?? '');
    $status      = trim($_POST['status'] ?? '');
    $priority    = trim($_POST['priority'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned    = $_POST['assign'] ?? [];
    $creator     = $_SESSION['user']['user_id'] ?? '';

    if (empty($subject) || empty($project_id) || empty($status) || empty($priority)) {
        $errorMsg = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (subject, project_id, status, priority, due_date, description, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sisssss", $subject, $project_id, $status, $priority, $due_date, $description, $creator);

        if ($stmt->execute()) {
            $newTaskId = $stmt->insert_id;

            $emailErrors = [];
            $emailSuccesses = [];

            if (!empty($assigned)) {
                $stmtAssign = $conn->prepare("INSERT INTO task_assigned_users (task_id, user_id) VALUES (?, ?)");
                foreach ($assigned as $uid) {
                    $stmtAssign->bind_param("is", $newTaskId, $uid);
                    $stmtAssign->execute();

                    // Fetch user details for email
                    $manager = $GLOBALS['Auth0UserManager'] ?? new Auth0UserManager();
                    $user = $manager->getUser($uid);
                    $userEmail = $user['email'];

                    // Fetch project name using project_id
                    $stmtProj = $conn->prepare("SELECT project_name FROM projects WHERE id = ?");
                    $stmtProj->bind_param("i", $project_id);
                    $stmtProj->execute();
                    $resProj = $stmtProj->get_result();
                    $projectData = $resProj->fetch_assoc();
                    $project_name = $projectData['project_name'] ?? 'Unknown Project';

                    // Email content
                    $emailSubject = "Task Created: {$subject}";
                    $messageBody = "The task '{$subject}' has been created. Here are the details:";

                    // Send email
                    $emailSent = sendTaskEmail(
                        $userEmail,
                        $emailSubject,
                        $messageBody,
                        [
                            'subject' => $subject,
                            'project_name' => $project_name,
                            'status' => $status,
                            'priority' => $priority,
                            'description' => $description,
                        ]
                    );

                    if ($emailSent) {
                        $emailSuccesses[] = $userEmail;
                    } else {
                        $emailErrors[] = $userEmail;
                    }
                }
            }

            echo "<p class='SUCCESS-MESSAGE'>Task created successfully.</p>";

            // Email result summary
            if (!empty($emailSuccesses)) {
                echo "<p class='SUCCESS-MESSAGE'>Email sent to: " . implode(", ", $emailSuccesses) . "</p>";
            }
            if (!empty($emailErrors)) {
                echo "<p class='ERROR-MESSAGE'>Failed to send email to: " . implode(", ", $emailErrors) . "</p>";
            }

            // Redirect only after showing all messages
            echo "<script>setTimeout(function(){ window.location.href='view-task-page.php?id=" . urlencode($newTaskId) . "'; }, 3000);</script>";
            exit;
        } else {
            $errorMsg = "Failed to create task. Please try again.";
        }
    }
}

// Load projects for dropdown menu
$projects = [];
$res_proj = $conn->query("SELECT id, project_name FROM projects");
while ($p = $res_proj->fetch_assoc()) {
    $projects[] = $p;
}

// Load Auth0 users for task assignment
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    // Use nickname or email as display name for user assignment
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'] ?? 'Unknown';
}

require __DIR__ . '/INCLUDES/inc_taskcreate.php';
require __DIR__ . '/INCLUDES/inc_footer.php';
require __DIR__ . '/INCLUDES/inc_disconnect.php';
