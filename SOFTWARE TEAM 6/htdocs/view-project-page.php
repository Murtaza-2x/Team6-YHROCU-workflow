<?php
/*
This file displays a single project's details by retrieving data from the projects table.
It shows project_name (subject), description, status, and priority.
If the project is found, its details are displayed.
An Edit button is available (for higher-clearance users) to go to the edit-project page.
*/
$title = "ROCU: View Project";
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid project ID.";
    exit;
}

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

include 'INCLUDES/inc_projectview.php';

include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
