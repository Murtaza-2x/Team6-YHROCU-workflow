<?php
/*
-------------------------------------------------------------
File: CreateTaskPageTest.php
Description:
- Tests the create-task-page.php functionality.
- Checks access control, form validation, and successful task creation.
-------------------------------------------------------------*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class CreateTaskPageTest extends BaseTestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    /**
     * setUp(): Called before each test.
     * We create a project (id=1) so there's something to assign a new task to.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
        $GLOBALS['conn'] = $this->conn; // Make sure global $conn is set.

        // Remove any leftover project with id=1, then create a fresh one.
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $stmt = $this->conn->prepare("INSERT INTO projects (id, project_name, created_by) VALUES (1, 'Test Project', 'auth0|testuser123')");
        $stmt->execute();
        $stmt->close();
    }

    /**
     * tearDown(): Called after each test.
     * We remove any tasks named 'Test Task' and the project with id=1,
     * then close the DB connection.
     */
    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM tasks WHERE subject = 'Test Task'");
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $this->tearDownDatabase();
    }

    /**
     * Test: Access Denied for Guest
     *
     * If there's no user in session, the page should return JSON
     * error 'Not authorized' in test mode.
     */
    public function testAccessDeniedForGuest()
    {
        $this->clearAuth0Session();
        $output = $this->captureOutput(__DIR__ . '/test_files/create-task-page.php');
        $json = json_decode($output, true);

        // Expect 'Not authorized' if no user in session.
        $this->assertEquals('Not authorized', $json['error']);
    }

    /**
     * Test: Access Granted for Admin
     *
     * If the user is admin, they can view the form, 
     * so presumably we get no JSON error and standard HTML (meaning JSON is null).
     */
    public function testAccessGrantedForAdmin()
    {
        $this->fakeAuth0User(['role' => 'admin']);

        // Ensure it's treated as a regular page load
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_POST);

        $output = $this->captureOutput(__DIR__ . '/test_files/create-task-page.php');
        $json = json_decode($output, true);

        $this->assertNull($json, "Expected non-JSON output for admin loading page.");
    }


    /**
     * Test: Invalid Form Submission
     *
     * If we omit required fields (e.g., 'subject' is empty),
     * we expect "Please fill in all required fields." in JSON error.
     */
    public function testInvalidFormSubmissionShowsError()
    {
        $this->fakeAuth0User(['role' => 'admin']);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'create_task' => true,
            'subject' => '',
            'description' => 'Test',
            'project_id' => 1,
            'status' => 'New',
            'priority' => 'High'
        ];

        $output = $this->captureOutput(__DIR__ . '/test_files/create-task-page.php');
        $json = json_decode($output, true);

        // Expect a JSON error message from test mode.
        $this->assertEquals("Please fill in all required fields.", $json['error']);
    }

    /**
     * Test: Task Creation Works
     *
     * Submitting valid data should yield 
     * "Task created successfully." in JSON
     * and the new task should be in the DB.
     */
    public function testTaskCreationWorks()
    {
        // Admin role so we can create tasks.
        $this->fakeAuth0User(['role' => 'admin']);
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST = [
            'create_task' => true,
            'subject' => 'Test Task',
            'description' => 'Description here',
            'project_id' => 1,
            'status' => 'New',
            'priority' => 'High'
        ];

        $output = $this->captureOutput(__DIR__ . '/test_files/create-task-page.php');
        $json = json_decode($output, true);

        // Expect "Task created successfully."
        $this->assertEquals('Task created successfully.', $json['success']);

        // Confirm the task is in the DB.
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE subject = ?");
        $stmt->bind_param("s", $_POST['subject']);
        $stmt->execute();
        $result = $stmt->get_result();

        // We expect to find a record with that subject.
        $this->assertNotEmpty($result->fetch_assoc(), "Task should exist in DB.");
    }
}