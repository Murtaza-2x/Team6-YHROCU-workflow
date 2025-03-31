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

        <?php
if (isset($_POST['create_auth0_user'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $clearance = $_POST['clearance'];

    try {
        $auth0 = new Auth0Manager();
        $api_result = $auth0->createUser($email, $password, ['clearance' => $clearance]);

        if ($api_result === true) {
            echo "<div class='SUCCESS-MESSAGE'>✅ User successfully created in Auth0!</div>";
        } elseif (is_array($api_result) && isset($api_result['status'])) {
            // cleaner error formatting
            echo "<div class='ERROR-MESSAGE'> Error {$api_result['code']}: {$api_result['message']}</div>";
        } else {
            echo "<div class='ERROR-MESSAGE'>An unexpected error occurred.</div>";
        }
    } catch (Exception $e) {
        echo "<div class='ERROR-MESSAGE'> Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
        ?>

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
                                                <p class="PASSWORD-BADGE">Password managed by Auth0</p>
                                                <div class="ACTION-DROPDOWN">
                                                    <button type="button" class="ACTION-DROPDOWN-TOGGLE">⋮</button>

                                                    <div class="ACTION-DROPDOWN-MENU">
                                                        <button class="ACTION-DROPDOWN-ITEM" disabled>Actions:</button>

                                                        <?php if ($user['id'] != $_SESSION['id']): ?>
                                                            <input type="hidden" name="current_status" value="<?= $user['status'] ?>">
                                                            <button type="submit" name="toggle_user" class="ACTION-DROPDOWN-ITEM">
                                                                <?= $user['status'] === 'Active' ? 'Disable' : 'Re-enable' ?>
                                                            </button>
                                                            <button type="submit" name="delete_user" class="ACTION-DROPDOWN-ITEM" onclick="return confirm('Delete this user?');">Delete</button>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
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