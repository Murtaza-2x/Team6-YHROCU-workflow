<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

/*
-------------------------------------------------------------
File: EditTaskPageTest.php
Description:
- Tests the edit-task-page.php functionality using normal HTML output.
- Relies on role_helper "is_logged_in()" and "is_staff()" checks.
- Asserts on <p class='ERROR-MESSAGE'> or <p class='SUCCESS-MESSAGE'> text.
-------------------------------------------------------------
*/

class EditTaskPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    protected $taskId = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();

        // Insert a dummy project
        $this->conn->query("INSERT INTO projects (id, project_name) VALUES (99, 'Test Project')");
        // Insert a dummy task
        $stmt = $this->conn->prepare("
            INSERT INTO tasks (subject, status, priority, description, project_id, created_by)
            VALUES ('DummyTask','New','High','SomeDescription',99,'auth0|tester')
        ");
        $stmt->execute();
        $this->taskId = $this->conn->insert_id;
        $stmt->close();
    }

    protected function tearDown(): void
    {
        // Clean up dummy data
        $this->conn->query("DELETE FROM tasks WHERE id={$this->taskId}");
        $this->conn->query("DELETE FROM projects WHERE id=99");

        $this->tearDownDatabase();
    }

    public function testGuestIsDenied()
    {
        // No user in session => should see "You are not authorized..."
        $this->clearAuth0Session();

        // Force GET
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // We pass a valid numeric id
        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=' . $this->taskId);

        // Check for the denial message in HTML
        $this->assertStringContainsString("You are not authorized to view this page.", $output);
    }

    public function testInvalidTaskId()
    {
        // Simulate a staff user
        $this->fakeAuth0User(['role' => 'Admin']); // or a role that is_staff()
        
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // pass a non-numeric ID => "Invalid task ID."
        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=bogus');
        $this->assertStringContainsString("Invalid task ID.", $output);
    }

    public function testTaskNotFound()
    {
        // If we pass a numeric but non-existent ID => "Task not found."
        $this->fakeAuth0User(['role' => 'Admin']);
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $nonExistentId = 999999;
        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id='.$nonExistentId);
        $this->assertStringContainsString("Task not found.", $output);
    }

    public function testAllFieldsRequired()
    {
        // Admin user => we pass an empty subject => "All fields are required."
        $this->fakeAuth0User(['role' => 'Admin']);
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_task' => true,
            'subject'     => '', // triggers error
            'project_id'  => 99,
            'status'      => 'In Progress',
            'priority'    => 'Medium',
            'description' => 'Test desc'
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id='.$this->taskId);
        $this->assertStringContainsString("All fields are required.", $output);
    }

    public function testUpdateSuccess()
    {
        // Admin user => pass all fields => "Task updated successfully."
        $this->fakeAuth0User(['role' => 'Admin']);
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_task' => true,
            'subject'     => 'Updated Task Title',
            'project_id'  => 99,
            'status'      => 'Complete',
            'priority'    => 'Low',
            'description' => 'New description',
            'assign'      => []
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id='.$this->taskId);

        $this->assertStringContainsString("Task updated successfully.", $output);

        // Confirm DB
        $stmt = $this->conn->prepare("SELECT subject, status, priority, description FROM tasks WHERE id=?");
        $stmt->bind_param("i", $this->taskId);
        $stmt->execute();
        $res = $stmt->get_result();
        $task = $res->fetch_assoc();
        $this->assertEquals('Updated Task Title', $task['subject']);
        $this->assertEquals('Complete', $task['status']);
        $this->assertEquals('Low', $task['priority']);
        $this->assertEquals('New description', $task['description']);
    }
}