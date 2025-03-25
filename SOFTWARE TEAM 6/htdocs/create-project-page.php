<?php
/*
This file creates a new project in the projects table.
For POST requests, it gathers user inputs (project_name, description, status, priority) from the form,
constructs an INSERT statement, and inserts the new project.
On success, it redirects to the view-project-page for the new project.
On GET requests, it displays the form.
*/
$title = 'ROCU: Create Project';
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $conn->real_escape_string($_POST['project_name']);
    $description  = $conn->real_escape_string($_POST['description']);
    $status       = $conn->real_escape_string($_POST['status']);
    $priority     = $conn->real_escape_string($_POST['priority']);

    $sql = "INSERT INTO projects (project_name, description, status, priority)
            VALUES ('$projectName', '$description', '$status', '$priority')";
    if ($conn->query($sql) === TRUE) {
        $project_id = $conn->insert_id;
        header("Location: view-project-page.php?id=$project_id");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    include 'INCLUDES/inc_projectcreate.php';
}

include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
