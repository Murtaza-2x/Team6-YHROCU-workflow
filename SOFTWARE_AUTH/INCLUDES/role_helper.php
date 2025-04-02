<?php
/*
-------------------------------------------------------------
 File: ROLE_helper.php
 Description: 
 - Contains helpers related to roles
-------------------------------------------------------------
*/

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function get_role(): string {
    return $_SESSION['user']['role'] ?? 'User';
}

function has_role(string $requiredRole): bool {
    return get_role() === $requiredRole;
}

function require_role(string $requiredRole): void {
    if (!has_role($requiredRole)) {
        header('Location: index.php?error=clearance_required');
        exit;
    }
}

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: index.php?error=login_required');
        exit;
    }
}

function is_admin(): bool
{
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Admin';
}

function is_manager() {
    return get_role() === 'Manager';
}

function is_staff() {
    return is_admin() || is_manager();
}

function getAssignedUsers(int $taskId, mysqli $conn): array {
    $assigned = [];
    $stmt = $conn->prepare("SELECT auth0_user_id FROM task_assigned_users WHERE task_id = ?");
    $stmt->bind_param('i', $taskId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $assigned[] = $row['auth0_user_id'];
    }
    return $assigned;
}