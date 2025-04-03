<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';

class Auth0SessionPersistenceTest extends TestCase
{
    use Auth0SessionTrait;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testSessionContainsExpectedKeysAfterLogin()
    {
        // Simulate a login using your trait.
        $user = $this->fakeAuth0User([
            'sub'      => 'auth0|persist123',
            'email'    => 'persist@example.com',
            'nickname' => 'PersistUser',
            'role'     => 'user'
        ]);

        // Assert that the session has a 'user' key and that it contains the expected data.
        $this->assertArrayHasKey('user', $_SESSION, "Session should contain 'user' after login.");
        $this->assertEquals('auth0|persist123', $_SESSION['user']['sub']);
        $this->assertEquals('persist@example.com', $_SESSION['user']['email']);
        $this->assertEquals('PersistUser', $_SESSION['user']['nickname']);
        $this->assertEquals('user', $_SESSION['user']['role']);
        echo "testSessionContainsExpectedKeysAfterLogin passed\n";
    }
}
