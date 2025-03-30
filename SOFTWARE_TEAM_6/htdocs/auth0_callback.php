<?php
require_once __DIR__ . '/vendor/autoload.php';
require 'INCLUDES/inc_connect.php';
require 'group_mapping.php';

$_SESSION["id"] = $user_id;
$_SESSION["email"] = $email;
$_SESSION["clearance"] = $clearance;

if (isset($_GET['error'])) {
    // Auth0 error detected (e.g. access_denied, login_required)
    $error_description = isset($_GET['error_description']) ? $_GET['error_description'] : $_GET['error'];
    header('Location: index.php?error=' . urlencode($error_description));
    exit();
}

$auth0 = new \Auth0\SDK\Auth0([
    'domain' => 'dev-1kz8p05uenan0uz6.us.auth0.com',
    'clientId' => 'BcXE3qlpzEEfSprHX2DIVXCJUJIdBoqp',
    'clientSecret' => 'yk7WWnPZGji3VV4fk0wTHFCWdaGBlgmDTkvAmgBQAa_2jyAgyuSB1qWLJl9Xvyog',
    'redirectUri' => 'http://localhost/YHROCU-CLONE\Team6-YHROCU-workflow\SOFTWARE_TEAM_6\htdocs/auth0_callback.php',
]);

$token = $auth0->exchange();
$userInfo = $auth0->getUser();

// Extract user info
$email = $userInfo['email'];
$name = $userInfo['name'];
$roles = $userInfo['https://yourapp.example.com/roles'] ?? [];

// Step 7: Map Roles to Clearance
$clearance = mapAuth0RolesToClearance($roles);

// Step 8: Insert / Update DB User
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $status = "active";
    $random_pass = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, clearance, status, auth_source) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $okta_name, $okta_email, $random_password, $assigned_clearance, $active, $source);

    $source = 'auth0';
    
    $stmt->execute();
    $user_id = $stmt->insert_id;
} else {
    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    if ($user['clearance'] !== $clearance) {
        $stmt = $conn->prepare("UPDATE users SET clearance=? WHERE email=?");
        $stmt->bind_param("ss", $clearance, $email);
        $stmt->execute();
    }
}

// Step 9: Create Session
$_SESSION["id"] = $user_id;
$_SESSION["email"] = $email;
$_SESSION["clearance"] = $clearance;

header('Location: list-task-page.php?clearance=' . $_SESSION["clearance"] . '&id=' . $_SESSION["id"]);
exit;
