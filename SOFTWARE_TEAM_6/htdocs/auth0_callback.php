<?php
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';

session_start();

$auth0 = Auth0Factory::create();

$auth0->exchange(); // ← exchanges code for tokens

$user = $auth0->getUser();

if (!$user) {
    header('Location: index.php?error=1&msg=Failed to get user info');
    exit;
}

// ✔ Store user in session!
$_SESSION['user'] = $user;

// Optional Debug:
// echo '<pre>'; print_r($user); echo '</pre>'; exit;

// Redirect to index (or dashboard)
header('Location: list-task-page.php');
exit;
