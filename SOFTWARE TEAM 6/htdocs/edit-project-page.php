<?php
/*
This file allows users with sufficient clearance to edit an existing project.
On GET, it retrieves the project's details and displays them in an editable form.
On POST, it updates the project details in the projects table and redirects to the view page.
*/
$title = 'ROCU: Edit Project';
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid project ID.";
    exit;
}

if ($_SESSION['clearance'] === 'User') {
    echo "You do not have permission to edit projects.";
    exit;
}

$project_name = '';
$description  = '';
$status       = '';
$priority     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $conn->real_escape_string($_POST['project_name']);
    $description  = $conn->real_escape_string($_POST['description']);
    $status       = $conn->real_escape_string($_POST['status']);
    $priority     = $conn->real_escape_string($_POST['priority']);

    $sql = "UPDATE projects 
            SET project_name = '$projectName',
                description  = '$description',
                status       = '$status',
                priority     = '$priority'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: view-project-page.php?id=$id");
        exit;
    } else {
        echo "Error updating project: " . $conn->error;
    }
} else {
    $sql = "SELECT * FROM projects WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $projectName = $row['project_name'];
        $description  = $row['description'];
        $status       = $row['status'];
        $priority     = $row['priority'];
    } else {
        echo "Project not found.";
        exit;
    }
}

include 'INCLUDES/inc_projectedit.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
