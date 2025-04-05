<?php
/*
-------------------------------------------------------------
File: AdminUserTest.php
Description:
- Tests admin-page.php access control and behavior.
- Simulates Auth0 sessions for guest, regular user, and admin roles.
- Includes a mocked test to simulate user creation using Auth0UserManager.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/../INCLUDES/Auth0UserManager.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

class AdminUserTest extends BaseTestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    /**
     * Test: Access Denied for Guest
     * Verifies that a guest (with no session) is denied access.
     * Expected output should include "Access Denied".
     */
    public function testAccessDeniedForGuest(): void
    {
        $this->clearAuth0Session();
        $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
        $this->assertStringContainsString("Access Denied", $output);
    }

    /**
     * Test: Access Denied for Non-Admin User
     * Verifies that a user with role "user" cannot access the admin page.
     */
    public function testAccessDeniedForNonAdmin(): void
    {
        // Simulate login as a regular user by setting 'role' explicitly.
        $this->fakeAuth0User([
            'nickname' => 'RegularUser',
            'role' => 'user',
            'app_metadata' => ['role' => 'user']
        ]);
        $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
        $this->assertStringContainsString("Access Denied", $output);
    }

    /**
     * Test: Admin Gets Access
     * Verifies that an admin sees the welcome message when accessing the admin page.
     */
    public function testAdminGetsAccess(): void
    {
        // Simulate login as an admin by explicitly setting 'role' to 'admin'.
        $this->fakeAuth0User([
            'nickname' => 'CaptainAdmin',
            'role' => 'admin',
            'app_metadata' => ['role' => 'admin']
        ]);
        $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
        $this->assertStringContainsString("Welcome Admin CaptainAdmin", $output);
    }

    /**
     * Test: Admin Can Create User With Mocked Manager
     * Mocks Auth0UserManager to simulate successful user creation.
     * Injects the mock into the global space and verifies that a success message appears.
     */
    public function testAdminCanCreateUserWithMockedManager(): void
    {
        // Create a mock Auth0UserManager with expectations.
        $mock = $this->createMock(Auth0UserManager::class);
        $mock->expects($this->once())
            ->method('createUser')
            ->with(
                $this->equalTo('newuser@example.com'),
                $this->equalTo('TestPass123!'),
                $this->equalTo('User')
            );
        $mock->method('getUsers')->willReturn([]); // Simulate empty user list.

        // Inject the mock into the global scope.
        $GLOBALS['Auth0UserManager'] = $mock;

        // Simulate admin login with 'role' explicitly set.
        $this->fakeAuth0User([
            'nickname' => 'CaptainAdmin',
            'role' => 'admin',
            'app_metadata' => ['role' => 'admin']
        ]);

        // Simulate POST submission for user creation.
        $_POST['create_user']   = true;
        $_POST['new_email']     = 'newuser@example.com';
        $_POST['new_password']  = 'TestPass123!';
        $_POST['new_role']      = 'User';

        // Capture the admin page output.
        $output = $this->captureOutput(__DIR__ . '/../admin-page.php');

        // Assert that the success message appears.
        $this->assertStringContainsString('User created successfully', $output);

        // Clean up the global mock.
        unset($GLOBALS['Auth0UserManager']);
    }

    /**
     * Test: Access Denied for Manager
     * Verifies that a user with role "manager" is denied admin access.
     */
    public function testAccessDeniedForManager(): void
    {
        // Simulate login as a manager by explicitly setting 'role' to 'manager'.
        $this->fakeAuth0User([
            'nickname' => 'ManagerGuy',
            'role' => 'manager',
            'app_metadata' => ['role' => 'manager']
        ]);
        $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
        $this->assertStringContainsString("Access Denied", $output);
    }

    /**
     * Test: Access Denied for Unknown Role
     * Verifies that a user with an unknown or invalid role is denied access.
     */
    public function testAccessDeniedForUnknownRole(): void
    {
        // Simulate login with an unrecognized role.
        $this->fakeAuth0User([
            'nickname' => 'WeirdUser',
            'role' => 'hacker',
            'app_metadata' => ['role' => 'hacker']
        ]);
        $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
        $this->assertStringContainsString("Access Denied", $output);
    }

    /**
     * Test: Access Denied When Role Is Missing
     * Verifies that a user with no role set is denied access.
     */
    public function testAccessDeniedWhenRoleIsMissing(): void
    {
        // Simulate login with no role provided.
        $this->fakeAuth0User([
            'nickname' => 'NoRoleGuy',
            // No 'role' key is set.
            'app_metadata' => []
        ]);
        $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
        $this->assertStringContainsString("Access Denied", $output);
    }
}