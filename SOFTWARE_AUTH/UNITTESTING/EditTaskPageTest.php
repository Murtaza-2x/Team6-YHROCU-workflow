<?php
/*
-------------------------------------------------------------
File: EditTaskPageTest.php
Description:
- PHPUnit tests for edit-task-page.php in test mode (JSON).
- Tests:
    > Invalid task ID => {"error":"Invalid task ID"}
    > Nonexistent task => {"error":"Task not found"}
    > Missing fields => {"error":"All fields are required"}
    > Successful update => {"success":"Task updated successfully"}
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

class EditTaskPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    protected function setUp(): void
    {
        // Optionally, we can do $this->fakeAuth0User() if "is_staff()" is required
        parent::setUp();
    }

    public function testInvalidTaskIdShowsError()
    {
        // ?id= (empty) => "Invalid task ID"
        $_GET['id'] = ''; 
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include __DIR__ . '/../edit-task-page.php';
        $output = ob_get_clean();

        $json = json_decode($output,true);
        $this->assertNotNull($json, "Output not valid JSON.");
        $this->assertEquals("Invalid task ID", $json['error']);
    }

    public function testNonexistentTaskShowsError()
    {
        // ?id=99999 => "Task not found"
        $_GET['id'] = '99999'; 
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include __DIR__ . '/../edit-task-page.php';
        $output = ob_get_clean();

        $json = json_decode($output,true);
        $this->assertNotNull($json);
        $this->assertEquals("Task not found", $json['error']);
    }

    public function testEditTaskAllFieldsRequired()
    {
        // ?id=1 => valid ID, but missing fields => "All fields are required"
        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_task' => true,
            // subject => missing
            // project_id => missing, etc...
            'status'=>'In Progress',
            'priority'=>'Medium'
        ];

        ob_start();
        include __DIR__ . '/../edit-task-page.php';
        $output = ob_get_clean();

        $json = json_decode($output,true);
        $this->assertNotNull($json);
        $this->assertEquals("All fields are required", $json['error']);
    }

    public function testEditTaskSuccess()
    {
        // ?id=1 => we pass all fields => "Task updated successfully"
        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_task'=> true,
            'subject'    => 'Updated Subject',
            'project_id' => '99',
            'status'     => 'Complete',
            'priority'   => 'Low',
            'description'=> 'New desc'
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-task-page.php');
        $json = json_decode($output,true);

        $this->assertNotNull($json);
        $this->assertEquals("Task updated successfully",$json['success']);
    }
}
