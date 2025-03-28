<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/pill_styles.css" rel="stylesheet">
    <link href="CSS/dropdown_styles.css" rel="stylesheet">
    <link href="CSS/taskview_styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="JS/SELECT-MULTI-DROPDOWN.js"></script>
    <script src="JS/SELECT-STATUS.js"></script>
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- EDIT TASK FORM -->
<form action="edit-project-page.php?id=<?php echo $id; ?>" method="post">
    <div class='VIEW-TASK-CONTAINER'>
        <div class='VIEW-PROJECT-BOX'>
            <div class='VIEW-HEAD'>
                <h1>Edit Project</h1>
                <p>Edit Project Details below</p>
            </div>

            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">Project Title</h1>
                    <div class='INPUT-GROUP'>
                        <input type="text" id="project-title" name="project_name"
                            value="<?php echo htmlspecialchars($projectName); ?>"
                            placeholder="Project Title" required />
                    </div>
                </div>
            </div>
            <!-- HEADER END -->

            <!-- PRIORITY & STATUS -->
            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <div class="TASK-LABEL">Status</div>
                    <div class="TASK-PILL-CONTAINER" id="status-container">
                        <div class="PILL">
                            <input type="hidden" name="status" id="status-input" value="<?php echo htmlspecialchars($status); ?>" required />
                            <button type="button" class="PILL-NEW <?php echo ($status === 'New') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>"
                                onclick="selectStatus('New')">New</button>
                            <button type="button" class="PILL-IN-PROGRESS <?php echo ($status === 'In Progress') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>"
                                onclick="selectStatus('In Progress')">In Progress</button>
                            <button type="button" class="PILL-COMPLETE <?php echo ($status === 'Complete') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>"
                                onclick="selectStatus('Complete')">Complete</button>
                        </div>
                    </div>
                </div>

                <div class="VIEW-COLUMN">
                    <div class="TASK-LABEL">Priority</div>
                    <div class="TASK-PILL-CONTAINER" id="priority-container">
                        <div class="PILL">
                            <input type="hidden" name="priority" id="priority-input" value="<?php echo htmlspecialchars($priority); ?>" required />
                            <button type="button" class="PILL-URGENT <?php echo ($priority === 'Urgent') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>"
                                onclick="selectPriority('Urgent')">Urgent</button>
                            <button type="button" class="PILL-MODERATE <?php echo ($priority === 'Moderate') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>"
                                onclick="selectPriority('Moderate')">Moderate</button>
                            <button type="button" class="PILL-LOW <?php echo ($priority === 'Low') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>"
                                onclick="selectPriority('Low')">Low</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- PRIORITY & STATUS END -->

            <!-- DESCRIPTION -->
            <div class="VIEW-ROW">
                <label for="description" class="TASK-LABEL DESCRIPTION-LABEL">
                    Description
                    <img class="INFO-ICON" src="ICONS/info.png" alt="Info" />
                </label>
                <textarea id="description" name="description" class="TASK-TEXT-AREA" rows="6" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <!-- BUTTONS -->
            <div class="TASK-BUTTONS">
                <button class="UPDATE-BUTTON" type="submit">Update Project</button>
                <button class="CANCEL-BUTTON" type="button" onclick="window.location.href='view-project-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($id); ?>'">Cancel</button>
            </div>
            <!-- BUTTONS END -->
        </div>
    </div>
</form>
<!-- EDIT PROJECT FORM END -->