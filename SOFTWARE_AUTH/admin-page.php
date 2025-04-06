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

// Dependency injection-compatible
$userManager = $GLOBALS['Auth0UserManager'] ?? new Auth0UserManager();

// Check if the user has Admin role
if (!has_role('Admin')) {
    echo "<p class='ERROR-MESSAGE'>You are not authorized to view this page.</p>";
    include 'INCLUDES/inc_footer.php';
    exit;
}

// Prepare allowed roles
$allowed_roles = ['User', 'Manager', 'Admin'];

$errorMsg = $_GET['error'] ?? '';
$successMsg = $_GET['success'] ?? '';

// Create user functionality
if (isset($_POST['create_user'])) {
    $email    = trim($_POST['new_email']     ?? '');
    $password = trim($_POST['new_password']  ?? '');
    $role     = trim($_POST['new_role']      ?? 'User');

    if (!$email || !$password || !in_array($role, $allowed_roles)) {
        $errorMsg = "Please fill all fields correctly.";
    } else {
        try {
            $userManager->createUser($email, $password, $role);
            $successMsg = "User created successfully.";
        } catch (Exception $ex) {
            $errorMsg = "Error creating user: " . htmlspecialchars($ex->getMessage());
        }
    }
}

// Role + Email Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId   = $_POST['update_user'];
    $newRole  = $_POST['role_change'][$userId] ?? 'User';
    $newEmail = trim($_POST['email_change'][$userId] ?? '');

    try {
        if (!is_string($userId) || trim($userId) === '') {
            throw new Exception("Missing or invalid user ID.");
        }

        if (!in_array($newRole, $allowed_roles)) {
            throw new Exception("Invalid role selected.");
        }

        $existingUser = $userManager->getUser($userId);
        $currentEmail = $existingUser['email'] ?? '';

        // Update email only if changed
        if ($newEmail !== $currentEmail) {
            $userManager->updateUserEmail($userId, $newEmail);
        }

        // Update role
        $userManager->updateUserRole($userId, $newRole);

        // Redirect with success message
        header("Location: admin-page.php?success=" . urlencode("User updated successfully."));
        exit;
    } catch (Exception $e) {
        header("Location: admin-page.php?error=" . urlencode("Update failed: " . $e->getMessage()));
        exit;
    }
}


// Generate Password Reset Link functionality
if (isset($_POST['reset_password'])) {
    $userId = $_POST['reset_password'];
    try {
        $resetLink = $userManager->generatePasswordResetLink($userId);
        $successMsg = "Password Reset Link:<br>
            <input class='INPUT-GROUP-3' type='text' id='reset-link' value='" . htmlspecialchars($resetLink) . "' readonly>
            <div class='TASK-BUTTONS'>
            <button class='CREATE-BUTTON' type='button' onclick='copyResetLink()'>Copy Link</button>
            </div>";
    } catch (Exception $e) {
        header("Location: admin-page.php?error=" . urlencode("Error creating reset link: " . $e->getMessage()));
        exit;
    }
}

// Handle disabling a user
if (isset($_GET['disable_user'])) {
    $userId = $_GET['disable_user'];
    try {
        // Fetch the current status of the user
        $user = $userManager->getUser($userId);
        $currentStatus = $user['app_metadata']['status'] ?? 'active'; // Default to 'active'

        // Toggle status between active and inactive
        $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

        // Update user status
        $userManager->updateUserRole($userId, $user['app_metadata']['role'], $newStatus);

        // Redirect with success message
        header("Location: admin-page.php?success=" . urlencode("User status updated to " . ucfirst($newStatus) . " successfully."));
        exit;
    } catch (Exception $e) {
        header("Location: admin-page.php?error=" . urlencode("Error updating user status: " . $e->getMessage()));
        exit;
    }
}

// Handle deleting a user
if (isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    try {
        $userManager->deleteUser($userId);
        // Redirect with success message
        header("Location: admin-page.php?success=" . urlencode("User deleted successfully."));
        exit;
    } catch (Exception $e) {
        header("Location: admin-page.php?error=" . urlencode("Error deleting user: " . $e->getMessage()));
    }
}

// Get Users functionality
$auth0_users = $userManager->getUsers();

require __DIR__ . '/INCLUDES/inc_adminpage.php';
require __DIR__ . '/INCLUDES/inc_footer.php';
require __DIR__ . '/INCLUDES/inc_disconnect.php';
