<?php
/*
This file allows the user to edit an existing task in the database.
On GET, it retrieves the task details (including assigned users) and displays them in an editable form.
On POST, it updates the task details and the task_assigned_users linking table, then redirects to the view page.
*/

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
$assignedUsers = '';
$assignedUserIds = [];

$sql_assigned = "SELECT user_id FROM task_assigned_users WHERE task_id = $id";
$result_assigned = $conn->query($sql_assigned);
if ($result_assigned && $result_assigned->num_rows > 0) {
    while ($r = $result_assigned->fetch_assoc()) {
        $assignedUserIds[] = $r['user_id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject     = $conn->real_escape_string($_POST['subject']);
    $project_id  = $conn->real_escape_string($_POST['project_id']);
    $status      = $conn->real_escape_string($_POST['status']);
    $priority    = $conn->real_escape_string($_POST['priority']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "UPDATE tasks 
            SET subject     = '$subject',
                project_id  = '$project_id',
                status      = '$status',
                priority    = '$priority',
                description = '$description'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $conn->query("DELETE FROM task_assigned_users WHERE task_id = $id");
        if (isset($_POST['assign']) && is_array($_POST['assign'])) {
            foreach ($_POST['assign'] as $user_id) {
                $user_id = (int)$user_id;
                $sql_link = "INSERT INTO task_assigned_users (task_id, user_id) VALUES ($id, $user_id)";
                $conn->query($sql_link);
            }
        }
        header("Location: view-task-page.php?id=$id");
        exit;
    } else {
        echo "Error updating task: " . $conn->error;
    }
}
else {
    $sql = "SELECT t.*,
                   GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
            FROM tasks t
            LEFT JOIN task_assigned_users tau ON t.id = tau.task_id
            LEFT JOIN users u ON tau.user_id = u.id
            WHERE t.id = $id
            GROUP BY t.id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row         = $result->fetch_assoc();
        $subject     = $row['subject'];
        $project_id  = $row['project_id'];
        $status      = $row['status'];
        $priority    = $row['priority'];
        $description = $row['description'];
        $assignedUsers = $row['assigned_users'] ?? 'No Users Assigned';
    } else {
        echo "Task not found.";
        exit;
    }
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

include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
