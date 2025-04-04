<?php
/*
-------------------------------------------------------------
File: role_helper.php
Description: 
- Contains helpers related to roles
-------------------------------------------------------------
*/

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Get the current user's role, default to 'User' if not set
function get_role(): string {
    return $_SESSION['user']['role'] ?? 'User';
}

// Check if the current user has the specified role
function has_role(string $requiredRole): bool {
    return get_role() === $requiredRole;
}

// Require the specified role for access, otherwise redirect
function require_role(string $requiredRole): void {
    if (!has_role($requiredRole)) {
        header('Location: index.php?error=clearance_required');
        exit;
    }
}

// Check if the user is logged in by checking the session
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

// Require login for access, otherwise redirect
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: index.php?error=login_required');
        exit;
    }
}

// Check if the user is an admin
function is_admin(): bool
{
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Admin';
}

// Check if the user is a manager
function is_manager() {
    return get_role() === 'Manager';
}

// Check if the user is either an admin or a manager
function is_staff() {
    return is_admin() || is_manager();
}

// Get the list of assigned users for a specific task
function getAssignedUsers(int $taskId, mysqli $conn): array {
    $assigned = [];
    $stmt = $conn->prepare("SELECT auth0_user_id FROM task_assigned_users WHERE task_id = ?");
    $stmt->bind_param('i', $taskId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $assigned[] = $row['auth0_user_id']; // Collect assigned user IDs
    }
    return $assigned;
}

// Get users session role
function get_session_user(): ?array {
    return $_SESSION['user'] ?? null;
}