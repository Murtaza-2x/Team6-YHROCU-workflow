<?php
/*
-------------------------------------------------------------
File: CreateTaskPageTest.php
Description:
- Tests the create-task-page.php functionality.
- Verifies access control, form validation, and task creation.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class CreateTaskPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();

        // Insert dummy project (ID 1)
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $stmt = $this->conn->prepare("INSERT INTO projects (id, project_name, created_by) VALUES (1, 'Test Project', 'auth0|testuser123')");
        $stmt->execute();
        $stmt->close();
    }

    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM tasks WHERE subject = 'Test Task'");
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $this->tearDownDatabase();
    }

    public function testAccessDeniedForGuest()
    {
        $this->clearAuth0Session();
        $output = $this->captureOutput(__DIR__ . '/../create-task-page.php');
        $this->assertStringContainsString("Please log in", $output);
    }

    public function testAccessGrantedForAdmin()
    {
        $this->fakeAuth0User(['role' => 'admin']);
        $output = $this->captureOutput(__DIR__ . '/../create-task-page.php');
        $this->assertStringContainsString("Create Task", $output);
    }

    public function testInvalidFormSubmissionShowsError()
    {
        $this->fakeAuth0User(['role' => 'admin']);
        $_POST = [
            'create_task' => true,
            'subject' => '', // Missing subject
            'description' => 'Some description',
            'project_id' => 1,
            'status' => 'New',
            'priority' => 'High',
        ];

        $output = $this->captureOutput(__DIR__ . '/../create-task-page.php');
        $this->assertStringContainsString("Please fill in all required fields", $output);
    }

    public function testTaskCreationWorks()
    {
        $this->fakeAuth0User(['role' => 'admin']);
        $_POST = [
            'create_task' => true,
            'subject' => 'Test Task',
            'description' => 'Some description',
            'project_id' => 1,
            'status' => 'New',
            'priority' => 'High',
        ];

        $output = $this->captureOutput(__DIR__ . '/../create-task-page.php');
        $this->assertStringContainsString("Task created successfully", $output);

        // Confirm task exists
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE subject = ?");
        $stmt->bind_param("s", $_POST['subject']);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        $this->assertNotEmpty($task, "Task should be present in the database.");
    }
}
