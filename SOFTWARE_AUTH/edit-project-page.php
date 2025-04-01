<?php
/*
-------------------------------------------------------------
File: edit-project-page.php
Description:
- Handles editing of a single project.
- Displays:
    > Project title, status, priority, and description.
    > Assigned users aggregated from tasks.
    > Allows Admins to update project info.
-------------------------------------------------------------
*/

$title = "ROCU: Edit Project";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

if (!has_role('Admin')) {
    header('Location: index.php?error=1&msg=Not authorized.');
    exit;
}

$projectId = $_GET['id'] ?? null;

if (!$projectId || !is_numeric($projectId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid project ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Get project data
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

// Project Details
$projectName = $project['project_name'];
$status      = $project['status'];
$priority    = $project['priority'];
$description = $project['description'];

// Aggregated Assigned Users
$assignedUsers = [];
$stmtUsers = $conn->prepare("SELECT DISTINCT tau.user_id FROM task_assigned_users tau 
                             JOIN tasks t ON tau.task_id = t.id 
                             WHERE t.project_id = ?");
$stmtUsers->bind_param("i", $projectId);
$stmtUsers->execute();
$resUsers = $stmtUsers->get_result();
while ($row = $resUsers->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {

    $newName = trim($_POST['project_name'] ?? '');
    $newStatus = trim($_POST['status'] ?? '');
    $newPriority = trim($_POST['priority'] ?? '');
    $newDescription = trim($_POST['description'] ?? '');

    if (empty($newName) || empty($newStatus) || empty($newPriority) || empty($newDescription)) {
        echo "<p class='ERROR-MESSAGE'>All fields are required.</p>";
    } else {
        $stmtUpdate = $conn->prepare("UPDATE projects SET project_name = ?, status = ?, priority = ?, description = ? WHERE id = ?");
        $stmtUpdate->bind_param("ssssi", $newName, $newStatus, $newPriority, $newDescription, $projectId);

        if ($stmtUpdate->execute()) {
            echo "<p class='SUCCESS-MESSAGE'>Project updated successfully. Redirecting...</p>";
            echo "<script>setTimeout(function(){ window.location.href='view-project-page.php?id=" . urlencode($projectId) . "'; }, 1500);</script>";
            exit;
        } else {
            echo "<p class='ERROR-MESSAGE'>Failed to update project. Please try again.</p>";
        }
    }
}


include 'INCLUDES/inc_projectedit.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
?>
