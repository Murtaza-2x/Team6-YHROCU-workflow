<?php
/*
-------------------------------------------------------------
File: create-project-page.php
Description:
- Allows Admins to create new projects.
- Collects:
    > Project title, status, priority, description, due date.
- Shows success or error messages and redirects.
-------------------------------------------------------------
*/

$title = "ROCU: Create Project";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';

if (!is_logged_in() || !is_staff()) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

$errorMsg = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = trim($_POST['project_name'] ?? '');
    $status       = trim($_POST['status'] ?? '');
    $priority     = trim($_POST['priority'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $due_date     = trim($_POST['due_date'] ?? '');
    $created_by   = $_SESSION['user']['user_id'] ?? '';

    if (empty($project_name) || empty($status) || empty($priority) || empty($description) || empty($due_date)) {
        $errorMsg = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO projects (project_name, status, priority, description, due_date, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $project_name, $status, $priority, $description, $due_date, $created_by);
        if ($stmt->execute()) {
            $newProjectId = $stmt->insert_id;
            echo "<p class='SUCCESS-MESSAGE'>Project created successfully. Redirecting...</p>";
            echo "<script>setTimeout(function(){ window.location.href='view-project-page.php?id=" . urlencode($newProjectId) . "'; }, 1500);</script>";
            exit;
        } else {
            $errorMsg = "Failed to create project. Please try again.";
        }
    }
}

include 'INCLUDES/inc_projectcreate.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';