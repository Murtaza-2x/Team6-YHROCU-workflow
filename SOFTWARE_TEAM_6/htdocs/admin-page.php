<?php
/* 
This file contains the main content for the admin page, which allows an admin to manage users.
Functionality includes:
  - Creating a new user (with fields for username, email, password, and clearance).
  - Editing existing user details (username, email, clearance, and password if provided).
  - Toggling a user's status between Active and Disabled.
  - Deleting a user.
*/

$title = "Admin Panel";
include 'INCLUDES/inc_connect.php';
include 'INCLUDES/inc_header.php';
require_once 'INCLUDES/Auth0Manager.php';

if (!isset($_SESSION['clearance']) || $_SESSION['clearance'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        $conn->query("DELETE FROM users WHERE id = $userId");
    } elseif (isset($_POST['toggle_user'])) {
        $userId        = $_POST['user_id'];
        $currentStatus = $_POST['current_status'];
        $newStatus     = ($currentStatus === 'Active') ? 'Disabled' : 'Active';
        $conn->query("UPDATE users SET status = '$newStatus' WHERE id = $userId");
    } elseif (isset($_POST['edit_user'])) {
        $userId    = $_POST['user_id'];
        $username  = $_POST['username'];
        $email     = $_POST['email'];
        $clearance = $_POST['clearance'];
        $updateQuery = "UPDATE users SET username = '$username', email = '$email', clearance = '$clearance'";
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $updateQuery .= ", password = '$password'";
        }
        $updateQuery .= " WHERE id = $userId";
        $conn->query($updateQuery);
    }
}

$result = $conn->query("SELECT id, username, email, clearance, status FROM users");

include 'INCLUDES/inc_adminpage.php';
include 'INCLUDES/inc_footer.php';
include 'INCLUDES/inc_disconnect.php';
