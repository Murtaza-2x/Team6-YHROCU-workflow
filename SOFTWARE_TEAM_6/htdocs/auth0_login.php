<?php
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';

session_start();

$auth0 = Auth0Factory::create();
$authorizeUrl = $auth0->login();

$authorizeUrl .= '&login_hint=' . urlencode($_SESSION['login_email'] ?? '');

header('Location: ' . $authorizeUrl);
exit;