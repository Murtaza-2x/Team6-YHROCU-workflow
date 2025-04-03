<?php
// Import the PHPUnit TestCase class.
use PHPUnit\Framework\TestCase;

// Include our traits for simulating Auth0 sessions and capturing output.
// These traits provide methods to set up or clear a fake user session,
// and to capture the output of an included PHP file into a variable.
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

class AdminUserTest extends TestCase
{
    // Use the Auth0SessionTrait for simulating user sessions,
    // and BufferedPageTestTrait for capturing output.
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    /**
     * Test that a guest (i.e. when no user is in session) is denied access.
     * In test mode, the admin page should output "Access Denied" instead of performing a redirect.
     */
    public function testAccessDeniedForGuest()
    {
        try {
            // Clear the session so that no user is logged in.
            $this->clearAuth0Session();
            
            // Capture the output of the admin page.
            $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
            
            // Assert that the captured output contains "Access Denied".
            $this->assertStringContainsString("Access Denied", $output, "Guest should be denied access.");
            
            // Echo a message indicating this test passed.
            echo "testAccessDeniedForGuest passed\n";
        } catch (\Throwable $e) {
            // If an exception occurs, echo the failure message and rethrow the exception.
            echo "testAccessDeniedForGuest failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Test that a non-admin user is denied access.
     * The admin page should output "Access Denied" if a user with a role other than "admin" is logged in.
     */
    public function testAccessDeniedForNonAdmin()
    {
        try {
            // Simulate a non-admin user by setting role to 'user' and a sample nickname.
            $this->fakeAuth0User(['role' => 'user', 'nickname' => 'RegularUser']);
            
            // Capture the output of the admin page.
            $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
            
            // Assert that the output contains "Access Denied".
            $this->assertStringContainsString("Access Denied", $output, "Non-admin should be denied access.");
            
            // Echo a message indicating this test passed.
            echo "testAccessDeniedForNonAdmin passed\n";
        } catch (\Throwable $e) {
            // Echo the error message if the test fails and rethrow the exception.
            echo "testAccessDeniedForNonAdmin failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Test that an admin user is granted access.
     * When a user with the "admin" role is logged in, the admin page should output a welcome message.
     */
    public function testAdminGetsAccess()
    {
        try {
            // Simulate an admin user with role "admin" and a custom nickname.
            $this->fakeAuth0User(['role' => 'admin', 'nickname' => 'CaptainAdmin']);
            
            // Capture the output of the admin page.
            $output = $this->captureOutput(__DIR__ . '/../admin-page.php');
            
            // Assert that the output contains the expected welcome message.
            $this->assertStringContainsString("Welcome Admin CaptainAdmin", $output, "Admin should see a welcome message.");
            
            // Echo a message indicating this test passed.
            echo "testAdminGetsAccess passed\n";
        } catch (\Throwable $e) {
            // If the test fails, echo the error message and rethrow the exception.
            echo "testAdminGetsAccess failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}