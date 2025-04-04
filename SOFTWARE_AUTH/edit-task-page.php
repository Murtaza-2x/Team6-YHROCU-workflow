<?php
echo "DEBUG: Entered edit-task-page.php\n";

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

// Load environment and helper files.
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';
require_once __DIR__ . '/INCLUDES/inc_email.php';

// 1) Determine if we are in test mode.
$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;
echo "DEBUG: isTesting = " . var_export($isTesting, true) . "\n";

// 2) If not in test mode, include header.
if (!$isTesting) {
    echo "DEBUG: Not in test mode, including inc_header\n";
    require_once __DIR__ . '/INCLUDES/inc_header.php';
} else {
    echo "DEBUG: Running in test mode, skipping inc_header\n";
}

// Start session if not active
if (session_status() !== PHP_SESSION_ACTIVE) {
    echo "DEBUG: Starting session...\n";
    session_start();
} else {
    echo "DEBUG: Session already active\n";
}

// Use session to retrieve current user
$user = $_SESSION['user'] ?? null;
echo "DEBUG: user from session = " . var_export($user, true) . "\n";

// 3) Authorization check – only admin allowed.
if (!$user || strtolower($user['role'] ?? '') !== 'admin') {
    echo "DEBUG: Authorization failed, user is not admin or no user.\n";
    if ($isTesting) {
        echo json_encode(['error' => 'Not authorized']);
        return;
    } else {
        echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
        include __DIR__ . '/INCLUDES/inc_footer.php';
        exit;
    }
}
echo "DEBUG: Authorization success, user is admin.\n";

// 4) Inject Auth0UserManager instance (allows tests to override it).
$userManager = $GLOBALS['Auth0UserManager'] ?? new Auth0UserManager();
echo "DEBUG: userManager injected\n";

// 5) Validate task ID
$taskId = $_GET['id'] ?? null;
echo "DEBUG: Received taskId = " . var_export($taskId, true) . "\n";

if (!$taskId || !is_numeric($taskId)) {
    echo "DEBUG: Invalid taskId\n";
    if ($isTesting) {
        echo json_encode(['error' => 'Invalid task ID']);
        return;
    } else {
        echo "<p class='ERROR-MESSAGE'>Invalid task ID.</p>";
        include __DIR__ . '/INCLUDES/inc_footer.php';
        exit;
    }
}
echo "DEBUG: taskId is valid, id = $taskId\n";

// 6) Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    echo "DEBUG: Handling update task submission\n";
    $subject     = trim($_POST['subject'] ?? '');
    $project_id  = trim($_POST['project_id'] ?? '');
    $status      = trim($_POST['status'] ?? '');
    $priority    = trim($_POST['priority'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned    = $_POST['assign'] ?? [];
    $edited_by   = $_SESSION['user']['user_id'] ?? '';

    echo "DEBUG: subject=$subject, project_id=$project_id, status=$status, priority=$priority\n";

    if (empty($subject) || empty($project_id) || empty($status) || empty($priority)) {
        echo "DEBUG: Some fields are empty -> returning error\n";
        if ($isTesting) {
            echo json_encode(['error' => 'All fields are required']);
            return;
        } else {
            echo "<p class='ERROR-MESSAGE'>All fields are required.</p>";
        }
    } else {
        echo "DEBUG: Fields are all present, archiving old data...\n";
        // Archive current task data.
        $stmtArchive = $conn->prepare("INSERT INTO task_archive (task_id, subject, status, priority, description, edited_by, created_at)
            SELECT id, subject, status, priority, description, ?, created_at FROM tasks WHERE id = ?");
        $stmtArchive->bind_param("si", $edited_by, $taskId);
        $stmtArchive->execute();

        // Update task
        $stmt = $conn->prepare("UPDATE tasks SET subject=?, project_id=?, status=?, priority=?, description=? WHERE id=?");
        $stmt->bind_param("sisssi", $subject, $project_id, $status, $priority, $description, $taskId);
        if ($stmt->execute()) {
            echo "DEBUG: Task update success, clearing old assignments...\n";
            $conn->query("DELETE FROM task_assigned_users WHERE task_id = $taskId");

            if (!empty($assigned)) {
                echo "DEBUG: Insert new assigned user(s)\n";
                $stmtAssign = $conn->prepare("INSERT INTO task_assigned_users (task_id, user_id) VALUES (?, ?)");
                foreach ($assigned as $uid) {
                    $stmtAssign->bind_param("is", $taskId, $uid);
                    $stmtAssign->execute();

                    // Fetch user details for email
                    $userForEmail = $userManager->getUser($uid);
                    $userEmail = $userForEmail['email'] ?? '';
                    // Send email
                    sendTaskEmail($userEmail, 'Task Updated',
                        "The task '{$subject}' has been updated.\nDescription: {$description}",
                        [
                            'subject'        => $subject,
                            'project_name'   => $project_id,
                            'status'         => $status,
                            'priority'       => $priority,
                            'description'    => $description,
                            'assigned_users' => implode(', ', $assigned)
                        ]
                    );
                }
            }
            echo "DEBUG: Done updating, isTesting=$isTesting\n";
            if ($isTesting) {
                echo json_encode(['success' => 'Task updated successfully']);
                return;
            } else {
                echo "<p class='SUCCESS-MESSAGE'>Task updated successfully. Redirecting...</p>";
                echo "<script>setTimeout(function(){ window.location.href='view-task-page.php?id=" . urlencode($taskId) . "'; }, 1500);</script>";
                exit;
            }
        } else {
            echo "DEBUG: Task update failed\n";
            if ($isTesting) {
                echo json_encode(['error' => 'Task update failed']);
                return;
            } else {
                echo "<p class='ERROR-MESSAGE'>Task update failed.</p>";
            }
        }
    }
}

// 7) Load task info
echo "DEBUG: Loading task info for ID=$taskId\n";
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$res = $stmt->get_result();
$task = $res->fetch_assoc();

if (!$task) {
    echo "DEBUG: Task not found in DB\n";
    if ($isTesting) {
        echo json_encode(['error' => 'Task not found']);
        return;
    } else {
        echo "<p class='ERROR-MESSAGE'>Task not found.</p>";
        include __DIR__ . '/INCLUDES/inc_footer.php';
        exit;
    }
}
echo "DEBUG: Task found\n";

// Retrieve fields
$subject     = $task['subject'];
$description = $task['description'];
$project_id  = $task['project_id'];
$status      = $task['status'];
$priority    = $task['priority'];

// Retrieve assigned users
echo "DEBUG: Retrieving assigned users\n";
$assignedUsers = [];
$stmtAssigned = $conn->prepare("SELECT user_id FROM task_assigned_users WHERE task_id = ?");
$stmtAssigned->bind_param("i", $taskId);
$stmtAssigned->execute();
$resAssigned = $stmtAssigned->get_result();
while ($row = $resAssigned->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}
echo "DEBUG: assignedUsers = " . var_export($assignedUsers, true) . "\n";

// Retrieve Auth0 users
echo "DEBUG: get Auth0 users...\n";
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}
echo "DEBUG: found " . count($auth0_users) . " auth0_users\n";

// Retrieve projects
echo "DEBUG: retrieving projects\n";
$projects = [];
$res_proj = $conn->query("SELECT id, project_name FROM projects");
while ($p = $res_proj->fetch_assoc()) {
    $projects[] = $p;
}
echo "DEBUG: found " . count($projects) . " projects\n";

// If GET request, show form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "DEBUG: GET request. isTesting=$isTesting\n";
    if ($isTesting) {
        echo "<form method='post'>";
        echo "<label>Subject:</label>";
        echo "<input type='text' name='subject' value='" . htmlspecialchars($subject) . "' />";
        echo "<br />";
        echo "<label>Description:</label>";
        echo "<textarea name='description'>" . htmlspecialchars($description) . "</textarea>";
        echo "<br />";
        echo "<input type='hidden' name='project_id' value='" . htmlspecialchars($project_id) . "' />";
        echo "<label>Status:</label>";
        echo "<input type='text' name='status' value='" . htmlspecialchars($status) . "' />";
        echo "<br />";
        echo "<label>Priority:</label>";
        echo "<input type='text' name='priority' value='" . htmlspecialchars($priority) . "' />";
        echo "<br />";
        echo "<button type='submit' name='update_task'>Update Task</button>";
        echo "</form>";
    } else {
        echo "DEBUG: normal mode, including inc_taskedit etc.\n";
        include __DIR__ . '/INCLUDES/inc_taskedit.php';
        include __DIR__ . '/INCLUDES/inc_footer.php';
        include __DIR__ . '/INCLUDES/inc_disconnect.php';
    }
}
?>
