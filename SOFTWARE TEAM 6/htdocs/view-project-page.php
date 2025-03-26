<?php
/*
This file displays the details for a single project from the projects table.
It also retrieves and displays all the distinct users assigned to tasks that belong to this project.
*/

$title = "ROCU: View Project";
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid project ID.";
    exit;
}

$sql_project = "SELECT * FROM projects WHERE id = $id";
$result_project = $conn->query($sql_project);
if ($result_project && $result_project->num_rows > 0) {
    $row = $result_project->fetch_assoc();
    $projectName = $row['project_name'];
    $description  = $row['description'] ?? '';
    $status       = $row['status'] ?? '';
    $priority     = $row['priority'] ?? '';
} else {
    echo "Project not found.";
    exit;
}

$sql_assigned = "
    SELECT GROUP_CONCAT(DISTINCT u.username SEPARATOR ', ') AS assigned_users
    FROM tasks t
    LEFT JOIN task_assigned_users tau ON t.id = tau.task_id
    LEFT JOIN users u ON tau.user_id = u.id
    WHERE t.project_id = $id
    GROUP BY t.project_id
";
$result_assigned = $conn->query($sql_assigned);
if ($result_assigned && $result_assigned->num_rows > 0) {
    $row_assigned = $result_assigned->fetch_assoc();
    $assignedUsers = $row_assigned['assigned_users'] ?? 'No Users Assigned';
} else {
    $assignedUsers = 'No Users Assigned';
}

include 'INCLUDES/inc_projectview.php';

include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
