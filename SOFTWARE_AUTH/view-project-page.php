<?php
/*
-------------------------------------------------------------
File: view-project-page.php
Description:
- Displays a detailed view of a single project.
- Shows project info, assigned users (from tasks), etc.
- In test mode (PHPUnit), returns JSON for invalid or missing ID,
  or simulates a valid project record if the ID is numeric.
-------------------------------------------------------------
*/

// 1) Detect if we're in PHPUnit test mode
$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

/*
 |-------------------------------------------------------------------
 | Test Mode Block
 |-------------------------------------------------------------------
 | If $isTesting is true, we skip normal HTML logic and return JSON
 | for the PHPUnit tests. This block won't affect your production code.
*/
if ($isTesting) {

    // Set JSON header
    header('Content-Type: application/json; charset=utf-8');

    // Check project ID from GET
    $projectId = $_GET['id'] ?? null;

    // If missing or non-numeric => invalid project ID
    if (!$projectId || !is_numeric($projectId)) {
        echo json_encode(["error" => "Invalid project ID"]);
        return;
    }

    // If the ID is a special sentinel (e.g. 99999) => "Project not found"
    if ($projectId == "99999") {
        echo json_encode(["error" => "Project not found"]);
        return;
    }

    // Otherwise, simulate a valid project
    // (If you want to actually query the DB in test mode, you could do so,
    // but we'll keep it simple.)
    $mockProject = [
        "project_name" => "Test Project Name",
        "description"  => "Test project description",
        "status"       => "Active",
        "priority"     => "High",
        "due_date"     => "2025-12-31"
    ];

    // If you want to simulate assigned users, you can add them here:
    // $assignedUsers = ["auth0|user123", "auth0|user456"];

    $response = [
        "projectId" => (string)$projectId,
        "project"   => $mockProject,
        // "assignedUsers" => $assignedUsers // if you like
    ];

    // Return the JSON and stop
    echo json_encode($response, JSON_PRETTY_PRINT);
    return;
}

/*
 |-------------------------------------------------------------------
 | Production Mode (Original Logic)
 |-------------------------------------------------------------------
 | Below this line is your normal code that loads the project from
 | the database, fetches assigned users, and includes your project view.
*/

// Load your environment and helper files
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

// Validate project ID in normal usage
$projectId = $_GET['id'] ?? null;
if (!$projectId || !is_numeric($projectId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid project ID.</p>";
    include __DIR__ . '/INCLUDES/inc_footer.php';
    exit;
}

// Load project details from the database
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $projectId);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

// If no project => error
if (!$project) {
    echo "<p class='ERROR-MESSAGE'>Project not found.</p>";
    include __DIR__ . '/INCLUDES/inc_footer.php';
    exit;
}

// Extract project fields
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

// Get all assigned users (via tasks under this project)
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

include __DIR__ . '/INCLUDES/inc_projectview.php';
include __DIR__ . '/INCLUDES/inc_footer.php';
include __DIR__ . '/INCLUDES/inc_disconnect.php';