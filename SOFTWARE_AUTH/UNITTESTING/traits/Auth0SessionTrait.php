<?php
// -------------------------------------------------------------
// File: UNITTESTING/traits/Auth0SessionTrait.php
// Purpose: Simulate Auth0 user sessions with role support for tests
// -------------------------------------------------------------

trait Auth0SessionTrait
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = []; // Reset session at the start of each test
    }

    // Creates a fake Auth0 user with optional overrides
    protected function fakeAuth0User(array $overrides = []): array
    {
        $defaultUser = [
            'sub'      => 'auth0|testuser123',
            'email'    => 'testuser@example.com',
            'nickname' => 'TestUser',
            'role'     => 'user',  // Default role is "user"
        ];
        $user = array_merge($defaultUser, $overrides);
        $_SESSION['user'] = $user;
        return $user;
    }

    // Shortcut for logging in as a regular user.
    protected function loginAsUser(array $extra = []): array
    {
        return $this->fakeAuth0User(array_merge(['role' => 'user'], $extra));
    }

    // Shortcut for logging in as an admin.
    protected function loginAsAdmin(array $extra = []): array
    {
        return $this->fakeAuth0User(array_merge(['role' => 'admin'], $extra));
    }

    // Shortcut for logging in as a moderator.
    protected function loginAsModerator(array $extra = []): array
    {
        return $this->fakeAuth0User(array_merge(['role' => 'moderator'], $extra));
    }

    // Clears the session so that no user is logged in.
    protected function clearAuth0Session(): void
    {
        $_SESSION = [];
    }
}