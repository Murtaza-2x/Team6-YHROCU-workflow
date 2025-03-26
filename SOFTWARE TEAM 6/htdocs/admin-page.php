<?php
// ** This is an admin page - which allows an admin when logged in to change user details and see existing user details/stauses and update them **

session_start();
require_once 'INCLUDES/inc_connect.php';

// Checks if the user is an admin, if they aren't redirect to homepage. 
if (!isset($_SESSION['clearance']) || $_SESSION['clearance'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

$title = "Admin Panel";
include 'INCLUDES/inc_header.php';

// Handle form submissions for creating, editing, toggling, and deleting users.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new user
    if (isset($_POST['create_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $clearance = $_POST['clearance'];
        $conn->query("INSERT INTO users (username, email, password, clearance, status) VALUES ('$username', '$email', '$password', '$clearance', 'Active')");
    
    // Delete an existing user
    } elseif (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        $conn->query("DELETE FROM users WHERE id = $userId");

    // Toggle user status between Active and Disabled
    } elseif (isset($_POST['toggle_user'])) {
        $userId = $_POST['user_id'];
        $currentStatus = $_POST['current_status'];
        $newStatus = ($currentStatus === 'Active') ? 'Disabled' : 'Active';
        $conn->query("UPDATE users SET status = '$newStatus' WHERE id = $userId");
    
    // Edit user details
    } elseif (isset($_POST['edit_user'])) {
        $userId = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $clearance = $_POST['clearance'];
        $updateQuery = "UPDATE users SET username = '$username', email = '$email', clearance = '$clearance'";
    
    // Update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $updateQuery .= ", password = '$password'";
        }

        $updateQuery .= " WHERE id = $userId";
        $conn->query($updateQuery);
    }
}

// Retreive all user records to display in admin panel
$result = $conn->query("SELECT id, username, email, clearance, status FROM users");
?>

<div class="TASK-CONTENT">
    <h2>User Management</h2>

    <!-- Form for creating a new user --> 
    <form method="post" style="margin-bottom: 2rem;">
        <input type="text" name="username" placeholder="New Username" required>
        <input type="email" name="email" placeholder="New Email" required>
        <input type="password" name="password" placeholder="New Password" required>
        <select name="clearance">
            <option value="User">User</option>
            <option value="Manager">Manager</option>
            <option value="Admin">Admin</option>
        </select>
        <button type="submit" name="create_user">Create User</button>
    </form>

    <!-- Table displaying all users with edit and management actions -->
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Clearance</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <!-- Form for editing each user's information -->
                <form method="post">
                    <td>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </td>
                    <td>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </td>
                    <td>
                        <!-- Hidden fields to pass user ID and current status -->
                        <select name="clearance">
                            <option value="User" <?= $user['clearance'] === 'User' ? 'selected' : '' ?>>User</option>
                            <option value="Manager" <?= $user['clearance'] === 'Manager' ? 'selected' : '' ?>>Manager</option>
                            <option value="Admin" <?= $user['clearance'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </td>
                    <td><?= $user['status'] ?></td>
                    <td>
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="password" name="password" placeholder="New password (optional)">
                        <button type="submit" name="edit_user">Save</button>

                        <?php if ($user['id'] != $_SESSION['id']): ?>
                            <input type="hidden" name="current_status" value="<?= $user['status'] ?>">
                            <button type="submit" name="toggle_user">
                                <?= $user['status'] === 'Active' ? 'Disable' : 'Re-enable' ?>
                            </button>
                            <button type="submit" name="delete_user" onclick="return confirm('Delete this user?');">Delete</button>
                        <?php endif; ?>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>
