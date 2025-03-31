<?php
require_once 'INCLUDES/Auth0Factory.php';

session_start();
$auth0 = Auth0Factory::create();

// Log out from Auth0 and clear session
$auth0->logout();

// Optionally clear PHP session too
session_unset();
session_destroy();

header('Location: index.php');
exit();
