<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/taskcreate_styles.css" rel="stylesheet">
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- CREATE PROJECT FORM SECTION -->
<div class="CREATE-TASK-CONTAINER">
    <div class="CREATE-TASK-BOX">
        <h1>Create a New Project</h1>
        <p>Enter project details below</p>

        <?php if (!empty($errorMsg)) : ?>
            <div class="ERROR-MESSAGE">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>

        <form id="CREATE-PROJECT-FORM" action="create-project-page.php" method="post">

            <!-- ERROR/SUCCESS MESSAGES -->
            <?php if (!empty($errorMsg)) : ?>
                <div class="ERROR-MESSAGE"><?php echo htmlspecialchars($errorMsg); ?></div>
            <?php elseif (!empty($successMsg)) : ?>
                <div class="SUCCESS-MESSAGE"><?php echo htmlspecialchars($successMsg); ?></div>
            <?php endif; ?>

            <!-- PROJECT NAME & DUE DATE -->
            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <div class='INPUT-GROUP'>
                        <input type="text" name="project_name" placeholder="Project Name" required>
                    </div>
                </div>

                <div class="VIEW-COLUMN">
                    <label class="DESCRIPTION-LABEL">Due Date</label>
                    <div class='INPUT-GROUP'>
                        <input type="date" name="due_date" required>
                    </div>
                </div>
            </div>
            <!-- PROJECT NAME & DUE DATE END -->

            <!-- STATUS -->
            <div class="INPUT-GROUP">
                <select class="DROPDOWN-GROUP" id="status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="New">New</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Complete">Complete</option>
                </select>
            </div>
            <!-- STATUS END -->

            <!-- PRIORITY -->
            <div class="INPUT-GROUP">
                <select class="DROPDOWN-GROUP" id="priority" name="priority" required>
                    <option value="">Select Priority</option>
                    <option value="Low">Low</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>
            <!-- PRIORITY END -->

            <!-- DESCRIPTION -->
            <div class="DESC-GROUP">
                <label for="description" class="DESCRIPTION-LABEL">Description:</label>
                <textarea id="description" name="description" class="TASK-TEXT-AREA" rows="6" required></textarea>
            </div>
            <!-- DESCRIPTION END -->

            <!-- BUTTONS -->
            <div class="TASK-BUTTONS">
                <button class="CREATE-BUTTON" type="submit">Create Project</button>
                <button class="CANCEL-BUTTON" type="button" onclick="window.location.href='list-task-page.php'">Cancel</button>
            </div>
            <!-- BUTTONS END -->

        </form>
    </div>
</div>
<!-- CREATE PROJECT FORM SECTION END -->
