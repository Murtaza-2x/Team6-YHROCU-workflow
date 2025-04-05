<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

/*
-------------------------------------------------------------
File: TaskLogsTest.php
Description:
- Tests view-task-logs-page.php:
   * JSON-based checks in test mode for invalid ID, nonexistent task, unauthorized access, no logs, and mock logs.
   * CSV export in production mode with multiple substring checks.
-------------------------------------------------------------
*/

class TaskLogsTest extends BaseTestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // By default, set a staff user (manager)
        $_SESSION['user'] = [
            'role'     => 'manager',
            'user_id'  => 'auth0|managerUser'
        ];
    }

    // Test: Unauthorized user => "You are not authorized"
    public function testUnauthorizedUser()
    {
        $_SESSION['user']['role'] = 'user'; // not staff
        $_GET['id'] = '1';
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("You are not authorized", $json['error']);
    }

    // Test: Invalid ID => "Invalid task ID"
    public function testInvalidId()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = ''; // invalid
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("Invalid task ID", $json['error']);
    }

    // Test: Nonexistent task returns "Task not found"
    public function testNonexistentTask()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '99999'; // treated as nonexistent
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("Task not found", $json['error']);
    }

    // Test: No logs => "No logs found"
    public function testNoLogsFound()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1'; // assuming no logs exist for this task
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("No logs found", $json['info']);
    }

    // Test: When mock logs are returned, we expect a logs array with one entry
    public function testMockLogsReturned()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1';
        $_GET['mock_logs'] = '1'; // trigger mock logs response
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertArrayHasKey("logs", $json);
        $this->assertCount(1, $json['logs']);
        $this->assertEquals("Old Subject", $json['logs'][0]['subject']);
    }

    // Test: CSV export in production mode (force production mode with force_prod=1)
    public function testCsvExportProductionMode()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '123';          // valid numeric ID
        $_GET['force_prod'] = '1';    // force production mode (bypass JSON branch)
        $_GET['export'] = '1';        // trigger CSV export

        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');

        // Check for BOM in CSV output
        $this->assertStringContainsString("\xEF\xBB\xBF", $output, "Missing BOM in CSV output");

        // Instead of exact header match, check for individual headers
        $this->assertStringContainsString("Edited By", $output);
        $this->assertStringContainsString("Archived At", $output);
        $this->assertStringContainsString("Created At", $output);
        $this->assertStringContainsString("Subject", $output);
        $this->assertStringContainsString("Status", $output);
        $this->assertStringContainsString("Priority", $output);
        $this->assertStringContainsString("Description", $output);
    }
}