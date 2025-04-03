<?php
/*
-------------------------------------------------------------
File: view-project-page.php
Description:
- Displays a detailed view of a single project.
- Shows:
    > Project information (Title, Status, Priority, Description, Due Date)
    > Assigned Users aggregated from tasks linked to this project
    > Admin-only button to edit
-------------------------------------------------------------
*/

$title = "ROCU: View Project";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

// Redirect if user is not logged in
if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

// Validate project ID
$user = $_SESSION['user'];
$projectId = $_GET['id'] ?? null;

if (!$projectId || !is_numeric($projectId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid project ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Load project details from the database
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $projectId);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    echo "<p class='ERROR-MESSAGE'>Project not found.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Extract project details
$projectName = $project['project_name'];
$status      = $project['status'];
$priority    = $project['priority'];
$description = $project['description'];
$due_date    = $project['due_date'];

// Fetch Auth0 users for nickname resolution
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Get all assigned users through tasks under this project
$stmt = $conn->prepare("
    SELECT DISTINCT tau.user_id 
    FROM tasks t 
    JOIN task_assigned_users tau ON t.id = tau.task_id 
    WHERE t.project_id = ?
");
$stmt->bind_param("i", $projectId);
$stmt->execute();
$assignedResult = $stmt->get_result();
$assignedUsers = [];
while ($row = $assignedResult->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

// Render project view page
include 'INCLUDES/inc_projectview.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>