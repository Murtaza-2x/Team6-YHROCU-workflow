<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';

class Auth0SessionPersistenceTest extends TestCase
{
    use Auth0SessionTrait;

    /**
     * setUp() is executed before each test method.
     * Here, we ensure that a session is started and cleared.
     */
    protected function setUp(): void
    {
        // Start the session only if one is not already active.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Clear the session to ensure a clean state for each test.
        $_SESSION = [];
    }

    /**
     * Test that after simulating a login, the session contains the expected keys and values.
     */
    public function testSessionContainsExpectedKeysAfterLogin()
    {
        // Simulate a login by faking an Auth0 user with specific data.
        $user = $this->fakeAuth0User([
            'sub'      => 'auth0|persist123',     // Unique user identifier from Auth0.
            'email'    => 'persist@example.com',  // User's email address.
            'nickname' => 'PersistUser',          // User's display nickname.
            'role'     => 'user'                  // User's role.
        ]);

        // Assert that the session now has a 'user' key.
        $this->assertArrayHasKey('user', $_SESSION, "Session should contain 'user' after login.");

        // Assert that the session data under the 'user' key matches what was simulated.
        $this->assertEquals('auth0|persist123', $_SESSION['user']['sub'], "The 'sub' value does not match.");
        $this->assertEquals('persist@example.com', $_SESSION['user']['email'], "The 'email' value does not match.");
        $this->assertEquals('PersistUser', $_SESSION['user']['nickname'], "The 'nickname' value does not match.");
        $this->assertEquals('user', $_SESSION['user']['role'], "The 'role' value does not match.");

        echo "testSessionContainsExpectedKeysAfterLogin passed\n";
    }
}