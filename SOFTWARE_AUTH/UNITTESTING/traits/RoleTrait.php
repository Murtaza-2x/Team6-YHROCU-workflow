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

    protected function loginAsManager(): array
    {
        return $this->loginAs([
            'role' => 'manager',
            'nickname' => 'managerUser'
        ]);
    }

    protected function loginAsUser(): array
    {
        return $this->loginAs([
            'role' => 'user',
            'nickname' => 'defaultUser'
        ]);
    }

    protected function logout(): void
    {
        $_SESSION = [];
    }
}
