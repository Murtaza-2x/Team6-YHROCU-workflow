<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

/*
-------------------------------------------------------------
File: ProjectLogsTest.php
Description:
- Tests view-project-logs-page.php in test-mode JSON
  and does a CSV export check in production mode.
-------------------------------------------------------------
*/

class ProjectLogsTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // staff by default
        $_SESSION['user'] = [
            'role'=>'manager',
            'user_id'=>'auth0|manager123'
        ];
    }

    public function testUnauthorizedUser()
    {
        $_SESSION['user']['role'] = 'user'; // not staff
        $_GET['id'] = '1'; 
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("You are not authorized", $json['error']);
    }

    public function testInvalidProjectId()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = ''; // => "Invalid project ID"
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("Invalid project ID", $json['error']);
    }

    public function testNonexistentProject()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '99999'; // => "Project not found"
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("Project not found", $json['error']);
    }

    public function testNoLogsFound()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1'; // => "No logs found" if mock_logs not set
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("No logs found",$json['info']);
    }

    public function testMockLogsReturned()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1';
        $_GET['mock_logs'] = '1'; // => projectLogs + taskLogs arrays
        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);

        $this->assertArrayHasKey('projectLogs',$json);
        $this->assertCount(1,$json['projectLogs']);
        $this->assertEquals("Old Project Name",$json['projectLogs'][0]['project_name']);

        $this->assertArrayHasKey('taskLogs',$json);
        $this->assertCount(1,$json['taskLogs']);
        $this->assertEquals("Old Task Subject",$json['taskLogs'][0]['subject']);
    }

    public function testCsvExportProductionMode()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '555'; 
        $_GET['force_prod'] = '1'; // skip JSON
        $_GET['export'] = '1';     // do CSV

        $output = $this->captureOutput(__DIR__ . '/../view-project-logs-page.php');

        // BOM
        $this->assertStringContainsString("\xEF\xBB\xBF", $output, "Missing BOM in CSV output");

        // check for "Project Logs" header row, then "Task Logs"
        $this->assertStringContainsString("--- Project Logs ---", $output);
        $this->assertStringContainsString("--- Task Logs ---", $output);

        // Then we do multiple substring checks for the column headers:
        $this->assertStringContainsString("Edited By", $output);
        $this->assertStringContainsString("Created By", $output);
        $this->assertStringContainsString("Archived At", $output);
        $this->assertStringContainsString("Project Name", $output);

        // For tasks:
        $this->assertStringContainsString("Subject", $output);
        $this->assertStringContainsString("Status", $output);
        $this->assertStringContainsString("Priority", $output);
        $this->assertStringContainsString("Description", $output);

    }
}