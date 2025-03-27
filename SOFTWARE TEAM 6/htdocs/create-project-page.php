<?php
/*
This file creates a new project in the projects table.

For POST requests, it gathers user inputs (project_name, description, status, priority) from the form,
checks for duplicate project names (based on project_name),
constructs an INSERT statement, and inserts the new project into the `projects` table.
If a duplicate exists or an error occurs, an error message is displayed on the same page.
If successful, the user is redirected to the view-project-page for the new project.
For GET requests, it displays an HTML form for creating a new project.
*/

$title = 'ROCU: Create Project';
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

$clearance = $_SESSION['clearance'] ?? '';
if ($clearance === 'User') {
    echo "You do not have permission to create projects.";
    exit;
}

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $conn->real_escape_string($_POST['project_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $status      = $conn->real_escape_string($_POST['status']);
    $priority    = $conn->real_escape_string($_POST['priority']);

    $duplicateQuery = "SELECT id FROM projects WHERE project_name = '$projectName'";
    $dupResult = $conn->query($duplicateQuery);
    if ($dupResult && $dupResult->num_rows > 0) {
        $errorMsg = "Error: A project with the same name already exists. Please choose a different project name.";
    } else {
        $sql = "INSERT INTO projects (project_name, description, status, priority)
                VALUES ('$projectName', '$description', '$status', '$priority')";
        if ($conn->query($sql) === TRUE) {
            $project_id = $conn->insert_id;
            header("Location: view-project-page.php?id=$project_id");
            exit;
        } else {
            $errorMsg = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' || !empty($errorMsg)) {
    include 'INCLUDES/inc_projectcreate.php';
}

include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
