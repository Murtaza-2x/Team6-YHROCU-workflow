<?php
/*
-------------------------------------------------------------
File: Auth0SessionPersistenceTest.php
Description:
- Test that after simulating a login, the session contains the expected keys and values.
- Simulates a login by faking an Auth0 user with specific data.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';

class Auth0SessionPersistenceTest extends BaseTestCase
{
    use Auth0SessionTrait;

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