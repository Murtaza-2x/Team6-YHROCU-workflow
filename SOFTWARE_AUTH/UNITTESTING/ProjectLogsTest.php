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
        // Set a default staff user for testing
        $_SESSION['user'] = [
            'role'     => 'admin',
            'user_id'  => 'auth0|adminUser'
        ];
    }

    public function testUnauthorizedUser()
    {
        $_SESSION['user']['role'] = 'user'; // not staff
        $_GET['id'] = '1';
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("You are not authorized", $json['error']);
    }

    public function testInvalidProjectId()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = ''; // invalid project ID
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("Invalid project ID", $json['error']);
    }

    public function testNonexistentProject()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '99999'; // project not found
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("Project not found", $json['error']);
    }

    public function testNoLogsFound()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1'; // assume no logs exist for project ID 1
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("No logs found", $json['info']);
    }

    public function testMockLogsReturned()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1';
        $_GET['mock_logs'] = '1'; // trigger mock logs
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertArrayHasKey("projectLogs", $json);
        $this->assertCount(1, $json['projectLogs']);
        $this->assertEquals("Old Project Name", $json['projectLogs'][0]['project_name']);

        $this->assertArrayHasKey("taskLogs", $json);
        $this->assertCount(1, $json['taskLogs']);
        $this->assertEquals("Old Task Subject", $json['taskLogs'][0]['subject']);
    }

    public function testCsvExportProductionMode()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '555';          // valid numeric project ID
        $_GET['force_prod'] = '1';    // force production mode
        $_GET['export'] = '1';        // trigger CSV export

        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');

        // Check for BOM
        $this->assertStringContainsString("\xEF\xBB\xBF", $output, "Missing BOM in CSV output");

        // Multiple substring checks for CSV header columns
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