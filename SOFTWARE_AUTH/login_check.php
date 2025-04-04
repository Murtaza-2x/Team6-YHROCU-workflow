<?php
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Retrieve the email from POST data and trim any extra spaces
$email = trim($_POST['email'] ?? '');

// Check if email is empty and redirect with an error message if so
if (empty($email)) {
    header('Location: index.php?error=1&msg=Please enter an email.');
    exit;
}

// Use dependency-injected or real instance of Auth0UserManager
$userManager = $GLOBALS['Auth0UserManager'] ?? new Auth0UserManager();

// Fetch users by email from Auth0 using the user manager
$users = $userManager->getUserByEmail($email);

// If no users are found, redirect with an error message
if (empty($users)) {
    header('Location: index.php?error=1&msg=User not found. Please contact the administrator.');
    exit;
}

// Store the email in session and redirect to the Auth0 login page
$_SESSION['login_email'] = $email;
header('Location: auth0_login.php');
exit;