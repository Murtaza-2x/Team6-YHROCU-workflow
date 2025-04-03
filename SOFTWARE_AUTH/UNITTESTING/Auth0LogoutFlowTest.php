<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';

class Auth0LogoutFlowTest extends TestCase
{
    use Auth0SessionTrait;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testLogoutClearsSession()
    {
        try {
            // Set a dummy user in the session.
            $_SESSION['user'] = ['sub' => 'auth0|dummy', 'email' => 'dummy@example.com'];
            
            // Start output buffering to capture output.
            ob_start();
            include __DIR__ . '/../auth0_logout.php';
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