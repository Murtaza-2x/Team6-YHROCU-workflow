<?php
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';
session_start();
session_destroy();

$auth0 = Auth0Factory::create();

header('Location: index.php');
exit;