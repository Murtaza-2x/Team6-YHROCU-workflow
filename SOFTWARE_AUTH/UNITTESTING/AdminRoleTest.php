<?php
use PHPUnit\Framework\TestCase;

// Include the traits for simulating user sessions, buffering output, database connection, and role policies.
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';
require_once __DIR__ . '/traits/RolePolicyTrait.php';
require_once __DIR__ . '/traits/RoleTrait.php';

class AdminRoleTest extends TestCase
{
    // Use the provided traits to simulate sessions, capture output, manage DB connection, and check permissions.
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;
    use RolePolicyTrait;
    use RoleTrait;

    /**
     * setUp() is called before each test.
     * Here we establish a fresh database connection.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();  // Create a new DB connection.
    }

    /**
     * tearDown() is called after each test.
     * We close the database connection to prevent leaks.
     */
    protected function tearDown(): void
    {
        $this->tearDownDatabase();
    }

    /**
     * Test that an admin user has all the expected permissions.
     */
    public function testAdminHasAllPermissions()
    {
        try {
            // Log in as admin with a custom nickname.
            $this->loginAsAdmin(['nickname' => 'SuperAdmin']);
            
            // Assert that the admin user has each of the required permissions.
            $this->assertTrue($this->can('view_dashboard'), "Admin should be able to view the dashboard.");
            $this->assertTrue($this->can('edit_tasks'), "Admin should be able to edit tasks.");
            $this->assertTrue($this->can('manage_users'), "Admin should be able to manage users.");
            $this->assertTrue($this->can('view_logs'), "Admin should be able to view logs.");
            
            // Echo a message if this test passes.
            echo "testAdminHasAllPermissions passed\n";
        } catch (\Throwable $e) {
            // Echo the error message if the test fails and rethrow the exception.
            echo "testAdminHasAllPermissions failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Test that a regular user has only limited permissions.
     */
    public function testUserHasLimitedPermissions()
    {
        try {
            // Log in as a regular user.
            $this->loginAsUser(['nickname' => 'RegularUser']);
            
            // Assert that a regular user can view the dashboard, but cannot edit tasks or manage users.
            $this->assertTrue($this->can('view_dashboard'), "User should be able to view the dashboard.");
            $this->assertFalse($this->can('edit_tasks'), "Regular user should not be able to edit tasks.");
            $this->assertFalse($this->can('manage_users'), "Regular user should not be able to manage users.");
            
            // Echo a success message.
            echo "testUserHasLimitedPermissions passed\n";
        } catch (\Throwable $e) {
            // Echo error message and rethrow exception on failure.
            echo "testUserHasLimitedPermissions failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Test that a manager user can edit tasks but cannot manage users.
     */
    public function testManagerCanEditTasks()
    {
        try {
            // Log in as a manager.
            $this->loginAsManager(['nickname' => 'ManagerUser']);
            
            // Assert that a moderator has permission to edit tasks but not to manage users.
            $this->assertTrue($this->can('edit_tasks'), "Manager should be able to edit tasks.");
            $this->assertFalse($this->can('manage_users'), "Manager should not be able to manage users.");
            
            // Echo a success message.
            echo "testManagerCanEditTasks passed\n";
        } catch (\Throwable $e) {
            // Echo error message and rethrow exception.
            echo "testManagerCanEditTasks failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}