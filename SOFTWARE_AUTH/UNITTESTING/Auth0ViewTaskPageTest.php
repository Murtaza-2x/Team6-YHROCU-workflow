<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class Auth0ViewTaskPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a fresh DB connection.
        $this->setUpDatabase();
        // Clear GET parameters and simulate a logged-in user.
        $_GET = [];
        $this->fakeAuth0User();
        
        // Clean up any previous dummy records to avoid duplicate key errors.
        $this->conn->query("DELETE FROM tasks WHERE id = 1");
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        
        // Insert dummy project first to satisfy the foreign key.
        $projectName = "Test Project";
        $queryProject = "INSERT INTO projects (id, project_name) VALUES (1, ?)";
        $stmtProject = $this->conn->prepare($queryProject);
        $stmtProject->bind_param("s", $projectName);
        $stmtProject->execute();
        $stmtProject->close();
        
        // Insert dummy task record (id = 1) referencing the project.
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
        try {
            $_GET['id'] = "";
            $output = $this->captureOutput(__DIR__ . '/../view-task-page.php');
            $this->assertStringContainsString("Invalid task ID", $output);
            echo "testInvalidTaskIdShowsError passed\n";
        } catch (\Throwable $e) {
            echo "testInvalidTaskIdShowsError failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function testNonexistentTaskShowsError()
    {
        try {
            $_GET['id'] = "99999";
            $output = $this->captureOutput(__DIR__ . '/../view-task-page.php');
            $this->assertStringContainsString("Task not found", $output);
            echo "testNonexistentTaskShowsError passed\n";
        } catch (\Throwable $e) {
            echo "testNonexistentTaskShowsError failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function testViewTaskPageDisplaysTask()
    {
        try {
            $_GET['id'] = "1";
            $output = $this->captureOutput(__DIR__ . '/../view-task-page.php');
            $this->assertStringContainsString("View Task", $output);
            $this->assertStringContainsString("Test Task Subject", $output);
            $this->assertStringContainsString("Test task description.", $output);
            $this->assertStringContainsString("Test Project", $output);
            echo "testViewTaskPageDisplaysTask passed\n";
        } catch (\Throwable $e) {
            echo "testViewTaskPageDisplaysTask failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}