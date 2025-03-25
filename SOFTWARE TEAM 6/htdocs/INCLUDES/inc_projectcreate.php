<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/pill_styles.css" rel="stylesheet">
    <link href="CSS/dropdown_styles.css" rel="stylesheet">
    <link href="CSS/taskcreate_styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="JS/SELECT-MULTI-DROPDOWN.js"></script>

</head>


<p class="MIDDLE-HERO-IMAGE"></p>

<div class="CREATE-TASK-CONTAINER">
    <div class="CREATE-TASK-BOX">
        <h1>Create a New Project</h1>
        <p>Enter project details below</p>

        <form id="CREATE-PROJECT-FORM" action="create-project-page.php" method="post">

            <!-- SUBJECT -->
            <div class="INPUT-GROUP">
                <input type="text" id="project_name" name="project_name" placeholder="Project Title" required />
            </div>
            <!-- SUBJECT END -->

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