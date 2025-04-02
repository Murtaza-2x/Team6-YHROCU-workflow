<?php
/*
This file allows users with sufficient clearance to edit an existing project.
On GET, it retrieves the project's details and displays them in an editable form.
On POST, it updates the project details in the projects table and redirects to the view page.
*/

$title = 'ROCU: Edit Project';
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

// VALIDATE PROJECT ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid project ID.";
    exit;
}

// PERMISSION CHECK
if ($_SESSION['clearance'] === 'User') {
    echo "You do not have permission to edit projects.";
    exit;
}

// DEFAULT VALUES
$project_name = '';
$description  = '';
$status       = '';
$priority     = '';
$due_date     = '';

// ON FORM SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $conn->real_escape_string($_POST['project_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $status      = $conn->real_escape_string($_POST['status']);
    $priority    = $conn->real_escape_string($_POST['priority']);
    $due_date    = $conn->real_escape_string($_POST['due_date']); 

    $sql = "UPDATE projects 
            SET project_name = '$projectName',
                description  = '$description',
                status       = '$status',
                priority     = '$priority',
                DUE_DATE     = '$due_date'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $edited_by = $_SESSION['id'];
        $archive_sql = "
            INSERT INTO project_archive (
                project_id,
                project_name,
                description,
                status,
                priority,
                due_date,
                edited_by,
                archived_at
            )
            VALUES (
                $id,
                '$projectName',
                '$description',
                '$status',
                '$priority',
                '$due_date',
                $edited_by,
                NOW()
            )
        ";
        $conn->query($archive_sql); // Optional: log success/failure
        header("Location: view-project-page.php?clearance=" . urlencode($_SESSION['clearance']) . "&id=" . urlencode($id));
        exit;

    } else {
        echo "Error updating project: " . $conn->error;
    }

// ON FIRST LOAD (GET)
} else {
    $sql = "SELECT * FROM projects WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $projectName = $row['project_name'];
        $description = $row['description'];
        $status      = $row['status'];
        $priority    = $row['priority'];
        $due_date    = $row['DUE_DATE'];
    } else {
        echo "Project not found.";
        exit;
    }
}

include 'INCLUDES/inc_projectedit.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
