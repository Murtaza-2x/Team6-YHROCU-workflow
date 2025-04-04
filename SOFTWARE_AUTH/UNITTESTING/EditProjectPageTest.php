<?php
/*
-------------------------------------------------------------
File: Auth0EditProjectPageTest.php
Description:
- PHPUnit tests for edit-project-page.php in test mode (JSON).
- Tests:
   * Invalid project ID => {"error":"Invalid project ID"}
   * Nonexistent project => {"error":"Project not found"}
   * Missing fields => {"error":"All fields are required"}
   * Successful update => {"success":"Project updated successfully"}
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

class EditProjectPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Possibly login as admin if is_staff() is relevant
        // $this->fakeAuth0User(['role'=>'Admin']);
    }

    public function testInvalidProjectIdShowsError()
    {
        $_GET['id'] = ''; // empty => invalid
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include __DIR__ . '/../edit-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output,true);
        $this->assertNotNull($json, "Output not valid JSON");
        $this->assertEquals("Invalid project ID",$json['error']);
    }

    public function testNonexistentProjectShowsError()
    {
        $_GET['id'] = '99999'; // => "Project not found"
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include __DIR__ . '/../edit-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output,true);
        $this->assertNotNull($json);
        $this->assertEquals("Project not found",$json['error']);
    }

    public function testEditProjectAllFieldsRequired()
    {
        $_GET['id'] = '1'; // valid ID for test mode
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_project'=> true,
            // missing some fields => triggers "All fields are required"
            'project_name'=>'', // empty
            'status'=>'Active'
            // priority => missing
            // description => missing
            // due_date => missing
        ];

        ob_start();
        include __DIR__ . '/../edit-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output,true);
        $this->assertNotNull($json);
        $this->assertEquals("All fields are required",$json['error']);
    }

    public function testEditProjectSuccess()
    {
        $_GET['id'] = '1'; // valid ID => success path
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_project'=> true,
            'project_name'  => 'New Project Name',
            'status'        => 'Active',
            'priority'      => 'Low',
            'description'   => 'Updated Desc',
            'due_date'      => '2025-12-31'
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-project-page.php');
        $json = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("Project updated successfully",$json['success']);
    }
}