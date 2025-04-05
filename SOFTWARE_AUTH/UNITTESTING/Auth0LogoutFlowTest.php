<?php
/*
-------------------------------------------------------------
File: Auth0LogoutFlowTest.php
Description:
- Sets a dummy user in the session.
- Tests that logout clears session.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';

class Auth0LogoutFlowTest extends BaseTestCase
{
    use Auth0SessionTrait;

    public function testLogoutClearsSession()
    {
        try {
            // Set a dummy user in the session.
            $_SESSION['user'] = ['sub' => 'auth0|dummy', 'email' => 'dummy@example.com'];
            
            // Start output buffering to capture output.
            ob_start();
            include __DIR__ . '/test_files/auth0_logout.php';
            $output = ob_get_clean();
            
            // Debug: output captured content (for local debugging only)
            // echo "Captured output: $output\n";
            
            // Assert that the session is cleared.
            $this->assertArrayNotHasKey('user', $_SESSION, "Session should be cleared after logout.");
            
            // Assert that the captured output contains our test marker.
            $this->assertStringContainsString("Logout simulated: session cleared", $output);
            
            echo "testLogoutClearsSession passed\n";
        } catch (\Throwable $e) {
            echo "testLogoutClearsSession failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}