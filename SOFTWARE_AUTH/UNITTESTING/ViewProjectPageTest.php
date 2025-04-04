<?php
/*
-------------------------------------------------------------
File: Auth0ViewProjectPageTest.php
Description:
- PHPUnit tests for view-project-page.php, in test mode
- Tests that:
    > An invalid project ID returns {"error":"Invalid project ID"}
    > A nonexistent project ID returns {"error":"Project not found"}
    > A valid project ID returns the project details
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

class ViewProjectPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    protected function setUp(): void
    {
        // We can start or clear the session, if needed
        parent::setUp();
        // e.g. $this->fakeAuth0User(); or not, if user check is not tested
    }

    public function testInvalidProjectIdShowsError()
    {
        // Mimic ?id= empty => "Invalid project ID"
        $_GET['id'] = ''; 

        ob_start();
        include __DIR__ . '/../view-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output,true);
        $this->assertNotNull($json, "Output not valid JSON.");
        $this->assertEquals("Invalid project ID", $json['error']);
    }

    public function testNonexistentProjectShowsError()
    {
        // ?id=99999 => "Project not found"
        $_GET['id'] = '99999';

        ob_start();
        include __DIR__ . '/../view-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output,true);
        $this->assertNotNull($json);
        $this->assertEquals("Project not found", $json['error']);
    }

    public function testViewProjectPageDisplaysProject()
    {
        // a valid ID => "1" => returns mock JSON
        $_GET['id'] = '1';

        $output = $this->captureOutput(__DIR__ . '/../view-project-page.php');
        $json = json_decode($output,true);

        $this->assertNotNull($json);
        // We expect "projectId" => "1"
        $this->assertEquals("1", $json['projectId']);
        // We expect "project" => { "project_name"=>"Test Project Name", ... }
        $this->assertStringContainsString("Test Project Name",$json['project']['project_name']);
        $this->assertStringContainsString("Test project description",$json['project']['description']);
    }
}
