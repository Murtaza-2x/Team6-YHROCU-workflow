<?php
/*
-------------------------------------------------------------
File: admin-page.php
Description:
- Displays the Admin Panel.
- Handles:
    > User creation
    > Role updates
    > Password reset link generation
    > Shows all Auth0 users
    > Disable and Delete users
-------------------------------------------------------------

*/

$title = "ROCU: Admin Panel";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';

// Check if the user has Admin role
if (!has_role('Admin')) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Prepare allowed roles
$allowed_roles = ['User', 'Manager', 'Admin'];

$errorMsg = "";
$successMsg = "";

// Create user functionality
if (isset($_POST['create_user'])) {
    $email    = trim($_POST['new_email']     ?? '');
    $password = trim($_POST['new_password']  ?? '');
    $role     = trim($_POST['new_role']      ?? 'User');

    if (!$email || !$password || !in_array($role, $allowed_roles)) {
        $errorMsg = "Please fill all fields correctly.";
    } else {
        try {
            Auth0UserManager::createUser($email, $password, $role);
            $successMsg = "User created successfully.";
        } catch (Exception $ex) {
            $errorMsg = "Error creating user: " . htmlspecialchars($ex->getMessage());
        }
    }
}

// Change Role functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $userId = $_POST['change_role'];
    $role = $_POST['role_change'][$userId] ?? 'User';
    if (!in_array($role, $allowed_roles)) {
        $errorMsg = "Invalid role selected.";
    } else {
        Auth0UserManager::updateUserRole($userId, $role);
        $successMsg = "Role updated successfully.";
    }
}

// Generate Password Reset Link functionality
if (isset($_POST['reset_password'])) {
    $userId = $_POST['reset_password'];
    try {
        $resetLink = Auth0UserManager::generatePasswordResetLink ($userId);
        $successMsg = "Password Reset Link:<br>
            <input class='INPUT-GROUP-3' type='text' id='reset-link' value='" . htmlspecialchars($resetLink) . "' readonly>
            <div class='TASK-BUTTONS'>
            <button class='CREATE-BUTTON' type='button' onclick='copyResetLink()'>Copy Link</button>
            </div>";
    } catch (Exception $e) {
        $errorMsg = "Error creating reset link: " . htmlspecialchars($e->getMessage());
    }
}

// Handle disabling a user
if (isset($_GET['disable_user'])) {
    $userId = $_GET['disable_user'];
    try {
        // Fetch the current status of the user
        $user = Auth0UserManager::getUser($userId);
        $currentStatus = $user['app_metadata']['status'] ?? 'active'; // Default to 'active'

        // Toggle status between active and inactive
        $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

        // Update user status
        Auth0UserManager::updateUserRole($userId, $user['app_metadata']['role'], $newStatus);
        $successMsg = "User status updated to " . ucfirst($newStatus) . " successfully.";
    } catch (Exception $e) {
        $errorMsg = "Error updating user status: " . htmlspecialchars($e->getMessage());
    }
}

// Handle deleting a user
if (isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    try {
        Auth0UserManager::deleteUser($userId);
        $successMsg = "User deleted successfully.";
    } catch (Exception $e) {
        $errorMsg = "Error deleting user: " . htmlspecialchars($e->getMessage());
    }
}

// Get Users functionality
$auth0_users = Auth0UserManager::getUsers();

include 'INCLUDES/inc_adminpage.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';

?>