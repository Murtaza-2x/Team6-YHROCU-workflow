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
   * JSON-based checks in test mode for invalid ID, nonexistent task,
     unauthorized access, no logs, and mock logs.
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
        // Set a default staff user (manager) to pass authorization
        $_SESSION['user'] = [
            'role'     => 'manager',
            'user_id'  => 'auth0|managerUser'
        ];
    }

    /**
     * Test: Unauthorized User
     * Verifies that if a user with a non-staff role (e.g., "user") accesses the page,
     * the JSON response contains "You are not authorized".
     */
    public function testUnauthorizedUser()
    {
        $_SESSION['user']['role'] = 'user'; // Set role to non-staff
        $_GET['id'] = '1'; // Valid task ID assumed
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertEquals("You are not authorized", $json['error']);
    }

    /**
     * Test: Invalid Task ID
     * Verifies that providing an empty task ID returns a JSON error "Invalid task ID".
     */
    public function testInvalidId()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = ''; // Empty task ID is invalid
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertEquals("Invalid task ID", $json['error']);
    }

    /**
     * Test: Nonexistent Task
     * Verifies that a task ID of "99999" returns a JSON error "Task not found".
     */
    public function testNonexistentTask()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '99999'; // Simulated nonexistent task
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertEquals("Task not found", $json['error']);
    }

    /**
     * Test: No Logs Found
     * Verifies that when there are no logs in the system for a given task,
     * the JSON response returns an "info" key with "No logs found".
     */
    public function testNoLogsFound()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1'; // Assume no logs exist for task ID 1
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertEquals("No logs found", $json['info']);
    }

    /**
     * Test: Mock Logs Returned
     * Verifies that when using the mock_logs flag, the JSON response returns a logs array
     * containing one entry with expected values.
     */
    public function testMockLogsReturned()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1';
        $_GET['mock_logs'] = '1'; // Trigger mock logs response
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertArrayHasKey("logs", $json, "Logs key should exist in JSON.");
        $this->assertCount(1, $json['logs'], "There should be exactly one log returned.");
        $this->assertEquals("Old Subject", $json['logs'][0]['subject'], "The log's subject should match the mock value.");
    }

    /**
     * Test: CSV Export Production Mode
     * Forces production mode by setting force_prod=1 and export=1, then verifies that
     * the CSV export output contains the expected BOM and header fields.
     */
    public function testCsvExportProductionMode()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '123';          // Valid numeric ID
        $_GET['force_prod'] = '1';    // Force production mode (bypass JSON branch)
        $_GET['export'] = '1';        // Trigger CSV export

        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');

        // Check that the CSV output includes the Excel BOM
        $this->assertStringContainsString("\xEF\xBB\xBF", $output, "Missing BOM in CSV output");

        // Instead of matching the entire header, we check for key column names
        $this->assertStringContainsString("Edited By", $output);
        $this->assertStringContainsString("Archived At", $output);
        $this->assertStringContainsString("Created At", $output);
        $this->assertStringContainsString("Subject", $output);
        $this->assertStringContainsString("Status", $output);
        $this->assertStringContainsString("Priority", $output);
        $this->assertStringContainsString("Description", $output);
    }
}