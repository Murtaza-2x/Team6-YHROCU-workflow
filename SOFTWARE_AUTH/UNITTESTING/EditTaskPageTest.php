<?php
/*
-------------------------------------------------------------
File: EditTaskPageTest.php
Description:
- Tests the edit-task-page.php functionality.
- Uses PHPUNIT_RUNNING test mode so that errors and success responses are returned as JSON.
- Mocks Auth0UserManager to simulate Auth0 behavior.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';
require_once __DIR__ . '/../INCLUDES/Auth0UserManager.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "DEBUG: Setting error_reporting to E_ALL\n";

// Ensure PHPUNIT_RUNNING is defined BEFORE we do any output capturing
if (!defined('PHPUNIT_RUNNING')) {
    define('PHPUNIT_RUNNING', true);
    echo "DEBUG: Defined PHPUNIT_RUNNING = true in " . __FILE__ . "\n";
}

class EditTaskPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    protected int $taskId;
    protected $auth0UserManagerMock;

    protected function setUp(): void {
        echo "DEBUG: EditTaskPageTest::setUp\n";
        parent::setUp();
        $this->setUpDatabase();
        $GLOBALS['conn'] = $this->conn;

        // Create a dummy Auth0UserManager mock.
        $this->auth0UserManagerMock = $this->getMockBuilder(Auth0UserManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $GLOBALS['Auth0UserManager'] = $this->auth0UserManagerMock;

        // Clear previous dummy data.
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $this->conn->query("DELETE FROM tasks WHERE subject = 'Test Task'");

        // Insert dummy project.
        $stmt = $this->conn->prepare("INSERT INTO projects (id, project_name, created_by) VALUES (1, 'Test Project', 'auth0|testuser123')");
        $stmt->execute();
        $stmt->close();

        // Insert dummy task.
        $stmt = $this->conn->prepare("
            INSERT INTO tasks 
            (subject, status, priority, description, project_id, created_by) 
            VALUES 
            ('Test Task', 'New', 'High', 'Initial Description', 1, 'auth0|testuser123')
        ");
        $stmt->execute();
        $this->taskId = $this->conn->insert_id;
        $stmt->close();

        echo "DEBUG: Inserted dummy task ID={$this->taskId}\n";
    }

    protected function tearDown(): void {
        echo "DEBUG: EditTaskPageTest::tearDown\n";
        $this->conn->query("DELETE FROM task_archive WHERE task_id = {$this->taskId}");
        $this->conn->query("DELETE FROM task_assigned_users WHERE task_id = {$this->taskId}");
        $this->conn->query("DELETE FROM tasks WHERE id = {$this->taskId}");
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $this->tearDownDatabase();
    }

    public function testAccessDeniedForGuest() {
        echo "DEBUG: testAccessDeniedForGuest\n";
        $this->clearAuth0Session();

        // Force GET request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $_POST = [];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=' . $this->taskId);

        // Print everything captured
        echo "DEBUG OUTPUT:\n$output\n";

        $decoded = json_decode($output, true);
        $this->assertEquals('Not authorized', $decoded['error']);
    }

    public function testInvalidTaskId() {
        echo "DEBUG: testInvalidTaskId\n";
        $this->fakeAuth0User(['role' => 'admin', 'user_id' => 'auth0|testuser123']);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $_POST = [];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=invalid');
        echo "DEBUG OUTPUT:\n$output\n";

        $decoded = json_decode($output, true);
        $this->assertEquals('Invalid task ID', $decoded['error']);
    }

    public function testTaskNotFound() {
        echo "DEBUG: testTaskNotFound\n";
        $this->fakeAuth0User(['role' => 'admin', 'user_id' => 'auth0|testuser123']);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $_POST = [];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=999999');
        echo "DEBUG OUTPUT:\n$output\n";

        $decoded = json_decode($output, true);
        $this->assertEquals('Task not found', $decoded['error']);
    }

    public function testFormDisplayForAdmin() {
        echo "DEBUG: testFormDisplayForAdmin\n";
        $this->fakeAuth0User(['role' => 'admin', 'user_id' => 'auth0|testuser123']);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $_POST = [];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=' . $this->taskId);
        echo "DEBUG OUTPUT:\n$output\n";

        $this->assertStringContainsString("<form", $output);
    }

    public function testEditTaskFormValidation() {
        echo "DEBUG: testEditTaskFormValidation\n";
        $this->fakeAuth0User(['role' => 'admin', 'user_id' => 'auth0|testuser123']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET = [];
        $_POST = [
            'update_task' => true,
            'subject'     => '',  // Missing subject triggers validation error.
            'project_id'  => 1,
            'status'      => 'In Progress',
            'priority'    => 'Medium',
            'description' => 'Updated description',
            'assign'      => []
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=' . $this->taskId);
        echo "DEBUG OUTPUT:\n$output\n";

        $decoded = json_decode($output, true);
        $this->assertEquals('All fields are required', $decoded['error']);
    }

    public function testEditTaskSuccess() {
        echo "DEBUG: testEditTaskSuccess\n";
        $this->fakeAuth0User(['role' => 'admin', 'user_id' => 'auth0|testuser123']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET = [];
        $_POST = [
            'update_task' => true,
            'subject'     => 'Updated Task',
            'project_id'  => 1,
            'status'      => 'In Progress',
            'priority'    => 'Medium',
            'description' => 'Updated description',
            'assign'      => []
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=' . $this->taskId);
        echo "DEBUG OUTPUT:\n$output\n";

        $decoded = json_decode($output, true);
        $this->assertEquals('Task updated successfully', $decoded['success']);

        // Check DB
        $stmt = $this->conn->prepare("SELECT subject, description FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $this->taskId);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        $this->assertEquals('Updated Task', $task['subject']);
        $this->assertEquals('Updated description', $task['description']);
    }

    public function testEditTaskWithAssignedUserAndEmail() {
        echo "DEBUG: testEditTaskWithAssignedUserAndEmail\n";
        $this->fakeAuth0User(['role' => 'admin', 'user_id' => 'auth0|testuser123']);

        $assignedUserId = 'auth0|12345';

        // Expect the mock to return an email
        $this->auth0UserManagerMock->expects($this->once())
            ->method('getUser')
            ->with($this->equalTo($assignedUserId))
            ->willReturn(['email' => 'assigned@example.com']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET = [];
        $_POST = [
            'update_task' => true,
            'subject'     => 'Task With Assigned User',
            'project_id'  => 1,
            'status'      => 'In Progress',
            'priority'    => 'Medium',
            'description' => 'Updated description with assignment',
            'assign'      => [$assignedUserId]
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php?id=' . $this->taskId);
        echo "DEBUG OUTPUT:\n$output\n";

        $decoded = json_decode($output, true);
        $this->assertEquals('Task updated successfully', $decoded['success']);
    }
}