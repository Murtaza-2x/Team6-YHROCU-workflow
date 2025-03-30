<?php
session_start();
session_destroy();

$auth0_domain = 'dev-1kz8p05uenan0uz6.us.auth0.com';
$returnTo = urlencode('http://localhost/YHROCU-CLONE/Team6-YHROCU-workflow/SOFTWARE_TEAM_6/htdocs/index.php');

header("Location: https://$auth0_domain/v2/logout?returnTo=$returnTo&client_id=BcXE3qlpzEEfSprHX2DIVXCJUJIdBoqp");
exit();
?>
