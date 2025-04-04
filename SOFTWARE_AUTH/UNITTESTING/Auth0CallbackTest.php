<?php
/*
-------------------------------------------------------------
File: Auth0CallbackTest.php
Description:
- Tests the Auth0 callback flow.
- Uses a fake Auth0 instance (FakeAuth0) to simulate the Auth0 SDK behavior,
  since Auth0 is declared final and cannot be directly mocked.
- Mocks Auth0UserManager to simulate fetching user data.
- Verifies that after the callback, the session contains the expected user data.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../INCLUDES/Auth0UserManager.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';

// -------------------------------------------------------------
// FakeAuth0 Class
// Description:
// - A fake replacement for the Auth0 class since Auth0 is final and cannot be mocked.
// - Implements the minimal methods required by the callback: exchange() and getUser().
// -------------------------------------------------------------
class FakeAuth0 {
    // Simulate token exchange; does nothing.
    public function exchange() {}

    // Simulate retrieving the user from Auth0; returns a fake user with a 'sub' identifier.
    public function getUser() {
        return ['sub' => 'auth0|testuser123'];
    }
}

// -------------------------------------------------------------
// Auth0CallbackTest Class
// Description:
// - Tests the Auth0 callback functionality.
// - Uses the Auth0SessionTrait to simulate session management.
// -------------------------------------------------------------
class Auth0CallbackTest extends TestCase
{
    use Auth0SessionTrait;

    /**
     * setUp() is executed before each test method.
     * Here:
     * - Ensure a session is started and cleared.
     * - Set up GET parameters and session data to simulate an Auth0 callback.
     * - Inject our FakeAuth0 instance and a mocked Auth0UserManager into the global scope.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Start session if not already started.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear the session.
        $_SESSION = [];

        // Simulate callback parameters.
        $_GET['code'] = 'testcode';
        $_GET['state'] = 'teststate';
        $_SESSION['auth0_session']['state'] = 'teststate';

        // Inject our fake Auth0 instance into the global scope.
        $GLOBALS['auth0'] = new FakeAuth0();

        // Create a mock for Auth0UserManager and define its behavior.
        $mockUserManager = $this->createMock(\Auth0UserManager::class);
        // When getUser() is called on the manager, return a simulated user array.
        $mockUserManager->method('getUser')->willReturn([
            'user_id' => 'auth0|testuser123',
            'nickname' => 'TestUser',
            'email' => 'test@example.com',
            'app_metadata' => [
                'role' => 'admin',
                'status' => 'active',
            ]
        ]);
        // Inject the mocked user manager into the global scope.
        $GLOBALS['Auth0UserManager'] = $mockUserManager;
    }

    /**
     * testCallbackFlowSetsSessionData()
     * Purpose:
     * - Includes the auth0_callback.php file, which should process the callback.
     * - Verifies that after processing, the session contains the 'user' key with expected values.
     */
    public function testCallbackFlowSetsSessionData()
    {
        // Start output buffering to capture output from the callback.
        ob_start();
        try {
            // Include the callback file; it should set the session data.
            include __DIR__ . '/../auth0_callback.php';
        } finally {
            // Clean (discard) the output buffer.
            ob_end_clean();
        }

        // Assert that the session contains a 'user' key.
        $this->assertArrayHasKey('user', $_SESSION);
        // Assert that the user's role is 'Admin' (as normalized in the callback).
        $this->assertEquals('Admin', $_SESSION['user']['role']);
        // Assert that the user's nickname is 'TestUser'.
        $this->assertEquals('TestUser', $_SESSION['user']['nickname']);
    }
}