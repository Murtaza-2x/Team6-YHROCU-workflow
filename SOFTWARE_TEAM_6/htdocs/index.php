<?php
/*
This file handles user login functionality.
1. Checks if the `email` field is submitted.
2. Looks up the user in the `users` table based on the submitted email.
3. If a user is found, verifies the provided password against the stored hash.
4. On success, saves user details (ID, email, clearance) in the session and redirects to the task list page.
5. If credentials are incorrect, an error message is shown.
*/

$title = "ROCU: Login";
?>

<?php
require_once __DIR__ . '/INCLUDES/role_helper.php';

$clearance = get_role();
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>

<?php
$errorMsg = '';
if (isset($_GET['error'])) {
    $errorMsg = htmlspecialchars($_GET['msg'] ?? 'Unknown authentication error');
}

include 'INCLUDES/inc_login.php';
?>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>
