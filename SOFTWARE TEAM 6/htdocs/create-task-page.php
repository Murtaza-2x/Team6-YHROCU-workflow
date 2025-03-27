<?php
/*
This file is responsible for creating a new task in the application’s database.

For POST requests, the code gathers user inputs (subject, project, assignee, status, priority) from the form, 
constructs an SQL INSERT statement, and attempts to insert the new task into the `tasks` table. 
If successful, the user is redirected to a “view” page for the newly inserted task. 
If it’s a GET request, the script displays an HTML form that allows users to enter the details necessary for creating a new task.
An email notification is sent to a fake server when a task is created to simulate task creation notifications.
*/

$title = 'ROCU: Create Task';

include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';
include 'INCLUDES/inc_email-task-create.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject      = $conn->real_escape_string($_POST['subject']);
    $project_id   = $conn->real_escape_string($_POST['project_id']);
    $status       = $conn->real_escape_string($_POST['status']);
    $priority     = $conn->real_escape_string($_POST['priority']);
    $description  = $conn->real_escape_string($_POST['description']);

    $creatorId = $_SESSION['id'];

    $sql = "INSERT INTO tasks (`id`, `subject`, `project`, `status`, `priority`, `created_by`, `description` ) 
        VALUES (NULL, '" . $subject . "', '" . $project . "', '" . $status . "', '" . $priority . "', '" . $creatorId . "', '" . $description . "')";

    echo $sql;
    if ($conn->query($sql) === true) {
        $task_id = $conn->insert_id;

        if (!empty($_POST['assign'])) {
            foreach ($_POST['assign'] as $user_id) {
                $user_id = (int)$user_id;

                $sql_link = "INSERT INTO task_assigned_users (task_id, user_id)
                             VALUES ($task_id, $user_id)";
                $conn->query($sql_link);

                $userQuery = "SELECT email FROM users WHERE id = $user_id";
                $userResult = $conn->query($userQuery);
                if ($userResult && $userResult->num_rows > 0) {
                    $userRow = $userResult->fetch_assoc();
                    sendTaskEmail($userRow['email']);
                }
            }
        }

        echo "New record created successfully. Last inserted ID is: " . $id;
        header('Location: view-task-page.php?id=' . $id);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include 'INCLUDES/inc_taskcreate.php';
}
?>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>
