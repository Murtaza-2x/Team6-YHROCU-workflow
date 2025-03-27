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

if (!isset($_SESSION['clearance']) || $_SESSION['clearance'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_user'])) {
        $username  = $_POST['username'];
        $email     = $_POST['email'];
        $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $clearance = $_POST['clearance'];

        $checkQuery = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkQuery->bind_param("ss", $username, $email);
        $checkQuery->execute();
        $checkQuery->store_result();

        if ($checkQuery->num_rows > 0) {
            $feedback = "<div class='feedback error'>Username or Email already exists. Please choose another.</div>";
        } else {
            $insertQuery = $conn->prepare("INSERT INTO users (username, email, password, clearance, status) VALUES (?, ?, ?, ?, 'Active')");
            $insertQuery->bind_param("ssss", $username, $email, $password, $clearance);
            $insertQuery->execute();
            $feedback = "<div class='feedback success'>User created successfully!</div>";
        }
        $checkQuery->close();
    }
    elseif (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        $conn->query("DELETE FROM users WHERE id = $userId");
    }
    elseif (isset($_POST['toggle_user'])) {
        $userId        = $_POST['user_id'];
        $currentStatus = $_POST['current_status'];
        $newStatus     = ($currentStatus === 'Active') ? 'Disabled' : 'Active';
        $conn->query("UPDATE users SET status = '$newStatus' WHERE id = $userId");
    }
    elseif (isset($_POST['edit_user'])) {
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
