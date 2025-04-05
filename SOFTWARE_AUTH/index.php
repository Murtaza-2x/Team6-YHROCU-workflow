<?php
/*
-------------------------------------------------------------
File: index.php
Description:
- Handles user login functionality.
- 1. Checks if the `email` field is submitted.
- 2. Looks up the user in the `users` table based on the submitted email.
- 3. If a user is found, verifies the provided password against the stored hash.
- 4. On success, saves user details (ID, email, clearance) in the session and redirects to the task list page.
- 5. If credentials are incorrect, an error message is shown.
-------------------------------------------------------------
*/

$title = "ROCU: Login";
?>

<?php
require_once __DIR__ . '/INCLUDES/role_helper.php';

$clearance = get_role();
?>

<?php
require 'INCLUDES/inc_connect.php';

// Check if DB connection failed
if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
    $dbError = "Database connection failed. Please try again later.";
    error_log("DB connection failed in index.php: " . ($conn->connect_error ?? 'Unknown error'));
}
?>

<?php require 'INCLUDES/inc_header.php'; ?>

<?php
// Show DB error if connection failed
if (isset($dbError)) {
    echo "<p class='ERROR-MESSAGE'>{$dbError}</p>";
} else {
    // Normal login display
    $errorMsg = '';
    if (isset($_GET['error'])) {
        $errorMsg = htmlspecialchars($_GET['msg'] ?? 'Unknown authentication error');
    }
    include 'INCLUDES/inc_login.php';
}
?>

<?php require __DIR__ . '/INCLUDES/inc_footer.php'; ?>
<?php require __DIR__ . '/INCLUDES/inc_disconnect.php'; ?>