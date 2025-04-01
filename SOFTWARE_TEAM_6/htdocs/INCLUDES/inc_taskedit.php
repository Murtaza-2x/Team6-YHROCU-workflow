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

<form action="edit-task-page.php?id=<?php echo $taskId; ?>" method="post">

    <div class='VIEW-TASK-CONTAINER'>
        <div class='VIEW-PROJECT-BOX'>
            <div class='VIEW-HEAD'>
                <h1>Edit Task</h1>
                <p>Edit task details below</p>
            </div>

            <?php if (!empty($errorMsg)): ?>
                <div class="LOGIN-ERROR-MESSAGE"><?php echo htmlspecialchars($errorMsg); ?></div>
            <?php elseif (!empty($successMsg)): ?>
                <div class="LOGIN-SUCCESS-MESSAGE"><?php echo htmlspecialchars($successMsg); ?></div>
            <?php endif; ?>

            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">Task Title</h1>
                    <div class='INPUT-GROUP'>
                        <input type="text" name="subject" value="<?php echo htmlspecialchars($subject); ?>" placeholder="Task Title" required />
                    </div>
                </div>

                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">Project Allocation</h1>
                    <h2 class="TASK-LABEL">(where the task is assigned)</h2>
                    <div class='INPUT-GROUP'>
                        <select class="DROPDOWN-GROUP" name="project_id" required>
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?php echo $p['id']; ?>" <?php if ($p['id'] == $project_id) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($p['project_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <div class="TASK-LABEL">Status</div>
                    <div class="TASK-PILL-CONTAINER" id="status-container">
                        <input type="hidden" name="status" id="status-input" value="<?php echo htmlspecialchars($status); ?>" required />
                        <div class="PILL">
                            <button type="button" class="PILL-NEW <?php echo ($status === 'New') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('New')">New</button>
                            <button type="button" class="PILL-IN-PROGRESS <?php echo ($status === 'In Progress') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('In Progress')">In Progress</button>
                            <button type="button" class="PILL-COMPLETE <?php echo ($status === 'Complete') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectStatus('Complete')">Complete</button>
                        </div>
                    </div>
                </div>

                <div class="VIEW-COLUMN">
                    <div class="TASK-LABEL">Priority</div>
                    <div class="TASK-PILL-CONTAINER" id="priority-container">
                        <input type="hidden" name="priority" id="priority-input" value="<?php echo htmlspecialchars($priority); ?>" required />
                        <div class="PILL">
                            <button type="button" class="PILL-URGENT <?php echo ($priority === 'Urgent') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Urgent')">Urgent</button>
                            <button type="button" class="PILL-MODERATE <?php echo ($priority === 'Moderate') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Moderate')">Moderate</button>
                            <button type="button" class="PILL-LOW <?php echo ($priority === 'Low') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>" onclick="selectPriority('Low')">Low</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="VIEW-ROW">
                <label class="TASK-LABEL DESCRIPTION-LABEL">Description</label>
                <textarea class="TASK-TEXT-AREA" name="description" rows="6" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <div class="TASK-LABEL">Assign Users</div>
                    <div class="INPUT-GROUP">
                        <select class="DROPDOWN-GROUP" id="assign" name="assign[]" multiple>
                            <?php foreach ($auth0_users as $u):
                                $uid = $u['user_id'];
                                $nickname = htmlspecialchars($u['nickname'] ?? $u['email']);
                                $selected = in_array($uid, $assignedUsers) ? "selected" : "";
                            ?>
                                <option value="<?php echo $uid; ?>" <?php echo $selected; ?>><?php echo $nickname; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="TASK-BUTTONS">
                <button class="UPDATE-BUTTON" type="submit" name="update_task">Update Task</button>
                <button class="CANCEL-BUTTON" type="button" onclick="window.location.href='view-task-page.php?id=<?php echo urlencode($taskId); ?>'">Cancel</button>
            </div>

        </div>
    </div>
</form>