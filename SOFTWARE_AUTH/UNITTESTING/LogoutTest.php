<?php
use PHPUnit\Framework\TestCase;

class Auth0LogoutTest extends TestCase
{
    public function testLogoutRedirect()
    {
        // Simulate a session
        $_SESSION['user'] = ['sub' => 'auth0|123456', 'email' => 'testuser@example.com'];

        // Capture the output of the logout script
        ob_start();
        require_once __DIR__ . '/path_to_your_project/auth0_logout.php';  // Adjust path accordingly
        $output = ob_get_clean();

        // Assert that the session is destroyed
        $this->assertEmpty($_SESSION, "Session should be destroyed after logout");

        // Assert that the redirect URL is correct
        $this->assertStringContainsString('https://', $output, "Logout should redirect to Auth0 logout URL");
    }
}
