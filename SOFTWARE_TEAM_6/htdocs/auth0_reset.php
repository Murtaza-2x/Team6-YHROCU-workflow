<?php
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';

$auth0 = Auth0Factory::create();
$url = $auth0->changePassword();
header("Location: $url");
exit();
?>
