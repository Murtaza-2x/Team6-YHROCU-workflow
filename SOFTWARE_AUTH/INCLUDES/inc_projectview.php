<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/taskview_styles.css" rel="stylesheet">
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<div class='VIEW-TASK-CONTAINER'>
    <div class='VIEW-PROJECT-BOX'>
        <div class='VIEW-HEAD'>
            <h1>View Project</h1>
            <p>See Project Details below</p>
        </div>

        <!-- HEADER -->
        <div class="VIEW-ROW">
            <div class="VIEW-COLUMN">
                <h1 class="TASK-LABEL">Project Title</h1>
                <div class='INPUT-GROUP'>
                    <input type="text" value="<?php echo htmlspecialchars($project['project_name']); ?>" disabled />
                </div>
            </div>
        </div>
        <!-- HEADER END -->

        <!-- STATUS -->
        <div class="VIEW-ROW">
            <div class="VIEW-COLUMN">
                <h1 class="TASK-LABEL">Status</h1>
                <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                        <button class="PILL-NEW <?php echo ($project['status'] === 'New') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">New</button>
                        <button class="PILL-IN-PROGRESS <?php echo ($project['status'] === 'In Progress') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">In Progress</button>
                        <button class="PILL-COMPLETE <?php echo ($project['status'] === 'Complete') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Complete</button>
                    </div>
                </div>
            </div>
            <!-- STATUS END -->

            <!-- PRIORITY -->
            <div class="VIEW-COLUMN">
                <h1 class="TASK-LABEL">Priority</h1>
                <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                        <button class="PILL-URGENT <?php echo ($project['priority'] === 'Urgent') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Urgent</button>
                        <button class="PILL-MODERATE <?php echo ($project['priority'] === 'Moderate') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Moderate</button>
                        <button class="PILL-LOW <?php echo ($project['priority'] === 'Low') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Low</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- PRIORITY END -->

        <!-- DESCRIPTION -->
        <div class="VIEW-ROW">
            <label for="DESCRIPTION" class="TASK-LABEL DESCRIPTION-LABEL">
                Description
                <img class="INFO-ICON" src="ICONS/info.png" /></img>
            </label>
            <textarea
                id="description"
                name="description"
                class="TASK-TEXT-AREA"
                rows="6"
                readonly><?php echo htmlspecialchars($project['description']); ?></textarea>
        </div>
        <!-- DESCRIPTION END -->

        <!-- ASSIGNED -->
        <div class="TASK-ROW ASSIGNED-ROW">
            <div class="ASSIGNED-INFO">
                <span class="ASSIGNED-LABEL">Assigned:</span>
                <span class="ASSIGNED-LABEL-2">
                    <?php
                    // displays user nicknames from Auth0
                    $displayNames = array_map(function ($uid) use ($user_map) {
                        return $user_map[$uid] ?? $uid;
                    }, $assignedUsers);
                    echo htmlspecialchars(implode(', ', $displayNames)) ?: 'None';
                    ?>
                </span>
            </div>
        </div>
        <!-- ASSIGNED END -->

        <!-- BUTTONS -->
        <div class="TASK-BUTTONS">
            <?php if (has_role('Admin')): ?>
                <button class="UPDATE-BUTTON" onclick="window.location.href='edit-project-page.php?id=<?php echo urlencode($projectId); ?>'">Update Project</button>
            <?php endif; ?>
            <button class="CANCEL-BUTTON" onclick="window.location.href='list-task-page.php'">Cancel</button>
            <?php if (is_admin()): ?>
                <button class="VIEW-LOGS-BUTTON" onclick="window.location.href='view-project-logs-page.php?id=<?php echo urlencode($projectId); ?>'">
                    View Project Logs
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>