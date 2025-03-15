<?php
/*
This file allows the user to edit an existing task in the database. 
1. It begins by including the required connection and header files for the page. 
2. The script then checks the request method:
   - If the request is POST, it reads the ID and updated task details from the form inputs,
     constructs a SQL UPDATE statement, and updates the relevant row in the `tasks` table. If successful, it redirects to the page showing the updated task. 
   - If the request is GET, it retrieves the task's ID from the query string, 
     fetches that task’s details from the database, and pre-fills an HTML form so the user can edit the task. 
*/

$title = 'Edit Task';
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $subject = $_POST['subject'];
    $project = $_POST['project'];
    $assignee = $_POST['assignee'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];

    $sql = "UPDATE tasks SET
    subject='" . $subject . "',
    project='" . $project . "',
    assignee=" . $assignee . ",
    status='" . $status . "',
    priority='" . $priority . "'
    WHERE id = " . $id;
    // echo $sql;
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
        header('Location: view-task-page.php?id=' . $id);
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id = $_GET['id'];

    $sql = "SELECT * FROM tasks WHERE id = " . $id;
    $result = $conn->query($sql);

    $row = $result->fetch_assoc();
    echo "<form action='edit-task-page.php' method='post'>"
        . "  <input type='hidden' id='id' name='id' value=" . $row['id'] . "><br>"
        . "  <label for='subject'>Subject:</label><br>"
        . "  <input type='text' id='subject' name='subject' value=" . $row['subject'] . "><br>"
        . "  <label for='project'>Project:</label><br>"
        . "  <input type='text' id='project' name='project' value=" . $row['project'] . "><br>"
        . "  <label for='assignee'>Assignee:</label><br>"
        . "  <input type='text' id='assignee' name='assignee' value=" . $row['assignee'] . "><br>"
        . "  <label for='status'>Status:</label><br>"
        . "  <input type='text' id='status' name='status' value=" . $row['status'] . "><br>"
        . "  <label for='priority'>Priority:</label><br>"
        . "  <input type='text' id='priority' name='priority' value=" . $row['priority'] . "><br>"
        . "  <input type='submit' value='Save'>"
        . "</form>";
}
?>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>
