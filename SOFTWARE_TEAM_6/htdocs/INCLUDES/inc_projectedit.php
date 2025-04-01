<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/taskview_styles.css" rel="stylesheet">
    <script src="JS/SELECT-STATUS.js"></script>
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- EDIT PROJECT FORM -->
<form action="edit-project-page.php?id=<?php echo urlencode($projectId); ?>" method="post">
    <div class="VIEW-TASK-CONTAINER">
        <div class="VIEW-PROJECT-BOX">

            <div class="VIEW-HEAD">
                <h1>Edit Project</h1>
                <p>Modify project details below</p>
            </div>

            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">Project Name</h1>
                    <div class="INPUT-GROUP">
                        <input type="text" name="project_name" value="<?php echo htmlspecialchars($projectName); ?>" required />
                    </div>
                </div>
            </div>

            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <div class="TASK-LABEL">Status</div>
                    <div class="TASK-PILL-CONTAINER" id="status-container">
                        <div class="PILL">
                            <input type="hidden" name="status" id="status-input" value="<?php echo htmlspecialchars($status); ?>" required />
                            <button type="button" class="PILL-NEW <?php echo ($status === 'New') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('New')">New</button>
                            <button type="button" class="PILL-IN-PROGRESS <?php echo ($status === 'In Progress') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('In Progress')">In Progress</button>
                            <button type="button" class="PILL-COMPLETE <?php echo ($status === 'Complete') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('Complete')">Complete</button>
                        </div>
                    </div>
                </div>

                <div class="VIEW-COLUMN">
                    <div class="TASK-LABEL">Priority</div>
                    <div class="TASK-PILL-CONTAINER" id="priority-container">
                        <div class="PILL">
                            <input type="hidden" name="priority" id="priority-input" value="<?php echo htmlspecialchars($priority); ?>" required />
                            <button type="button" class="PILL-URGENT <?php echo ($priority === 'Urgent') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Urgent')">Urgent</button>
                            <button type="button" class="PILL-MODERATE <?php echo ($priority === 'Moderate') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Moderate')">Moderate</button>
                            <button type="button" class="PILL-LOW <?php echo ($priority === 'Low') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Low')">Low</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="VIEW-ROW">
                <label class="TASK-LABEL DESCRIPTION-LABEL">Description</label>
                <textarea name="description" class="TASK-TEXT-AREA" rows="6" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

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

            <div class="TASK-BUTTONS">
                <button class="UPDATE-BUTTON" type="submit" name="update_project">Update Project</button>
                <button class="CANCEL-BUTTON" type="button" onclick="window.location.href='view-project-page.php?id=<?php echo urlencode($projectId); ?>'">Cancel</button>
            </div>

        </div>
    </div>
</form>
<!-- EDIT PROJECT FORM END -->
