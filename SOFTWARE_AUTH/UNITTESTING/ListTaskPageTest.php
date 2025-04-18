<?php
/*
-------------------------------------------------------------
File: ListTaskPageTest.php
Description:
- PHPUnit tests for list-task-page.php (Dashboard)
- Tests:
    * Redirects unauthorized users to login
    * Displays dashboard for admin
    * Displays limited task list for normal user
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class ListTaskPageTest extends BaseTestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
        $_GET = [];

        // Insert dummy project + task
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $this->conn->query("DELETE FROM tasks WHERE id = 1");

        $GLOBALS['conn'] = $this->conn;

        $this->insertDummy(
            "INSERT INTO projects (id, project_name) VALUES (?, ?)",
            [1, "Dashboard Project"],
            "is"
        );

        $this->insertDummy(
            "INSERT INTO tasks (id, subject, project_id, status, priority, created_by) VALUES (?, ?, ?, ?, ?, ?)",
            [1, "Dashboard Task", 1, "New", "Urgent", "auth0|admin1"],
            "isisss"
        );

        $this->insertDummy(
            "INSERT INTO task_assigned_users (task_id, user_id) VALUES (?, ?)",
            [1, "auth0|user1"],
            "is"
        );
    }

    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM task_assigned_users WHERE task_id = 1");
        $this->conn->query("DELETE FROM tasks WHERE id = 1");
        $this->conn->query("DELETE FROM projects WHERE id = 1");
        $this->tearDownDatabase();
    }

    /**
     * Test: Guest user should be redirected to login page
     */
    public function testGuestUserRedirectedToLogin()
    {
        unset($_SESSION['user']); // simulate guest
        $output = $this->captureOutput(__DIR__ . '/test_files/list-task-page.php');

        $this->assertStringContainsString("Please log in first", $output);
    }

    /**
     * Test: Admin sees full task list
     */
    public function testAdminCanViewDashboard()
    {
        $_SESSION['user'] = [
            'user_id' => 'auth0|admin1',
            'role' => 'Admin',
            'nickname' => 'AdminTester'
        ];

        $output = $this->captureOutput(__DIR__ . '/test_files/list-task-page.php');
        $this->assertStringContainsString("Dashboard", $output);
        $this->assertStringContainsString("Dashboard Task", $output);
        $this->assertStringContainsString("Create Task", $output); // Button visible only for admin
    }

    /**
     * Test: Normal user sees assigned tasks only (no create buttons)
     */
    public function testUserSeesAssignedTasksOnly()
    {
        $_SESSION['user'] = [
            'user_id' => 'auth0|user1',
            'role' => 'User',
            'nickname' => 'UserTester'
        ];
        $GLOBALS['conn'] = $this->conn;

        $output = $this->captureOutput(__DIR__ . '/test_files/list-task-page.php');
        $this->assertStringContainsString("Dashboard", $output);
        $this->assertStringContainsString("Dashboard Task", $output);
        $this->assertStringNotContainsString("Create Task", $output);
    }

    /**
     * Test: Admin sees other users' tasks
     */
    public function testAdminSeesAllTasks()
    {
        $_SESSION['user'] = ['user_id' => 'auth0|admin1', 'role' => 'Admin'];

        // Always delete before inserting to avoid duplicate key
        $this->conn->query("DELETE FROM tasks WHERE id = 2");

        $this->insertDummy(
            "INSERT INTO tasks (id, subject, project_id, status, priority, created_by) VALUES (?, ?, ?, ?, ?, ?)",
            [2, "Unassigned Task", 1, "New", "Moderate", "auth0|admin2"],
            "isisss"
        );

        try {
            $output = $this->captureOutput(__DIR__ . '/test_files/list-task-page.php');
            $this->assertStringContainsString("Dashboard Task", $output);
            $this->assertStringContainsString("Unassigned Task", $output);
        } finally {
            $this->conn->query("DELETE FROM tasks WHERE id = 2");
        }
    }

    /**
     * Test: User does not see unassigned tasks
     */
    public function testUserCannotSeeUnassignedTasks()
    {
        $_SESSION['user'] = ['user_id' => 'auth0|user1', 'role' => 'User'];

        // Delete before insert to prevent duplicate ID
        $this->conn->query("DELETE FROM tasks WHERE id = 3");

        $this->insertDummy(
            "INSERT INTO tasks (id, subject, project_id, status, priority, created_by) VALUES (?, ?, ?, ?, ?, ?)",
            [3, "Unrelated Task", 1, "New", "Moderate", "auth0|admin2"],
            "isisss"
        );

        try {
            $output = $this->captureOutput(__DIR__ . '/test_files/list-task-page.php');
            $this->assertStringContainsString("Dashboard Task", $output); // visible
            $this->assertStringNotContainsString("Unrelated Task", $output); // not visible
        } finally {
            $this->conn->query("DELETE FROM tasks WHERE id = 3");
        }
    }
    public function testUserCanViewDashboard()
    {
        $_SESSION['user'] = [
            'user_id' => 'auth0|user1',
            'role' => 'User',
            'nickname' => 'UserTester'
        ];
        $GLOBALS['conn'] = $this->conn;
    
        $output = $this->captureOutput(__DIR__ . '/test_files/list-task-page.php');
    
        $this->assertStringContainsString("Dashboard Task", $output);
        $this->assertStringNotContainsString("Create Task", $output);
    }
    
}
