<?php
// -------------------------------------------------------------
// Trait: RolePolicyTrait
// Purpose: Define simple permission rules for roles during tests
// -------------------------------------------------------------

trait RolePolicyTrait
{
    protected function can(string $permission): bool
    {
        $role = $_SESSION['user']['role'] ?? 'guest';

        // Permission map
        $permissions = [
            'guest' => [],
            'user' => ['view_dashboard'],
            'manager' => ['view_dashboard', 'edit_tasks'],
            'admin' => ['view_dashboard', 'edit_tasks', 'manage_users', 'view_logs'],
        ];

        return in_array($permission, $permissions[$role] ?? []);
    }
}
