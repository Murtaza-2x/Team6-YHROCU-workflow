<?php
/*
This file is responsible for creating a new task in the application’s database.

For POST requests, the code gathers user inputs (subject, project_id, assignee, status, priority, description) from the form,
constructs an SQL INSERT statement, and attempts to insert the new task into the `tasks` table.
If successful, the user is redirected to a “view” page for the newly inserted task.
If it’s a GET request, the script displays an HTML form that allows users to enter the details necessary for creating a new task.
*/

$title = 'ROCU: Create Task';

include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject      = $_POST['subject'];
    $project_id   = $_POST['project_id'];
    $status       = $_POST['status'];
    $priority     = $_POST['priority'];
    $description  = $_POST['description'];

    $creatorId    = $_SESSION['id'];

    $sql = "INSERT INTO tasks (`id`, `subject`, `project_id`, `status`, `priority`, `created_by`, `description`) 
            VALUES (NULL, '" . $subject . "', '" . $project_id . "', '" . $status . "', '" . $priority . "', '" . $creatorId . "', '" . $description . "')";
    
    if ($conn->query($sql) === true) {
        $task_id = $conn->insert_id;

        if (!empty($_POST['assign'])) {
            foreach ($_POST['assign'] as $user_id) {
                $user_id = (int)$user_id;
                $sql_link = "INSERT INTO task_assigned_users (task_id, user_id) VALUES ($task_id, $user_id)";
                $conn->query($sql_link);
            }
        }

        header('Location: view-task-page.php?id=' . $task_id);
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include 'INCLUDES/inc_taskcreate.php';
}
?>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>
