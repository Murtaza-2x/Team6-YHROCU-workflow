<?php
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
