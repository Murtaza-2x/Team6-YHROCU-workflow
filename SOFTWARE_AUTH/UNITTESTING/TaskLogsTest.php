<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

/*
-------------------------------------------------------------
File: TaskLogsTest.php
Description:
- Tests view-task-logs-page.php:
   * JSON-based checks in test mode (invalid ID, nonexistent, unauthorized, etc.)
   * CSV export in production mode with multiple substring checks
-------------------------------------------------------------
*/

class TaskLogsTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // By default, staff user
        $_SESSION['user'] = [
            'role'=>'manager',
            'user_id'=>'auth0|managerUser'
        ];
    }

    // Unauthorized user => "You are not authorized"
    public function testUnauthorizedUser()
    {
        $_SESSION['user']['role'] = 'user'; // not staff
        $_GET['id'] = '1';
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("You are not authorized", $json['error']);
    }

    // Invalid ID => "Invalid task ID"
    public function testInvalidId()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = ''; // => "Invalid task ID"
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("Invalid task ID",$json['error']);
    }

    // Nonexistent => "Task not found"
    public function testNonexistentTask()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '99999'; // => "Task not found"
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("Task not found",$json['error']);
    }

    // No logs => "No logs found"
    public function testNoLogsFound()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1'; // no logs => "No logs found"
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("No logs found",$json['info']);
    }

    // Some logs => "logs" => mock data
    public function testMockLogsReturned()
    {
        $_SESSION['user']['role'] = 'admin';
        $_GET['id'] = '1';
        $_GET['mock_logs'] = '1'; // => logs array
        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');
        $json   = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertArrayHasKey("logs",$json);
        $this->assertCount(1,$json['logs']);
        $this->assertEquals("Old Subject",$json['logs'][0]['subject']);
    }

    public function testCsvExportProductionMode()
    {
        $_SESSION['user']['role'] = 'admin'; 
        $_GET['id'] = '123';          // numeric => valid
        $_GET['force_prod'] = '1';    // skip JSON block
        $_GET['export'] = '1';        // do CSV export

        $output = $this->captureOutput(__DIR__ . '/../view-task-logs-page.php');

        // We expect a BOM
        $this->assertStringContainsString("\xEF\xBB\xBF", $output, "Missing BOM in CSV output");

        // Instead of matching entire header exactly, we do multiple substring checks:
        $this->assertStringContainsString("Edited By", $output);
        $this->assertStringContainsString("Archived At", $output);
        $this->assertStringContainsString("Created At", $output);
        $this->assertStringContainsString("Subject", $output);
        $this->assertStringContainsString("Status", $output);
        $this->assertStringContainsString("Priority", $output);
        $this->assertStringContainsString("Description", $output);
    }
}