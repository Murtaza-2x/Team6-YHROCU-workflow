<?php
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';
session_start();

// Retrieve the email from POST data and trim any extra spaces
$email = trim($_POST['email'] ?? '');

// Check if email is empty and redirect with an error message if so
if (empty($email)) {
    header('Location: index.php?error=1&msg=Please enter an email.');
    exit;
}

// Fetch users by email from Auth0 using the user manager
$users = Auth0UserManager::getUserByEmail($email);

// If no users are found, redirect with an error message
if (empty($users)) {
    header('Location: index.php?error=1&msg=User not found. Please contact the administrator.');
    exit;
}

// Store the email in session and redirect to the Auth0 login page
$_SESSION['login_email'] = $email;
header('Location: auth0_login.php');
exit;