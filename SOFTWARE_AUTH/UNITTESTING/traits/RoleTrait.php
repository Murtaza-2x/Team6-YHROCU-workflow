<?php
// -------------------------------------------------------------
// Trait: RoleTrait
// Purpose: Simulate Auth0 users with different roles in session
// -------------------------------------------------------------

trait RoleTrait
{
    protected function loginAs(array $overrides = []): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $defaultUser = [
            'sub' => 'auth0|testuser123',
            'email' => 'testuser@example.com',
            'nickname' => 'TestUser',
            'role' => 'user'
        ];

        $user = array_merge($defaultUser, $overrides);
        $_SESSION['user'] = $user;

        return $user;
    }

    protected function loginAsAdmin(): array
    {
        return $this->loginAs([
            'role' => 'admin',
            'nickname' => 'AdminUser'
        ]);
    }

    protected function loginAsModerator(): array
    {
        return $this->loginAs([
            'role' => 'moderator',
            'nickname' => 'ModUser'
        ]);
    }

    protected function logout(): void
    {
        $_SESSION = [];
    }
}
