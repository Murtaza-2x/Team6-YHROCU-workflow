<?php
/*
-------------------------------------------------------------
File: auth0_logout.php
Description:
- Logs out the user by destroying the session.
- Redirects to the Auth0 logout endpoint for federated logout.
- After logout, redirects to the home page.
-------------------------------------------------------------
*/

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';

session_start();
session_destroy(); // Destroy the current session

// Set up Auth0 logout parameters
$domain    = $_ENV['AUTH0_DOMAIN'];
$client_id = $_ENV['AUTH0_CLIENT_ID'];
$returnTo  = urlencode('http://localhost/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/index.php');

$logoutUrl = "https://$domain/v2/logout?client_id=$client_id&returnTo=$returnTo&federated";

// Redirect to Auth0 logout URL
header("Location: $logoutUrl");
exit;