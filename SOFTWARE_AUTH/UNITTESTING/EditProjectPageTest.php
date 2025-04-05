<?php
/*
-------------------------------------------------------------
File: EditProjectPageTest.php
Description:
- PHPUnit tests for edit-project-page.php in test (JSON) mode.
- Adds extra role-based & archiving tests.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class EditProjectPageTest extends BaseTestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    /**
     * Test: Invalid Project ID
     * Test that "Invalid project ID" JSON error returns when Project ID is invalid.
     */
    public function testInvalidProjectId()
    {
        // ?id= empty => "Invalid project ID"
        $_GET['id'] = '';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include __DIR__ . '/../edit-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output, true);
        $this->assertNotNull($json, "Output not valid JSON");
        $this->assertEquals("Invalid project ID", $json['error']);
    }

    /**
     * Test: Nonexistent Project
     * Test that "Project not found" JSON error returns when Project not found.
     */
    public function testNonexistentProjectShowsError()
    {
        // ?id=99999 => "Project not found"
        $_GET['id'] = '99999';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include __DIR__ . '/../edit-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("Project not found", $json['error']);
    }

    /**
     * Test: Unauthorized User
     * Test that when user is not authorized to see page expect JSON error.
     */
    public function testUnauthorizedUser()
    {
        $_SESSION['user'] = ['role' => 'user', 'nickname' => 'UserTest'];

        $_GET['id'] = '1'; // valid ID
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include __DIR__ . '/../edit-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("You are not authorized", $json['error']);
    }

    /**
     * Test: All Fields Required
     * Test that all fields are filled in before submitting form.
     */
    public function testEditProjectAllFieldsRequired()
    {
        // Staff user => manager or admin
        $_SESSION['user'] = ['role' => 'manager', 'nickname' => 'ManagerTest'];

        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_project' => true,
            'project_name'  => '',  // missing required name
            'status'        => 'Active',
            'priority'      => 'Low',
            'description'   => '',  // missing
            'due_date'      => '2025-12-31'
        ];

        ob_start();
        include __DIR__ . '/../edit-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("All fields are required", $json['error']);
    }

    /**
     * Test: Edit Project Success as Manager
     * Test that when editing as Manager, return JSON "Project updated successfully".
     */
    public function testEditProjectSuccessAsManager()
    {
        // manager => staff => success
        $_SESSION['user'] = ['role' => 'manager', 'nickname' => 'ManagerTest'];

        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_project' => true,
            'project_name'   => 'New Project Name',
            'status'         => 'On Hold',
            'priority'       => 'High',
            'description'    => 'New Desc',
            'due_date'       => '2025-08-01'
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-project-page.php');
        $json = json_decode($output, true);

        $this->assertNotNull($json);
        $this->assertEquals("Project updated successfully", $json['success']);
    }

    /**
     * Test: Edit Project Success as Admin
     * Test that when editing as Admin, return JSON "Project updated successfully".
     */
    public function testEditProjectSuccessAsAdmin()
    {
        // admin => staff => success
        $_SESSION['user'] = ['role' => 'admin', 'nickname' => 'AdminTest'];

        $_GET['id'] = '1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'update_project' => true,
            'project_name'   => 'Admin Project Name',
            'status'         => 'Closed',
            'priority'       => 'Medium',
            'description'    => 'Admin Desc',
            'due_date'       => '2026-01-01'
        ];

        $output = $this->captureOutput(__DIR__ . '/../edit-project-page.php');
        $json = json_decode($output, true);

        $this->assertNotNull($json);
        $this->assertEquals("Project updated successfully", $json['success']);
    }
}
