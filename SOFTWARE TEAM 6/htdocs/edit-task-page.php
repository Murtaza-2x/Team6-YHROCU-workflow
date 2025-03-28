<?php
/*
  edit-task-page.php

  1) Only Admin or Manager can edit tasks.
  2) On GET, retrieves the current task (plus assigned users) to display in the form.
  3) On POST (update_task), it archives the current task in the `archive` table, then updates `tasks`.
  4) The assigned users in `task_assigned_users` are deleted and reinserted.

  The form is in inc_taskedit.php, which has a <button name="update_task">Update Task</button>.
*/

$clearance = $_SESSION['clearance'] ?? '';
if ($clearance === 'User') {
    echo "You do not have permission to edit tasks.";
    exit;
}

$title = 'ROCU: Edit Task';
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid task ID.";
    exit;
}

$subject       = '';
$project_id    = '';
$status        = '';
$priority      = '';
$description   = '';
$assignedUserIds = [];

$sql_assigned = "SELECT user_id FROM task_assigned_users WHERE task_id = $id";
$result_assigned = $conn->query($sql_assigned);
if ($result_assigned && $result_assigned->num_rows > 0) {
    while ($r = $result_assigned->fetch_assoc()) {
        $assignedUserIds[] = $r['user_id'];
    }
}

$sql_current = "SELECT * FROM tasks WHERE id = $id";
$result_current = $conn->query($sql_current);
if ($result_current && $result_current->num_rows > 0) {
    $currentTask = $result_current->fetch_assoc();
} else {
    echo "Task not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    $edited_by = $_SESSION['id'];
    $archiveSql = "INSERT INTO archive (task_id, subject, project_id, status, priority, description, created_at, edited_by)
                   VALUES (
                     {$currentTask['id']},
                     '" . $conn->real_escape_string($currentTask['subject']) . "',
                     '" . $conn->real_escape_string($currentTask['project_id']) . "',
                     '" . $conn->real_escape_string($currentTask['status']) . "',
                     '" . $conn->real_escape_string($currentTask['priority']) . "',
                     '" . $conn->real_escape_string($currentTask['description']) . "',
                     '" . $currentTask['created_at'] . "',
                     $edited_by
                   )";
    $conn->query($archiveSql);

    $subject     = $conn->real_escape_string($_POST['subject']);
    $project_id  = $conn->real_escape_string($_POST['project_id']);
    $status      = $conn->real_escape_string($_POST['status']);
    $priority    = $conn->real_escape_string($_POST['priority']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql_update = "UPDATE tasks 
                   SET subject     = '$subject',
                       project_id  = '$project_id',
                       status      = '$status',
                       priority    = '$priority',
                       description = '$description'
                   WHERE id = $id";

    if ($conn->query($sql_update) === TRUE) {
        $conn->query("DELETE FROM task_assigned_users WHERE task_id = $id");
        if (isset($_POST['assign']) && is_array($_POST['assign'])) {
            foreach ($_POST['assign'] as $user_id) {
                $user_id = (int)$user_id;
                $sql_link = "INSERT INTO task_assigned_users (task_id, user_id) VALUES ($id, $user_id)";
                $conn->query($sql_link);

                $userQuery = "SELECT email FROM users WHERE id = $user_id";
                $userResult = $conn->query($userQuery);
                if ($userResult && $userResult->num_rows > 0) {
                    $userRow = $userResult->fetch_assoc();
                    sendTaskUpdateEmail($userRow['email']);
                }
            }
        }
        header("Location: view-task-page.php?id=$id");
        exit;
    } else {
        echo "Error updating task: " . $conn->error;
    }
}
else {
    $subject     = $currentTask['subject'];
    $project_id  = $currentTask['project_id'];
    $status      = $currentTask['status'];
    $priority    = $currentTask['priority'];
    $description = $currentTask['description'];
}

$projects = [];
$sql_projects = "SELECT id, project_name FROM projects ORDER BY project_name";
$result_projects = $conn->query($sql_projects);
if ($result_projects && $result_projects->num_rows > 0) {
    while ($projRow = $result_projects->fetch_assoc()) {
        $projects[] = $projRow;
    }
}

$users = [];
$sql_users = "SELECT id, username FROM users ORDER BY username";
$result_users = $conn->query($sql_users);
if ($result_users && $result_users->num_rows > 0) {
    while ($userRow = $result_users->fetch_assoc()) {
        $users[] = $userRow;
    }
}

include 'INCLUDES/inc_taskedit.php';
include 'INCLUDES/inc_email-task-update.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
