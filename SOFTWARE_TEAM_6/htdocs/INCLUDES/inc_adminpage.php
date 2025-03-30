<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/pill_styles.css" rel="stylesheet">
    <link href="CSS/dropdown_styles.css" rel="stylesheet">
    <link href="CSS/admin_styles.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="JS/SEARCH-USERS.js"></script>
    <script src="JS/ADMIN-ACTIONS.js"></script>
    <script src="JS/TOGGLE-DROPDOWN.js"></script>

</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- ADMIN SECTION -->
<div class='ADMIN-CONTAINER'>
    <div class='ADMIN-BOX'>
        <div class='ADMIN-HEAD'>
            <h1>User Management</h1>
            <p>Manage Users below</p>
        </div>

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