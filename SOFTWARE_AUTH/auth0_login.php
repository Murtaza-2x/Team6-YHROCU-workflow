<?php
/*
-------------------------------------------------------------
File: auth0_login.php
Description:
- Clears the local session and silently logs out from Auth0 (federated).
- Immediately redirects the user to Auth0 for a fresh login.
- Appends login hint (email) if available in the session or via URL param.
-------------------------------------------------------------
*/
 
require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/Auth0Factory.php';
 
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
 
// Capture login email from URL param or session (before destroying session)
$email = $_GET['email'] ?? ($_SESSION['login_email'] ?? null);
 
// Clear local session
session_unset();
session_destroy();
 
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
 
// Prepare Auth0 credentials
$auth0_domain = $_ENV['AUTH0_DOMAIN'];
$client_id    = $_ENV['AUTH0_CLIENT_ID'];
 
// Build returnTo URL — must match an Allowed Logout URL in the Auth0 dashboard
$returnTo = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?auth0_logged_out=1' .
            (!empty($email) ? '&email=' . urlencode($email) : '');
 
// First-time visit: logout from Auth0 (federated) and redirect back
if (!isset($_GET['auth0_logged_out'])) {
    $logoutUrl = "https://$auth0_domain/v2/logout?client_id=$client_id&returnTo=" . urlencode($returnTo);
 
    echo '<!DOCTYPE html>
<html>
<head>
<title>Redirecting to login...</title>
</head>
<body>
<iframe src="' . htmlspecialchars($logoutUrl) . '" style="display:none;" onload="redirectToLogin()"></iframe>
<script>
            function redirectToLogin() {
                window.location.href = "' . htmlspecialchars($returnTo) . '";
            }
</script>
</body>
</html>';
    exit;
}
 
// After returning from logout: begin clean Auth0 login
$auth0 = Auth0Factory::create();
 
$params = [
    'prompt' => 'login',       // Force showing the login form
    'max_age' => 0,            // Prevent silent session reuse
    'screen_hint' => 'login'   // Ensure login UI is shown (not signup)
];
 
// Append login_hint if available
if (!empty($email)) {
    $params['login_hint'] = $email;
}
 
$authorizeUrl = $auth0->login(null, null, $params);
header('Location: ' . $authorizeUrl);
exit;