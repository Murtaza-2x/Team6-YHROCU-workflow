<?php
require_once __DIR__ . '/vendor/autoload.php';

$auth0 = new \Auth0\SDK\Auth0([
    'domain' => 'dev-1kz8p05uenan0uz6.us.auth0.com',
    'clientId' => 'BcXE3qlpzEEfSprHX2DIVXCJUJIdBoqp',
    'redirectUri' => 'http://localhost/YHROCU-CLONE\Team6-YHROCU-workflow\SOFTWARE_TEAM_6\htdocs/auth0_callback.php',
    'audience' => '',
    'scope' => 'openid profile email',
]);

header('Location: ' . $auth0->login());
exit;
?>
