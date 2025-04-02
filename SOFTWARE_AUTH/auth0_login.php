<?php
/*
-------------------------------------------------------------
File: auth0_login.php
Description:
- Handles the redirect to Auth0 for user login.
- Appends the login hint (email) if available in the session.
-------------------------------------------------------------
*/

use Auth0\SDK\API\Management;
use Auth0\SDK\Configuration\SdkConfiguration;

function getAuth0User($userId) {
    $token = Auth0TokenManager::getToken();
    $config = new SdkConfiguration([
        'domain'          => $_ENV['AUTH0_DOMAIN'],
        'clientId'        => $_ENV['AUTH0_MGMT_CLIENT_ID'],
        'clientSecret'    => $_ENV['AUTH0_MGMT_CLIENT_SECRET'],
        'managementToken' => $token,
    ]);
    $mgmt = new Management($config);
    $user = $mgmt->users()->get($userId);
    return json_decode($user->getBody(), true);
}

function checkUserStatus($userId) {
    $user = getAuth0User($userId);

    // Check if the user's status is "active"
    if (isset($user['app_metadata']['status']) && $user['app_metadata']['status'] !== 'active') {
        return false;  // User is inactive
    }
    return true;  // User is active
}

// After getting the user authentication
if (!is_logged_in()) {
    header("Location: index.php?error=login_required");
    exit;
}

$userId = $_SESSION['user']['user_id']; // Assuming user_id is stored in session

if (!checkUserStatus($userId)) {
    // If the user is inactive, prevent login and show an error message
    echo "<p class='ERROR-MESSAGE'>Your account is inactive. Please contact an administrator.</p>";
    exit;  // Terminate the script to prevent login
}

// Proceed with the login process if the user is active
