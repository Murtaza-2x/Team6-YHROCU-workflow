<?php
/*
-------------------------------------------------------------
File: auth0_login.php
Description:
- Handles the redirect to Auth0 for user login.
- Appends the login hint (email) if available in the session.
-------------------------------------------------------------
*/

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';

session_start(); // Start the session to access login email

// Create Auth0 object and generate login URL
$auth0 = Auth0Factory::create();
$authorizeUrl = $auth0->login();

// Append login hint if email exists in session
$authorizeUrl .= '&login_hint=' . urlencode($_SESSION['login_email'] ?? '');

// Redirect to Auth0 for authentication
header('Location: ' . $authorizeUrl);
exit;