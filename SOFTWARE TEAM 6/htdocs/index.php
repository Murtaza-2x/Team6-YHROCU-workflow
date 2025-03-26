<?php
/*
This file handles user login functionality. It includes the database connection and required headers before processing the login form:
1. Checks if the `email` field is submitted.
2. Looks up the user in the `users` table based on the submitted email.
3. If a user is found, verifies the provided password against the stored password.
4. On success, saves user details (ID, username, clearance) in the session and redirects to the task list page.
5. If credentials are incorrect, an error message is shown.
*/

$title = "ROCU: Login";
?>

<?php include 'INCLUDES/inc_connect.php'; ?>
<?php include 'INCLUDES/inc_header.php'; ?>

<?php
$errorMsg = '';

if (isset($_POST["email"])) {
    $sql = "SELECT * FROM users WHERE email = '" . $_POST["email"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $correct_pass = $row["password"];
        if ($_POST["password"] == $correct_pass) {
            $_SESSION["id"] = $row["id"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["clearance"] = $row["clearance"];
            header('Location: list-task-page.php?clearance=' . $clearance . '&id=' . $id);
        } else {
            $errorMsg = 'Incorrect Email Address or Password';
        }
    } else {
        $errorMsg = 'Incorrect Email Address or Password';
    }
}
include 'INCLUDES/inc_login.php';
?>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>
