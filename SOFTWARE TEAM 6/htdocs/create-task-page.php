<?php
/*
This file is responsible for creating a new task in the application’s database.

For POST requests, the code gathers user inputs (subject, project_id, assignee, status, priority, description) from the form,
checks for duplicate task names (based on subject), constructs an SQL INSERT statement, and attempts to insert the new task into the `tasks` table.
If a duplicate exists or an error occurs, an error message is displayed on the same page.
If successful, the user is redirected to a “view” page for the newly inserted task.
If it’s a GET request, the script displays an HTML form (from inc_taskcreate.php) that allows users to enter the details for a new task.
An email notification is sent to each assigned user.
*/

$title = 'ROCU: Create Task';

include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';
include 'INCLUDES/inc_email-task-create.php';

$clearance = $_SESSION['clearance'] ?? '';
if ($clearance === 'User') {
    echo "You do not have permission to create tasks.";
    exit;
}

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject     = $conn->real_escape_string($_POST['subject']);
    $project_id  = $conn->real_escape_string($_POST['project_id']);
    $status      = $conn->real_escape_string($_POST['status']);
    $priority    = $conn->real_escape_string($_POST['priority']);
    $description = $conn->real_escape_string($_POST['description']);
    $creatorId   = $_SESSION['id'];

    $duplicateQuery = "SELECT id FROM tasks WHERE subject = '$subject'";
    $dupResult = $conn->query($duplicateQuery);
    if ($dupResult && $dupResult->num_rows > 0) {
        $errorMsg = "Error: A task with the same subject already exists. Please choose a different task name.";
    } else {
        $sql = "INSERT INTO tasks (`id`, `subject`, `project_id`, `status`, `priority`, `created_by`, `description`) 
                VALUES (NULL, '$subject', '$project_id', '$status', '$priority', '$creatorId', '$description')";
        
        if ($conn->query($sql) === true) {
            $task_id = $conn->insert_id;

            if (!empty($_POST['assign'])) {
                foreach ($_POST['assign'] as $user_id) {
                    $user_id = (int)$user_id;
                    $sql_link = "INSERT INTO task_assigned_users (task_id, user_id) VALUES ($task_id, $user_id)";
                    $conn->query($sql_link);

                    $userQuery = "SELECT email FROM users WHERE id = $user_id";
                    $userResult = $conn->query($userQuery);
                    if ($userResult && $userResult->num_rows > 0) {
                        $userRow = $userResult->fetch_assoc();
                        sendTaskCreateEmail($userRow['email']);
                    }
                }
            }
            header('Location: view-task-page.php?id=' . $task_id);
            exit;
        } else {
            $errorMsg = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Display the form (for GET or if there is an error)
include 'INCLUDES/inc_taskcreate.php';

include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
