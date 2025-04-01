<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/pill_styles.css" rel="stylesheet">
    <link href="CSS/dropdown_styles.css" rel="stylesheet">
    <link href="CSS/taskview_styles.css" rel="stylesheet">
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- PROJECT VIEW -->
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
                    <input
                        type='text'
                        id='project-title'
                        name='project-title'
                        value="<?php echo htmlspecialchars($projectName); ?>"
                        placeholder='Project Title' disabled />
                </div>
            </div>
        </div>
        <!-- HEADER END -->

        <!-- PRIORITY & STATUS -->
        <div class="VIEW-ROW">
            <div class="VIEW-COLUMN">
                <div class="TASK-LABEL">Status</div>
                <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                        <button class="PILL-NEW <?php echo ($status === 'New')         ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">New</button>
                        <button class="PILL-IN-PROGRESS <?php echo ($status === 'In Progress') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">In Progress</button>
                        <button class="PILL-COMPLETE <?php echo ($status === 'Complete')    ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Complete</button>
                    </div>
                </div>
            </div>

            <div class="VIEW-COLUMN">
                <div class="TASK-LABEL">Priority</div>
                <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                        <button class="PILL-URGENT   <?php echo ($priority === 'Urgent')   ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Urgent</button>
                        <button class="PILL-MODERATE <?php echo ($priority === 'Moderate') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Moderate</button>
                        <button class="PILL-LOW      <?php echo ($priority === 'Low')       ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Low</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- PRIORITY & STATUS END -->

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
                readonly><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <!-- ASSIGNED -->
        <div class="TASK-ROW ASSIGNED-ROW">
            <div class="ASSIGNED-INFO">
                <span class="ASSIGNED-LABEL">Assigned:</span>
                <span class="ASSIGNED-LABEL-2">
                    <?php echo htmlspecialchars($assignedUsers) ?: 'None'; ?>
                </span>
            </div>
        </div>
        <!-- ASSIGNED END -->

        <!-- BUTTONS -->
        <div class="TASK-BUTTONS">

            <?php if ($_SESSION["clearance"] != 'User'): ?>
                <button class="UPDATE-BUTTON"
                    onclick="window.location.href='edit-project-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($id); ?>'">
                    Update Project
                </button>
            <?php endif; ?>

            <button
                class="CANCEL-BUTTON"
                onclick="window.location.href='list-task-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($_SESSION['id']); ?>'">
                Cancel
            </button>

            <?php if ($_SESSION["clearance"] != 'User'): ?>
                <button class="VIEW-LOGS-BUTTON"
                    onclick="window.location.href='view-project-logs-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($project_id); ?>'">
                    View Logs
                </button>
            <?php endif; ?>

        </div>
        <!-- BUTTONS END -->
    </div>
</div>
<!-- PROJECT VIEW END -->