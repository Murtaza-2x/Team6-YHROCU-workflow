<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

/*
-------------------------------------------------------------
File: ProjectLogsTest.php
Description:
- Tests view-project-logs-page.php in test mode for JSON responses:
   • Unauthorized access, invalid project ID, and nonexistent project.
   • Also tests mock logs returned.
- Additionally, tests CSV export in production mode using force_prod=1,
  and performs multiple substring checks on the CSV header.
-------------------------------------------------------------
*/

class ProjectLogsTest extends BaseTestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Set a default staff user with admin privileges for testing
        $_SESSION['user'] = [
            'role'     => 'admin',
            'user_id'  => 'auth0|adminUser'
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
        $_GET['id'] = '1';
        $output = $this->captureOutput(__DIR__ . '/test_files/view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertEquals("You are not authorized", $json['error']);
    }

    /**
     * Test: Invalid Project ID
     * Verifies that providing an empty project ID returns a JSON error "Invalid project ID".
     */
    public function testInvalidProjectId()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = ''; // Empty project ID
        $output = $this->captureOutput(__DIR__ . '/test_files/view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertEquals("Invalid project ID", $json['error']);
    }

    /**
     * Test: Nonexistent Project
     * Verifies that a project ID of "99999" returns a JSON error "Project not found".
     */
    public function testNonexistentProject()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '99999'; // Simulate a nonexistent project
        $output = $this->captureOutput(__DIR__ . '/test_files/view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertEquals("Project not found", $json['error']);
    }

    /**
     * Test: No Logs Found
     * Verifies that when there are no logs in the system for a given project,
     * the JSON response returns an info message "No logs found".
     */
    public function testNoLogsFound()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1'; // Assume no logs exist for project ID 1
        $output = $this->captureOutput(__DIR__ . '/test_files/view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");
        $this->assertEquals("No logs found", $json['info']);
    }

    /**
     * Test: Mock Logs Returned
     * Verifies that when the mock_logs flag is provided, the JSON response includes
     * both projectLogs and taskLogs arrays with the expected dummy data.
     */
    public function testMockLogsReturned()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1';
        $_GET['mock_logs'] = '1'; // Trigger mock logs response
        $output = $this->captureOutput(__DIR__ . '/test_files/view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json, "Output should be valid JSON.");

        // Check project logs
        $this->assertArrayHasKey("projectLogs", $json, "Project logs key missing.");
        $this->assertCount(1, $json['projectLogs'], "Expected one project log.");
        $this->assertEquals("Old Project Name", $json['projectLogs'][0]['project_name'], "Project log name mismatch.");

        // Check task logs
        $this->assertArrayHasKey("taskLogs", $json, "Task logs key missing.");
        $this->assertCount(1, $json['taskLogs'], "Expected one task log.");
        $this->assertEquals("Old Task Subject", $json['taskLogs'][0]['subject'], "Task log subject mismatch.");
    }

    /**
     * Test: CSV Export in Production Mode
     * Forces production mode using force_prod=1 and triggers CSV export via export=1.
     * Verifies that the CSV output includes the Excel BOM and key column headers.
     */
    public function testCsvExportProductionMode()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '555';          // Valid numeric project ID
        $_GET['force_prod'] = '1';    // Force production mode (bypass JSON branch)
        $_GET['export'] = '1';        // Trigger CSV export

        $output = $this->captureOutput(__DIR__ . '/test_files/view-project-logs-page.php');

        // Check that the CSV output includes the BOM (Byte Order Mark) for Excel
        $this->assertStringContainsString("\xEF\xBB\xBF", $output, "Missing BOM in CSV output");

        // Instead of matching the entire header line, check for individual header names
        $this->assertStringContainsString("Edited By", $output);
        $this->assertStringContainsString("Created By", $output);
        $this->assertStringContainsString("Archived At", $output);
        $this->assertStringContainsString("Project Name", $output);
        $this->assertStringContainsString("Status", $output);
        $this->assertStringContainsString("Priority", $output);
        $this->assertStringContainsString("Due Date", $output);
        $this->assertStringContainsString("Description", $output);
    }
}