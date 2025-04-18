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

$isTesting = defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION = [];

// During PHPUnit, don't redirect. Output confirmation for test.
if ($isTesting) {
    echo "Logout simulated: session cleared";
    return;
}

require_once __DIR__ . '/../../INCLUDES/env_loader.php';
require_once __DIR__ . '/../../INCLUDES/Auth0Factory.php';

// Clear session data.
$_SESSION = [];

// Clear session data and destroy the session
session_unset();
session_destroy();

// Clear the session cookie to prevent caching
setcookie(session_name(), '', time() - 3600, '/');  // Expire the session cookie

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true); 

// Set up Auth0 logout parameters
$domain    = $_ENV['AUTH0_DOMAIN'];
$client_id = $_ENV['AUTH0_CLIENT_ID'];
$returnTo  = urlencode('http://localhost/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_AUTH/index.php');

// Redirect to Auth0 logout URL for federated logout
$logoutUrl = "https://$domain/v2/logout?client_id=$client_id&returnTo=$returnTo&federated";

// Redirect to logout URL and force a fresh session on next login
header("Location: $logoutUrl");
exit;
?>
