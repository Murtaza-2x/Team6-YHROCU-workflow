<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';

class Auth0CallbackTest extends TestCase
{
    use Auth0SessionTrait;

    protected function setUp(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testCallbackFlowSetsSessionData()
    {
        // Set dummy GET parameters (they won't be used in test mode).
        $_GET['code']  = 'dummy-code';
        $_GET['state'] = 'dummy-state';

        // Include the callback file. In test mode, it should bypass exchange().
        include __DIR__ . '/../auth0_callback.php';

        // Assert that the session now has a 'user' key with expected values.
        $this->assertArrayHasKey('user', $_SESSION, "Callback should set user data in session.");
        $this->assertArrayHasKey('sub', $_SESSION['user'], "User data should include 'sub'.");
        $this->assertArrayHasKey('email', $_SESSION['user'], "User data should include 'email'.");
        echo "testCallbackFlowSetsSessionData passed\n";
    }
}