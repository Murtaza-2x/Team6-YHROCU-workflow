<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

/*
-------------------------------------------------------------
File: EditTaskPageTest.php
Description:
- Tests edit-task-page.php in test mode (JSON).
- Covers role-based checks, invalid ID, missing fields, assigned user emails, etc.
-------------------------------------------------------------
*/

class EditTaskPageTest extends BaseTestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Start session & set a default user as manager (so we pass staff checks)
        $_SESSION['user'] = [
            'role'     => 'manager',
            'nickname' => 'ManagerTest'
        ];
    }

    /**
     * Test: Invalid Task ID
     * Test that "Invalid task ID" JSON error returns when Task ID is invalid.
     */
    public function testInvalidTaskId()
    {
        $_GET['id'] = '';  // => "Invalid task ID"
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $output = $this->captureOutput(__DIR__ . '/test_files/edit-task-page.php');
        $json   = json_decode($output, true);

        $this->assertNotNull($json);
        $this->assertEquals("Invalid task ID", $json['error']);
    }

    /**
     * Test: Nonexistent Task
     * Test that "Task not found" JSON error returns when Task not found.
     */
    public function testNonexistentTask()
    {
        $_GET['id'] = '99999'; // => "Task not found"
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $output = $this->captureOutput(__DIR__ . '/test_files/edit-task-page.php');
        $json   = json_decode($output, true);

        $this->assertNotNull($json);
        $this->assertEquals("Task not found", $json['error']);
    }

    /**
     * Test: Unauthorized User
     * Test that when user is not authorized to see page expect JSON error.
     */
    public function testUnauthorizedUser()
    {
        // role=user => not staff => "You are not authorized"
        $_SESSION['user']['role'] = 'user';
        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $output = $this->captureOutput(__DIR__ . '/test_files/edit-task-page.php');
        $json   = json_decode($output, true);

        $this->assertNotNull($json);
        $this->assertEquals("You are not authorized", $json['error']);
    }

    /**
     * Test: All Fields Required
     * Test that all fields are filled in before submitting form.
     */
    public function testAllFieldsRequired()
    {
        // manager => staff => but missing fields => "All fields are required"
        $_SESSION['user']['role'] = 'manager';

        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_task' => true,
            'subject'     => '',
            'project_id'  => '10',
            'status'      => 'In Progress',
            'priority'    => 'High',
            'description' => '' // missing
        ];

        $output = $this->captureOutput(__DIR__ . '/test_files/edit-task-page.php');
        $json   = json_decode($output, true);

        $this->assertNotNull($json);
        $this->assertEquals("All fields are required", $json['error']);
    }

    /**
     * Test: Edit Task Success NO Assign
     * Test that when no Assigned user email is not sent.
     */
    public function testEditTaskSuccessNoAssign()
    {
        // manager => staff => success => no assigned => emailsSent=[]
        $_SESSION['user']['role'] = 'manager';

        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_task' => true,
            'subject'    => 'Test Subject',
            'project_id' => '42',
            'status'     => 'Complete',
            'priority'   => 'Low',
            'description' => 'Some desc'
            // no "assign"
        ];

        $output = $this->captureOutput(__DIR__ . '/test_files/edit-task-page.php');
        $json   = json_decode($output, true);

        $this->assertNotNull($json);
        $this->assertEquals("Task updated successfully", $json['success']);
        $this->assertArrayHasKey("emailsSent", $json);
        $this->assertCount(0, $json['emailsSent']);
    }

    /**
     * Test: Edit Task Success with Assign
     * Test that when user is Assigned email is sent.
     */
    public function testEditTaskSuccessWithAssign()
    {
        // manager => staff => success => assigned => emails
        $_SESSION['user']['role'] = 'manager';

        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_task' => true,
            'subject'    => 'EmailTest Subject',
            'project_id' => '10',
            'status'     => 'In Progress',
            'priority'   => 'Medium',
            'description' => 'Check email logic',
            'assign'     => ['auth0|abc123', 'auth0|xyz789']
        ];

        $output = $this->captureOutput(__DIR__ . '/test_files/edit-task-page.php');
        $json   = json_decode($output, true);

        $this->assertNotNull($json);
        $this->assertEquals("Task updated successfully", $json['success']);
        $this->assertArrayHasKey("emailsSent", $json);
        $this->assertCount(2, $json['emailsSent']);
        // check the user IDs show up
        $this->assertStringContainsString("auth0|abc123", $json['emailsSent'][0]);
        $this->assertStringContainsString("auth0|xyz789", $json['emailsSent'][1]);
    }
}
