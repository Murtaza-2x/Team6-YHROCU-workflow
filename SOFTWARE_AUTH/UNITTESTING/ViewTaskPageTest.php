<?php
/*
-------------------------------------------------------------
File: ViewTaskPageTest.php
Description:
- PHPUnit tests for view-task-page.php.
- Tests that:
    > An invalid task ID returns a JSON error message "Invalid task ID".
    > A nonexistent task ID returns a JSON error message "Task not found".
    > A valid task ID returns the correct task details.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class ViewTaskPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up a fresh database connection.
        $this->setUpDatabase();
        // Clear GET parameters and simulate a logged-in user.
        $_GET = [];
        $this->fakeAuth0User();
        
        // Clean up previous dummy records.
        $this->conn->query("DELETE FROM tasks WHERE id = 1");
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        
        // Insert a dummy project.
        $projectName = "Test Project";
        $queryProject = "INSERT INTO projects (id, project_name) VALUES (1, ?)";
        $stmtProject = $this->conn->prepare($queryProject);
        $stmtProject->bind_param("s", $projectName);
        $stmtProject->execute();
        $stmtProject->close();
        
        // Insert a dummy task (id = 1) referencing the project.
        $dummySubject     = "Test Task Subject";
        $dummyDescription = "Test task description.";
        $dummyProjectId   = 1;
        $dummyStatus      = "New";
        $dummyPriority    = "Urgent";
        $dummyCreatedBy   = $_SESSION['user']['user_id'] ?? 'auth0|testuser123';
        $queryTask = "INSERT INTO tasks (id, subject, description, project_id, status, priority, created_by) VALUES (1, ?, ?, ?, ?, ?, ?)";
        $stmtTask = $this->conn->prepare($queryTask);
        $stmtTask->bind_param("ssisss", $dummySubject, $dummyDescription, $dummyProjectId, $dummyStatus, $dummyPriority, $dummyCreatedBy);
        $stmtTask->execute();
        $stmtTask->close();
    }

    protected function tearDown(): void
    {
        // Clean up dummy records.
        $this->conn->query("DELETE FROM tasks WHERE id = 1");
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $this->tearDownDatabase();
    }

    public function testInvalidTaskIdShowsError()
    {
        $_GET['id'] = '';  // Invalid task ID (empty)
        
        // Capture the output.
        ob_start();
        include __DIR__ . '/../view-task-page.php';
        $output = ob_get_clean();
        
        // Parse JSON output.
        $jsonOutput = json_decode($output, true);
        $this->assertNotNull($jsonOutput, "Output is not valid JSON.");
        $this->assertArrayHasKey('error', $jsonOutput, "Error key is missing in output.");
        $this->assertEquals('Invalid task ID', $jsonOutput['error']);
    }
    
    public function testNonexistentTaskShowsError()
    {
        $_GET['id'] = "99999";  // Nonexistent task ID
        
        // Capture the output.
        ob_start();
        include __DIR__ . '/../view-task-page.php';
        $output = ob_get_clean();
        
        // Parse JSON output.
        $jsonOutput = json_decode($output, true);
        $this->assertNotNull($jsonOutput, "Output is not valid JSON.");
        $this->assertArrayHasKey('error', $jsonOutput, "Error key is missing in output.");
        $this->assertEquals('Task not found', $jsonOutput['error']);
    }
    
    public function testViewTaskPageDisplaysTask()
    {
        $_GET['id'] = "1";  // Valid task ID
        
        // Capture the output.
        $output = $this->captureOutput(__DIR__ . '/../view-task-page.php');
        $outputData = json_decode($output, true);
        $this->assertEquals("1", $outputData['taskId']);
        $this->assertStringContainsString("Test Task Subject", $outputData['task']['subject']);
        $this->assertStringContainsString("Test task description.", $outputData['task']['description']);
    }
}