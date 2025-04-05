<?php
/*
-------------------------------------------------------------
Trait: Auth0SessionTrait
Purpose: Simulate Auth0 user sessions with role support for tests
-------------------------------------------------------------
*/

trait Auth0SessionTrait
{
    /**
     * Setup before each test.
     * Starts the session and sets the test environment flags.
     */
    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        // Mark PHPUnit test mode
        $_ENV['PHPUNIT_RUNNING'] = true;

        if (!defined('PHPUNIT_RUNNING')) {
            define('PHPUNIT_RUNNING', true);
        }
    }

    /**
     * Simulates a login by injecting a fake Auth0 user into the session.
     *
     * @param array $overrides Keys to override in the default user structure.
     * @return array The resulting user stored in session.
     */
    public function fakeAuth0User(array $overrides = []): array
    {
        $defaultUser = [
            'sub'      => 'auth0|testuser123',
            'email'    => 'testuser@example.com',
            'nickname' => 'TestUser',
            'role'     => 'User',
        ];

        $user = array_merge($defaultUser, $overrides);

        // If app_metadata contains role, sync it to top-level 'role'
        if (isset($overrides['app_metadata']['role'])) {
            $user['role'] = $overrides['app_metadata']['role'];
        }

        $_SESSION['user'] = $user;
        return $user;
    }

    /**
     * Clears the session user to simulate logout.
     */
    protected function clearAuth0Session(): void
    {
        $_SESSION = [];
    }

    /**
     * Returns the session user, if any.
     *
     * @return array|null
     */
    protected function get_session_user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}