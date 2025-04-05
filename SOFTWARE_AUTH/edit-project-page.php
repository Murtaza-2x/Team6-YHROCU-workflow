<?php
/*
-------------------------------------------------------------
File: edit-project-page.php
Description:
- Displays and handles project editing.
- Admins can:
    > Edit project name, status, priority, description, due date.
    > Archive previous version before updating.
    > View assigned users aggregated from tasks.
-------------------------------------------------------------
*/

$title = "ROCU: Edit Project";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/Auth0UserFetcher.php';

if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

$projectId = $_GET['id'] ?? null;

if (!$projectId || !is_numeric($projectId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid project ID.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Get project info
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $projectId);
$stmt->execute();
$res = $stmt->get_result();
$project = $res->fetch_assoc();

if (!$project) {
    echo "<p class='ERROR-MESSAGE'>Project not found.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Archive and Update project details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
    $newName        = trim($_POST['project_name'] ?? '');
    $newStatus      = trim($_POST['status'] ?? '');
    $newPriority    = trim($_POST['priority'] ?? '');
    $newDescription = trim($_POST['description'] ?? '');
    $newDueDate     = trim($_POST['due_date'] ?? '');
    $editor         = $_SESSION['user']['user_id'] ?? '';

    if (empty($newName) || empty($newStatus) || empty($newPriority) || empty($newDescription) || empty($newDueDate)) {
        echo "<p class='ERROR-MESSAGE'>All fields are required.</p>";
    } else {
        // Archive previous project version
        $stmtArchive = $conn->prepare("INSERT INTO project_archive (project_id, created_at, project_name, status, priority, due_date, description, edited_by, created_by)
        SELECT id, created_at, project_name, status, priority, due_date, description, ?, created_by
        FROM projects
        WHERE id = ?");
        $stmtArchive->bind_param("si", $editor, $projectId);
        $stmtArchive->execute();

        // Update project data
        $stmtUpdate = $conn->prepare("UPDATE projects SET project_name=?, status=?, priority=?, description=?, due_date=? WHERE id=?");
        $stmtUpdate->bind_param("sssssi", $newName, $newStatus, $newPriority, $newDescription, $newDueDate, $projectId);
        if ($stmtUpdate->execute()) {
            echo "<p class='SUCCESS-MESSAGE'>Project updated and archived. Redirecting...</p>";
            echo "<script>setTimeout(function(){ window.location.href='view-project-page.php?id=" . urlencode($projectId) . "'; }, 1500);</script>";
            exit;
        } else {
            echo "<p class='ERROR-MESSAGE'>Failed to update project.</p>";
        }
    }
}

// Load assigned users (aggregated from tasks)
$assignedUsers = [];
$stmtUsers = $conn->prepare("SELECT DISTINCT tau.user_id FROM task_assigned_users tau JOIN tasks t ON tau.task_id = t.id WHERE t.project_id = ?");
$stmtUsers->bind_param("i", $projectId);
$stmtUsers->execute();
$resUsers = $stmtUsers->get_result();
while ($row = $resUsers->fetch_assoc()) {
    $assignedUsers[] = $row['user_id'];
}

// Fetch Auth0 users
$auth0_users = Auth0UserFetcher::getUsers();
$user_map = [];
foreach ($auth0_users as $u) {
    $user_map[$u['user_id']] = $u['nickname'] ?? $u['email'];
}

// Prepare project data for view
$projectName  = $project['project_name'] ?? '';
$status       = $project['status'] ?? '';
$priority     = $project['priority'] ?? '';
$description  = $project['description'] ?? '';
$due_date     = $project['due_date'] ?? '';

include __DIR__ . '/INCLUDES/inc_projectedit.php';
include __DIR__ . '/INCLUDES/inc_footer.php';
include __DIR__ . '/INCLUDES/inc_disconnect.php';