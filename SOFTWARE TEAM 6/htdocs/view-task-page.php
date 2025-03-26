<?php
/*
This file provides a detailed view of a single task by retrieving its details from the `tasks` table:
1. Includes the database connection and header.
2. The `id` is retrieved from the query string and used to SELECT from `tasks`.
3. If found, the task’s details (subject, project_name, assigned users, etc.) are displayed.
4. Higher clearance can see an Edit button, plus a Cancel/Back button, etc.
*/

$title = "ROCU: View Task";

include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid task ID.";
    exit;
}

$sql = "
  SELECT t.*,
         p.project_name,
         GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
  FROM tasks t
  LEFT JOIN projects p ON t.project_id = p.id
  LEFT JOIN task_assigned_users tau ON t.id = tau.task_id
  LEFT JOIN users u ON tau.user_id = u.id
  WHERE t.id = $id
  GROUP BY t.id
";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row         = $result->fetch_assoc();
    $subject     = $row['subject'];
    $projectName = $row['project_name'] ?? 'No Project Assigned';
    $status      = $row['status'];
    $priority    = $row['priority'];
    $description = $row['description'];
    $assignedUsers = $row['assigned_users'] ?? 'No Users Assigned'; 
} else {
    echo "Task not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $comment = $conn->real_escape_string($_POST['comment']);
    $user_id = $_SESSION['id'];
    $sql_insert_comment = "INSERT INTO comments (task_id, user_id, comment) 
                           VALUES ($id, $user_id, '$comment')";
    if ($conn->query($sql_insert_comment) === TRUE) {
        header("Location: view-task-page.php?id=$id");
        exit;
    } else {
        echo "Error adding comment: " . $conn->error;
    }
}

?>

<?php include 'INCLUDES/inc_taskview.php'; ?>

<?php
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
