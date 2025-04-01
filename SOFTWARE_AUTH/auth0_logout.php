<?php
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';

session_start();
session_destroy();
$domain    = $_ENV['AUTH0_DOMAIN'];
$client_id = $_ENV['AUTH0_CLIENT_ID'];
$returnTo  = urlencode('http://localhost/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_TEAM_6/htdocs/index.php');

$logoutUrl = "https://$domain/v2/logout?client_id=$client_id&returnTo=$returnTo&federated";

// Redirect
header("Location: $logoutUrl");
exit;