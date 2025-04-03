<?php
/*
-------------------------------------------------------------
File: list-task-page.php
Description:
- Displays the dashboard for tasks and projects.
- Shows tasks for users and admins, filtered by user role.
- Admins can create new tasks and projects.
-------------------------------------------------------------
*/

$title = "ROCU: Dashboard";

require_once __DIR__ . '/INCLUDES/env_loader.php';
require_once __DIR__ . '/INCLUDES/role_helper.php';
require_once __DIR__ . '/INCLUDES/inc_connect.php';
require_once __DIR__ . '/INCLUDES/Auth0UserManager.php'; // Ensure we include Auth0 manager
require_once __DIR__ . '/INCLUDES/inc_header.php';
require_once __DIR__ . '/INCLUDES/inc_dashboard.php';

if (!is_logged_in()) {
    header('Location: index.php?error=1&msg=Please log in first.');
    exit;
}

// Fetch the Auth0 user list for mapping IDs to usernames
$auth0_users = Auth0UserManager::getUsers();

$user      = $_SESSION['user'];
$clearance = $user['role'] ?? 'User';
$userId    = $user['user_id'] ?? null;

if ($clearance === 'User') {
    // For normal users
    $stmt = $conn->prepare("
        SELECT t.id, t.subject, t.project_id, p.project_name, t.status, t.priority, t.created_by
        FROM tasks t
        LEFT JOIN projects p ON t.project_id = p.id
        JOIN task_assigned_users tau ON t.id = tau.task_id
        WHERE tau.user_id = ?
        GROUP BY t.id
    ");
    $stmt->bind_param("s", $userId);
} else {
    // For admins
    $stmt = $conn->prepare("
        SELECT t.id, t.subject, t.project_id, p.project_name, t.status, t.priority, t.created_by
        FROM tasks t
        LEFT JOIN projects p ON t.project_id = p.id
        GROUP BY t.id
    ");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!-- TASK SECTION -->
<div class="TASK-CONTENT">
    <!-- TASK HEADER -->
    <div class="TASK-HEADER">
        <p class="TASK-HEADER-1">Task List</p>
        <p class="TASK-HEADER-2">(<?php echo $result->num_rows; ?>)</p>
    </div>
    <!-- TASK HEADER END -->

    <div class="TASK-AREA">

        <!-- TASK FILTER -->
        <div class="TASK-FILTER">
            <input type="text" id="searchInput" placeholder="Search tasks...">
            <button type="button" id="filterButton">Filter</button>
        </div>
        <!-- TASK FILTER END -->

        <!-- TASK LIST -->
        <div class="TASK-LIST">
            <?php if ($result->num_rows > 0): ?>
                <table class='TASK-TABLE' id='TASK-TABLE'>
                    <thead>
                        <tr>
                            <th>Subject <img src='ICONS/filter-filled.png' class='filter' /></th>
                            <th>Project <img src='ICONS/filter-filled.png' class='filter' /></th>
                            <th>Assignee <img src='ICONS/filter-filled.png' class='filter' /></th>
                            <th>Status <img src='ICONS/filter-filled.png' class='filter' /></th>
                            <th>Priority <img src='ICONS/filter-filled.png' class='filter' /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $activeTasks = [];
                        $completedTasks = [];

                        while ($row = $result->fetch_assoc()) {
                            if (!is_staff() && $row["status"] === 'Complete') {
                                $completedTasks[] = $row;
                            } else {
                                $activeTasks[] = $row;
                            }
                        }

                        foreach (array_merge($activeTasks, $completedTasks) as $row):
                            $isCompleted = !is_staff() && $row["status"] === 'Complete';
                            $rowStyle = $isCompleted ? 'style="opacity:0.5;"' : '';

                            // Status pill
                            switch ($row["status"]) {
                                case 'New':
                                    $statusPill = "<button class='PILL-NEW' id='PILL-ACTIVE'>New</button>";
                                    break;
                                case 'In Progress':
                                    $statusPill = "<button class='PILL-IN-PROGRESS' id='PILL-ACTIVE'>In Progress</button>";
                                    break;
                                case 'Complete':
                                    $statusPill = "<button class='PILL-COMPLETE' id='PILL-ACTIVE'>Complete</button>";
                                    break;
                                default:
                                    $statusPill = "<button class='PILL-INACTIVE'>" . htmlspecialchars($row["status"]) . "</button>";
                                    break;
                            }

                            // Priority pill
                            switch ($row["priority"]) {
                                case 'Urgent':
                                    $priorityPill = "<button class='PILL-URGENT' id='PILL-ACTIVE'>Urgent</button>";
                                    break;
                                case 'Moderate':
                                    $priorityPill = "<button class='PILL-MODERATE' id='PILL-ACTIVE'>Moderate</button>";
                                    break;
                                case 'Low':
                                    $priorityPill = "<button class='PILL-LOW' id='PILL-ACTIVE'>Low</button>";
                                    break;
                                default:
                                    $priorityPill = "<button class='PILL-INACTIVE'>" . htmlspecialchars($row["priority"]) . "</button>";
                                    break;
                            }

                            // Creator
                            $createdById = $row["created_by"];
                            $creatorName = "Unknown";
                            foreach ($auth0_users as $auth0User) {
                                if (isset($auth0User['user_id']) && $auth0User['user_id'] === $createdById) {
                                    $creatorName = $auth0User['name'] ?? $auth0User['nickname'] ?? $auth0User['email'] ?? "Unknown";
                                    break;
                                }
                            }
                        ?>
                            <tr <?php echo $rowStyle; ?>>
                                <td><a href='view-task-page.php?id=<?php echo urlencode($row["id"]); ?>'><?php echo htmlspecialchars($row["subject"]); ?></a></td>
                                <td><a href='view-project-page.php?id=<?php echo urlencode($row["project_id"]); ?>'><?php echo htmlspecialchars($row["project_name"]) ?: "N/A"; ?></a></td>
                                <td><?php echo htmlspecialchars($creatorName); ?></td>
                                <td><?php echo $statusPill; ?></td>
                                <td><?php echo $priorityPill; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h1 class='USER-MESSAGE'>No tasks found.</h1>
            <?php endif; ?>

            <?php if (is_admin()): ?>
                <button class='CREATE-TASK-BUTTON' onclick="location.href='create-task-page.php'">Create Task</button>
                <button class='CREATE-PROJECT-BUTTON' onclick="location.href='create-project-page.php'">Create Project</button>
            <?php endif; ?>
        </div>
        <!-- TASK LIST END -->
    </div>
</div>
<!-- TASK SECTION END -->

<?php include 'INCLUDES/inc_footer.php'; ?>
<?php include 'INCLUDES/inc_disconnect.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="JS/SEARCH-TABLE.js"></script>