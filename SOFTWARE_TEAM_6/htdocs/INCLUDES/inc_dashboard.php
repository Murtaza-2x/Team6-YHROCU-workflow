<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/dashboard_styles.css" rel="stylesheet">
    <link href="CSS/tasklist_styles.css" rel="stylesheet">
</head>

<!-- DASHBOARD SECTION -->
<div class="DASH-CONTENT">
    <div class="DASH-HEADER">
        <p class="DASH-HEADER-1">Dashboard -</p>
        <?php

        $user = $_SESSION['user'] ?? [];
        $id = $user['sub'] ?? $user['user_id'] ?? null;
        $clearance = $user['role'] ?? 'User';
        $displayName = htmlspecialchars($user['nickname'] ?? $user['name'] ?? 'User');

        switch ($clearance) {
            case 'Admin':
                $clearanceLabel = 'Admin';
                break;
            case 'Manager':
                $clearanceLabel = 'Manager';
                break;
            case 'User':
            default:
                $clearanceLabel = 'Staff';
                break;
        }

        echo "<p class='DASH-HEADER-2'>Welcome {$clearanceLabel} {$displayName}</p>";

        ?>
    </div>

    <div class="DASH-AREA">
        <div class="DASH-SECTION-CONTAINER">

            <div class="DASH-SECTION-1">
            </div>

        </div>
    </div>
</div>
<!-- DASHBOARD SECTION END -->
