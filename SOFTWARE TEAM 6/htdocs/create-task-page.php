<?php 
/*
This file is responsible for creating a new task in the application’s database.

For POST requests, the code gathers user inputs (subject, project, assignee, status, priority) from the form, 
constructs an SQL INSERT statement, and attempts to insert the new task into the `tasks` table. 
If successful, the user is redirected to a “view” page for the newly inserted task. 
If it’s a GET request, the script displays an HTML form that allows users to enter the details necessary for creating a new task.
*/

$title = 'Create New Task'; 
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $project = $_POST['project'];
    $assignee = $_POST['assignee'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $sql = "INSERT INTO tasks (`id`, `subject`, `project`, `assignee`, `status`, `priority`) 
        VALUES (NULL, '" . $subject . "', '" . $project . "', '" . $assignee . "', '" . $status . "', '" . $priority . "')";
    echo $sql;
    if ($conn->query($sql) === true) {
        $id = $conn->insert_id;
        echo "New record created successfully. Last inserted ID is: " . $id;
        header('Location: view-task-page.php?id=' . $id);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "<form action='create-task-page.php' method='post'>"
        . "  <label for='subject'>Subject:</label><br>"
        . "  <input type='text' id='subject' name='subject'><br>"
        . "  <label for='project'>Project:</label><br>"
        . "  <input type='text' id='project' name='project'><br>"
        . "  <label for='assignee'>Assignee:</label><br>"
        . "  <input type='text' id='assignee' name='assignee'><br>"
        . "  <label for='status'>Status:</label><br>"
        . "  <input type='text' id='status' name='status'><br>"
        . "  <label for='priority'>Priority:</label><br>"
        . "  <input type='text' id='priority' name='priority'><br>"
        . "  <input type='submit' value='Save'>"
        . "</form>";
}
?>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>
