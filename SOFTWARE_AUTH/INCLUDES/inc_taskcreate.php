<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/taskcreate_styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="JS/SELECT-MULTI-DROPDOWN.js"></script>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- CREATE TASK SECTION -->
<div class="CREATE-TASK-CONTAINER">
    <div class="CREATE-TASK-BOX">
        <h1>Create a New Task</h1>
        <p>Enter task details below</p>

        <?php if (!empty($errorMsg)) : ?>
            <div class="ERROR-MESSAGE">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>

        <form id="CREATE-TASK-FORM" action="create-task-page.php" method="post">

            <!-- TASK SUBJECT -->
            <div class="INPUT-GROUP">
                <input type="text" id="subject" name="subject" placeholder="Subject" required />
            </div>
            <!-- TASK SUBJECT END -->

            <!-- PROJECT SELECTION -->
            <div class="INPUT-GROUP">
                <select class="DROPDOWN-GROUP" id="project_id" name="project_id" required>
                    <option value="">Select Project</option>
                    <?php foreach ($projects as $proj): ?>
                        <option value="<?php echo $proj['id']; ?>"><?php echo htmlspecialchars($proj['project_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- PROJECT SELECTION END -->

            <!-- ASSIGNED USERS -->
            <div class="INPUT-GROUP">
                <select class="DROPDOWN-GROUP-2" id="assign" name="assign[]" multiple required>
                    <?php foreach ($auth0_users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['user_id']); ?>">
                            <?php echo htmlspecialchars($user['nickname'] ?? $user['email']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- ASSIGNED USERS END -->

            <!-- TASK STATUS -->
            <div class="INPUT-GROUP">
                <select class="DROPDOWN-GROUP" id="status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="New">New</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Complete">Complete</option>
                </select>
            </div>
            <!-- TASK STATUS END -->

            <!-- TASK PRIORITY -->
            <div class="INPUT-GROUP">
                <select class="DROPDOWN-GROUP" id="priority" name="priority" required>
                    <option value="">Select Priority</option>
                    <option value="Low">Low</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>
            <!-- TASK PRIORITY END -->

            <!-- TASK DESCRIPTION -->
            <div class="DESC-GROUP">
                <label for="description" class="DESCRIPTION-LABEL">Description:</label>
                <textarea id="description" name="description" class="TASK-TEXT-AREA" rows="6" required></textarea>
            </div>
            <!-- TASK DESCRIPTION END -->

            <!-- BUTTONS -->
            <div class="TASK-BUTTONS">
                <button class="CREATE-BUTTON" type="submit">Create Task</button>
                <button class="CANCEL-BUTTON" type="button" onclick="window.location.href='list-task-page.php'">Cancel</button>
            </div>
            <!-- BUTTONS END -->
        </form>
    </div>
</div>
<!-- CREATE TASK SECTION END -->
