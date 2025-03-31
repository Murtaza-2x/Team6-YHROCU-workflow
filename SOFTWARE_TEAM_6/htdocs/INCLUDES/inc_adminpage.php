<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/pill_styles.css" rel="stylesheet">
    <link href="CSS/dropdown_styles.css" rel="stylesheet">
    <link href="CSS/admin_styles.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="JS/SEARCH-USERS.js"></script>
    <script src="JS/ADMIN-ACTIONS.js"></script>
    <script src="JS/TOGGLE-DROPDOWN.js"></script>
    <script src="JS/PASSWORD-STRENGTH.js"></script>

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
                    <label for="email">Email:</label>
                </div>
                <div class="INPUT-WRAPPER">
                    <div class="INPUT-GROUP">
                        <input type="email" id="email" name="email" placeholder="New Email" required>
                    </div>
                </div>
            </div>

            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL">
                    <label for="password">Temp Password:</label>
                </div>
                <div class="INPUT-WRAPPER">
                    <div class="INPUT-GROUP">
                        <input type="text" id="password" name="password" placeholder="Temporary Password" oninput="updateStrength()" required>
                    </div>
                    <div id="STRENGTH-BAR">
                        <div id="STRENGTH-FILL"></div>
                    </div>
                    <small id="STRENGTH-FEEDBACK">Password Strength</small>
                </div>
            </div>

            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL">
                    <label for="clearance">Role:</label>
                </div>
                <div class="INPUT-WRAPPER">
                    <div class="INPUT-GROUP">
                        <select id="clearance" class="DROPDOWN-GROUP" name="clearance" required>
                            <option value="">Select Role</option>
                            <option value="User">User</option>
                            <option value="Manager">Manager</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="TASK-BUTTONS">
                <button type="submit" id="create-user-button" name="create_auth0_user" class="CREATE-BUTTON" disabled>Create Auth0 User</button>
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
                                <th>User ID</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user):
                                $metadata = $user['app_metadata'] ?? [];
                                $role = $metadata['role'] ?? 'User';
                                $uid = $user['user_id'] ?? $user['sub'] ?? 'unknown';
                                $email = $user['email'] ?? 'unknown';
                            ?>
                                <form method="post">
                                    <tr>
                                        <td>
                                            <div class="INPUT-GROUP-2">
                                                <input type="text" name="username" value="<?php echo htmlspecialchars($uid); ?>" required readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="INPUT-GROUP-2">
                                                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required readonly>
                                            </div>
                                            <div class="INPUT-GROUP-2">
                                                <input type="email" name="email" value="<?php echo htmlspecialchars($role); ?>" required readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="INPUT-GROUP-2">
                                                <select name="role" class="DROPDOWN-GROUP-3" disabled>
                                                    <option value="User" <?php if ($role === 'User') echo 'selected'; ?>>User</option>
                                                    <option value="Admin" <?php if ($role === 'Admin') echo 'selected'; ?>>Admin</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                            <div class="INPUT-INLINE">
                                                <p class="PASSWORD-BADGE">Password managed by Auth0</p>
                                                <div class="ACTION-DROPDOWN">
                                                    <button type="button" class="ACTION-DROPDOWN-TOGGLE">⋮</button>

                                                    <div class="ACTION-DROPDOWN-MENU">
                                                        <button class="ACTION-DROPDOWN-ITEM" disabled>Actions:</button>
                                                        <?php if ($uid === ($_SESSION['user']['sub'] ?? $_SESSION['user']['user_id'])): ?>
                                                            <button type="submit" disabled>You cannot change yourself</button>
                                                        <?php else: ?>
                                                            <button type="submit">Update</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </form>
                        </tbody>
                    </table>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- USER TABLE END -->
    </div>
</div>
<!-- ADMIN SECTION END -->