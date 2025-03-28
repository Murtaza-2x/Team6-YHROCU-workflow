<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/pill_styles.css" rel="stylesheet">
    <link href="CSS/dropdown_styles.css" rel="stylesheet">
    <link href="CSS/admin_styles.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="JS/SEARCH-USERS.js"></script>
    <script src="JS/ADMIN-ACTIONS.js"></script>

</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- ADMIN SECTION -->
<div class='ADMIN-CONTAINER'>
    <div class='ADMIN-BOX'>
        <div class='ADMIN-HEAD'>
            <h1>User Management</h1>
            <p>Manage Users below</p>
        </div>

        <!-- FORM -->
        <form method="post">
            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL">
                    <label for="username">Username:</label>
                </div>
                <div class="INPUT-GROUP">
                    <input type="text" id="username" name="username" placeholder="New Username" required>
                </div>
            </div>

            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL">
                    <label for="email">Email:</label>
                </div>
                <div class="INPUT-GROUP">
                    <input type="email" id="email" name="email" placeholder="New Email" required>
                </div>
            </div>

            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL">
                    <label for="password">Password:</label>
                </div>
                <div class="INPUT-GROUP">
                    <input type="text" id="password" name="password" placeholder="New Password" required>
                </div>
            </div>

            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL">
                    <label for="clearance">Role:</label>
                </div>
                <div class="INPUT-GROUP">
                    <select id="clearance" class="DROPDOWN-GROUP" name="clearance" required>
                        <option value="">Select Role</option>
                        <option value="User">User</option>
                        <option value="Manager">Manager</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="TASK-BUTTONS">
                <button type="submit" name="create_user" class="CREATE-BUTTON">Create User</button>
            </div>
        </form>
        <!-- FORM END -->

        <!-- SEARCH BAR -->
        <div class="ADMIN-FILTER">
            <input type="text" id="searchInput" placeholder="Search...">
            <button>Filter</button>
        </div>
        <!-- SEARCH BAR END -->

        <!-- USER TABLE -->
        <div class="ADMIN-CONTENT">

            <div class="ADMIN-AREA">
                <div class="ADMIN-LIST">
                    <table class="ADMIN-TABLE" id="USER-TABLE">
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
                                <form method="post" class="inline-form user-row-form">
                                    <tr>
                                        <td>
                                            <div class="INPUT-GROUP-2">
                                                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="INPUT-GROUP-2">
                                                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="INPUT-GROUP-2">
                                                <select name="clearance" class="DROPDOWN-GROUP-3" disabled>
                                                    <option value="User" <?= $user['clearance'] === 'User' ? 'selected' : '' ?>>User</option>
                                                    <option value="Manager" <?= $user['clearance'] === 'Manager' ? 'selected' : '' ?>>Manager</option>
                                                    <option value="Admin" <?= $user['clearance'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="status <?= strtolower(trim($user['status'])) ?>">
                                            <?php
                                            $status = $user['status'];
                                            switch ($status) {
                                                case 'Active':
                                                    echo "<button class='PILL-NEW PILL-ACTIVE'>Active</button>";
                                                    break;
                                                case 'Disabled':
                                                    echo "<button class='PILL-INACTIVE'>Disabled</button>";
                                                    break;
                                                default:
                                                    echo "<button class='PILL-INACTIVE'>$status</button>";
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <div class="INPUT-INLINE">
                                                <div class="INPUT-GROUP-3">
                                                    <input type="text" name="password" placeholder="Password" readonly>
                                                </div>
                                                <button type="button" class="btn-secondary edit-btn">Edit</button>
                                                <button type="submit" name="edit_user" class="btn-primary action-btn" style="display:none;">Save</button>
                                                <?php if ($user['id'] != $_SESSION['id']): ?>
                                                    <input type="hidden" name="current_status" value="<?= $user['status'] ?>">
                                                    <button type="submit" name="toggle_user" class="btn-warning action-btn" style="display:none;">
                                                        <?= $user['status'] === 'Active' ? 'Disable' : 'Re-enable' ?>
                                                    </button>
                                                    <button type="submit" name="delete_user" class="btn-danger action-btn" style="display:none;" onclick="return confirm('Delete this user?');">Delete</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                </form>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- USER TABLE END -->
    </div>
</div>
<!-- ADMIN SECTION END -->