<?php
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';

session_start();

$auth0 = Auth0Factory::create();
$auth0->exchange();

$rawUser = $auth0->getUser();
if (!$rawUser || !isset($rawUser['sub'])) {
    header('Location: index.php?error=1&msg=Failed to get user info');
    exit;
}

$fullUser = Auth0UserManager::getUser($rawUser['sub']);
$fullUser['role'] = ucfirst(strtolower($fullUser['app_metadata']['role'] ?? 'User'));

$_SESSION['user'] = $fullUser;

header('Location: list-task-page.php');
exit;
