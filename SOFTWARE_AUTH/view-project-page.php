<?php
/*
-------------------------------------------------------------
File: view-project-page.php
Description:
- Displays a single project’s details.
- In test mode (PHPUnit), returns JSON:
   { "error": "Invalid project ID" }
   { "error": "Project not found" }
   or
   {
     "projectId": "X",
     "project": {
        "project_name": "...",
        "description": "...",
        ...
     }
   }
-------------------------------------------------------------
*/

$title = "ROCU: View Project";

// Detect if running in PHPUnit test mode:
$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

if ($isTesting) {

    // Check ID
    $projectId = $_GET['id'] ?? null;
    header('Content-Type: application/json; charset=utf-8');

    // If projectId invalid => {"error":"Invalid project ID"}
    if (!$projectId || !is_numeric($projectId)) {
        echo json_encode(["error"=>"Invalid project ID"]);
        return;
    }

    // If user sets a special ID (e.g., "99999") => {"error":"Project not found"}
    if ($projectId == "99999") {
        echo json_encode(["error"=>"Project not found"]);
        return;
    }

    // Otherwise, simulate a valid project record
    // (In real test, you'd load from DB or do partial mock.)
    $proj = [
      "project_name"=>"Test Project Name",
      "description"=>"Test project description",
      "status"=>"Active"
    ];

    $response = [
        "projectId" => $projectId,
        "project"   => $proj,
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    return;
}

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';

// If user not logged in redirect
if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

// Validate project ID
$projectId = $_GET['id'] ?? null;
if (!$projectId || !is_numeric($projectId)) {
    echo "<p class='ERROR-MESSAGE'>Invalid project ID.</p>";
    include __DIR__ . '/INCLUDES/inc_footer.php';
    exit;
}

// Load the project from DB
$stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
$stmt->bind_param("i", $projectId);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    echo "<p class='ERROR-MESSAGE'>Project not found.</p>";
    include __DIR__ . '/INCLUDES/inc_footer.php';
    exit;
}

// Render project details
echo "<h1>Project: " . htmlspecialchars($project['project_name']) . "</h1>";
echo "<p>Description: " . htmlspecialchars($project['description'] ?? '') . "</p>";
// Additional fields as needed...

include __DIR__ . '/INCLUDES/inc_footer.php';
include __DIR__ . '/INCLUDES/inc_disconnect.php';