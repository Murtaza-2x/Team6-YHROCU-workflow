<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/taskview_styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="JS/SELECT-MULTI-DROPDOWN.js"></script>
    <script src="JS/SELECT-STATUS.js"></script>
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- PROJECT EDIT FORM SECTION -->
<div class="VIEW-TASK-CONTAINER">
    <div class="VIEW-PROJECT-BOX">

        <!-- HEADER -->
        <div class="VIEW-HEAD">
            <h1>Edit Project</h1>
            <p>Edit project details below</p>
        </div>
        <!-- HEADER END -->

        <form method="post">

            <!-- PROJECT NAME -->
            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">Project Name</h1>
                    <div class='INPUT-GROUP'>
                        <input type="text" name="project_name" value="<?php echo htmlspecialchars($project['project_name']); ?>" required>
                    </div>
                </div>
            </div>
            <!-- PROJECT NAME END -->

            <!-- STATUS & PRIORITY -->
            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">Status</h1>
                    <div class="TASK-PILL-CONTAINER" id="status-container">
                        <input type="hidden" name="status" id="status-input" value="<?php echo htmlspecialchars($project['status']); ?>" required />
                        <div class="PILL">
                            <button type="button" class="PILL-NEW <?php echo ($project['status'] === 'New') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('New')">New</button>
                            <button type="button" class="PILL-IN-PROGRESS <?php echo ($project['status'] === 'In Progress') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('In Progress')">In Progress</button>
                            <button type="button" class="PILL-COMPLETE <?php echo ($project['status'] === 'Complete') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('Complete')">Complete</button>
                        </div>
                    </div>
                </div>

                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">Priority</h1>
                    <div class="TASK-PILL-CONTAINER" id="priority-container">
                        <input type="hidden" name="priority" id="priority-input" value="<?php echo htmlspecialchars($project['priority']); ?>" required />
                        <div class="PILL">
                            <button type="button" class="PILL-URGENT <?php echo ($project['priority'] === 'Urgent') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Urgent')">Urgent</button>
                            <button type="button" class="PILL-MODERATE <?php echo ($project['priority'] === 'Moderate') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Moderate')">Moderate</button>
                            <button type="button" class="PILL-LOW <?php echo ($project['priority'] === 'Low') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Low')">Low</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- STATUS & PRIORITY END -->

            <!-- DESCRIPTION -->
            <div class="VIEW-ROW">
                <label class="TASK-LABEL DESCRIPTION-LABEL">Description:</label>
                <textarea class="TASK-TEXT-AREA" name="description" rows="6" required><?php echo htmlspecialchars($project['description']); ?></textarea>
            </div>
            <!-- DESCRIPTION END -->

            <!-- ASSIGNED USERS -->
            <div class="TASK-ROW ASSIGNED-ROW">
                <div class="ASSIGNED-INFO">
                    <span class="ASSIGNED-LABEL">Assigned Users:</span>
                    <span class="ASSIGNED-LABEL-2">
                        <?php
                        $displayNames = array_map(fn($uid) => $user_map[$uid] ?? $uid, $assignedUsers);
                        echo htmlspecialchars(implode(', ', $displayNames)) ?: 'None';
                        ?>
                    </span>
                </div>
            </div>
            <!-- ASSIGNED USERS END -->

            <!-- DUE DATE -->
            <div class="TASK-ROW ASSIGNED-ROW">
                <div class="ASSIGNED-INFO">
                    <span class="ASSIGNED-LABEL">Due Date</span>
                    <div class='INPUT-GROUP-2'>
                        <input type="date" name="due_date" value="<?php echo htmlspecialchars($due_date); ?>" required>
                    </div>
                </div>
            </div>
            <!-- DUE DATE END -->

            <!-- BUTTONS -->
            <div class="TASK-BUTTONS">
                <button class="UPDATE-BUTTON" type="submit" name="update_project">Update Project</button>
                <button class="CANCEL-BUTTON" type="button" onclick="window.location.href='view-project-page.php?id=<?php echo urlencode($projectId); ?>'">Cancel</button>
            </div>
            <!-- BUTTONS END -->

        </form>
    </div>
</div>
<!-- PROJECT EDIT FORM SECTION END -->