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

session_start(); // Start session to store user data

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

// Save user details to session
$_SESSION['user'] = $fullUser;

// Redirect to the task list page
header('Location: list-task-page.php');
exit;