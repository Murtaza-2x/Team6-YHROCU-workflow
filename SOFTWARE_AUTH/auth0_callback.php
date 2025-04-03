<?php
/*
-------------------------------------------------------------
File: auth0_callback.php
Description:
- Handles the Auth0 callback after successful authentication.
- Retrieves user information from Auth0 and saves it to the session.
- Redirects to the task list page upon successful login.
-------------------------------------------------------------
*/

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';

// Prevent the browser from caching the page
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start(); // Start session to store user data
session_unset();
session_regenerate_id(true);

// Create Auth0 instance and exchange the code for tokens
$auth0 = Auth0Factory::create();
$auth0->exchange();

// Get user information from Auth0
$rawUser = $auth0->getUser();

// Check if user exists in Auth0
if (!$rawUser || !isset($rawUser['sub'])) {
    header('Location: index.php?error=1&msg=User does not exist');
    exit;
}

// Fetch additional user details from Auth0
$fullUser = Auth0UserManager::getUser($rawUser['sub']);

// Assign role to user, defaulting to 'User' if not found
$fullUser['role'] = ucfirst(strtolower($fullUser['app_metadata']['role'] ?? 'User'));

// Check the user's status in app_metadata
$status = $fullUser['app_metadata']['status'] ?? 'active';

if ($status === 'inactive') {
    $_SESSION = [];
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    header('Location: index.php?error=1&msg=Your%20account%20is%20disabled.%20Please%20contact%20an%20administrator.');
    exit;
}

// Save user details to session
$_SESSION['user'] = $fullUser;

// Redirect to the task list page or the dashboard
header('Location: list-task-page.php');
exit;
