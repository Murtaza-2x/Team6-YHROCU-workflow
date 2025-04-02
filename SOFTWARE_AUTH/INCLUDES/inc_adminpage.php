<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/admin_styles.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="JS/SEARCH-USERS.js"></script>
    <script src="JS/ADMIN-ACTIONS.js"></script>
    <script src="JS/TOGGLE-DROPDOWN.js"></script>
    <script src="JS/PASSWORD-STRENGTH.js"></script>
    <script src="JS/RESET-LINK-COPY.js"></script>
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<div class="ADMIN-CONTAINER">
    <div class="ADMIN-BOX">
        <!-- ADMIN HEADER -->
        <div class="ADMIN-HEAD">
            <h1>User Management</h1>
            <p>Manage Users below</p>
        </div>
        <!-- ADMIN HEADER END -->

        <?php if (!empty($errorMsg)) : ?>
            <div class="LOGIN-ERROR-MESSAGE"><?php echo $errorMsg; ?></div>
        <?php elseif (!empty($successMsg)) : ?>
            <div class="LOGIN-SUCCESS-MESSAGE"><?php echo $successMsg; ?></div>
        <?php endif; ?>

        <!-- CREATE USER FORM -->
        <form method="post">
            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL"><label for="new_email">Email:</label></div>
                <div class="INPUT-WRAPPER">
                    <div class="INPUT-GROUP">
                        <input type="email" id="new_email" name="new_email" placeholder="New Email" required>
                    </div>
                </div>
            </div>

            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL"><label for="password">Password:</label></div>
                <div class="INPUT-WRAPPER">
                    <div class="INPUT-GROUP">
                        <input type="text" id="password" name="new_password" placeholder="Password" oninput="updateStrength()" required>
                    </div>
                    <div id="STRENGTH-BAR">
                        <div id="STRENGTH-FILL"></div>
                    </div>
                    <small id="STRENGTH-FEEDBACK">Password Strength</small>
                </div>
            </div>

            <div class="ADMIN-ROW">
                <div class="ADMIN-LABEL"><label for="new_role">Role:</label></div>
                <div class="INPUT-WRAPPER">
                    <div class="INPUT-GROUP">
                        <select id="new_role" class="DROPDOWN-GROUP" name="new_role" required>
                            <?php foreach ($allowed_roles as $roleOption): ?>
                                <option value="<?php echo $roleOption; ?>" <?php if ($roleOption === 'User') echo 'selected'; ?>>
                                    <?php echo $roleOption; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="TASK-BUTTONS">
                <button class="CREATE-BUTTON" type="submit" name="create_user">Create User</button>
            </div>
        </form>
        <!-- CREATE USER FORM END -->

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
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auth0_users as $user):
                                $metadata = $user['app_metadata'] ?? [];
                                $role = $metadata['role'] ?? 'User';
                                $status = $metadata['status'] ?? 'active';
                                $uid = $user['user_id'] ?? $user['sub'] ?? 'unknown';
                                $email = $user['email'] ?? 'unknown';
                                $currentAdminId = $_SESSION['user']['user_id'] ?? null;
                                $userId = $user['user_id'] ?? $user['sub'] ?? 'unknown';
                                $isSelf = ($userId === $currentAdminId);
                            ?>
                                <?php if (!$isSelf): ?>
                                    <form method="post">
                                        <tr class="<?= $status === 'inactive' ? 'disabled-row' : '' ?>">
                                            <td>
                                                <div class="INPUT-GROUP-2"><input type="text" value="<?php echo htmlspecialchars($userId); ?>" readonly></div>
                                            </td>
                                            <td>
                                                <div class="INPUT-GROUP-2"><input type="email" value="<?php echo htmlspecialchars($email); ?>" readonly></div>
                                            </td>
                                            <td>
                                                <div class="INPUT-GROUP-2">
                                                    <select name="role_change[<?php echo $userId; ?>]" class="DROPDOWN-GROUP-3">
                                                        <?php foreach ($allowed_roles as $roleOption): ?>
                                                            <option value="<?php echo $roleOption; ?>" <?php if ($role === $roleOption) echo 'selected'; ?>>
                                                                <?php echo $roleOption; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo ucfirst($status); ?>
                                            </td>
                                            <td>
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
                                                <div class="INPUT-INLINE">
                                                    <div class="ACTION-DROPDOWN">
                                                        <button type="button" class="ACTION-DROPDOWN-TOGGLE">⋮</button>
                                                        <div class="ACTION-DROPDOWN-MENU">
                                                            <button class="ACTION-DROPDOWN-ITEM" disabled>Actions:</button>
                                                            <button class="ACTION-DROPDOWN-ITEM" type="submit" name="change_role" value="<?php echo htmlspecialchars($userId); ?>">Update Role</button>
                                                            <button class="ACTION-DROPDOWN-ITEM" type="submit" name="reset_password" value="<?php echo htmlspecialchars($userId); ?>">Reset Password</button>
                                                            <?php if ($status === 'inactive'): ?>
                                                                <button class="ACTION-DROPDOWN-ITEM" type="submit" name="reenable_user" value="<?php echo htmlspecialchars($userId); ?>">Enable</button>
                                                            <?php else: ?>
                                                                <button class="ACTION-DROPDOWN-ITEM" type="submit" name="disable_user" value="<?php echo htmlspecialchars($userId); ?>">Disable</button>
                                                            <?php endif; ?>
                                                            <button class="ACTION-DROPDOWN-ITEM" type="submit" name="delete_user" value="<?php echo htmlspecialchars($userId); ?>">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </form>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="opacity:0.3;">[<?php echo htmlspecialchars($email); ?>] - You cannot edit yourself</td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- USER TABLE END -->
    </div>
</div>
<!-- ADMIN PANEL END -->