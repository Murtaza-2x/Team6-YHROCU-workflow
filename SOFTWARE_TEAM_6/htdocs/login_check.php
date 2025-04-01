<?php
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php';
session_start();

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    header('Location: index.php?error=1&msg=Please enter an email.');
    exit;
}

$users = Auth0UserManager::getUserByEmail($email);

if (empty($users)) {
    header('Location: index.php?error=1&msg=User not found. Please contact the administrator.');
    exit;
}

$_SESSION['login_email'] = $email;
header('Location: auth0_login.php');
exit;
