<?php
/* 
This file contains the main content for the admin page, which allows an admin to manage users.
Functionality includes:
  - Creating a new user (with fields for username, email, password, and clearance).
  - Editing existing user details (username, email, clearance, and password if provided).
  - Toggling a user's status between Active and Disabled.
  - Deleting a user.
*/

$title = "Admin Panel";
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';

require_role('Admin');

require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];
    $allowed_roles = ['Admin', 'User'];

    if (!in_array($newRole, $allowed_roles)) {
        echo "<p>Invalid role selected.</p>";
    } elseif ($userId === ($_SESSION['user']['sub'] ?? $_SESSION['user']['user_id']) && $newRole !== 'Admin') {
        echo "<p>You cannot remove your own Admin role.</p>";
    } else {
        $result = Auth0UserManager::updateUserRole($userId, $newRole);
        if (isset($result['user_id'])) {
            echo "<p>User role updated successfully.</p>";
        } else {
            echo "<p>Failed to update user role.</p>";
        }
    }
}

$users = Auth0UserManager::getUsers();

include 'INCLUDES/inc_adminpage.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
