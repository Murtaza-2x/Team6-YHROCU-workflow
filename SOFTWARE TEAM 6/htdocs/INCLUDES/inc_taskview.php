<?php
$clearance = $_SESSION["clearance"];
?>

<head>
    <link href="CSS/taskview_styles.css" rel="stylesheet">
</head>

<!-- MIDDLE SECTION -->
<div class='MIDDLE-SECTION'>
    <p class="MIDDLE-HERO-IMAGE"></p>

    <!-- TASK VIEW -->
    <div class='VIEW-TASK-CONTAINER'>
        <div class='VIEW-TASK-BOX'>
            <h1>View Task</h1>
            <p>See Task Details below</p>

            <!-- HEADER -->
            <div class="VIEW-ROW">
                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">Task Title</h1>
                    <div class='INPUT-GROUP'>
                        <input
                            type='text'
                            id='task-title'
                            name='task-title'
                            placeholder='Task Title Example' disabled />
                    </div>
                </div>

                <div class="VIEW-COLUMN">
                    <h1 class="TASK-LABEL">
                        Project Allocation
                    </h1>
                    <h2 class="TASK-LABEL">
                        (where the task is assigned)
                    </h2>
                    <div class='INPUT-GROUP'>
                        <input
                            type='text'
                            id='task-title'
                            name='task-title'
                            placeholder='Project Example' disabled />
                        <!-- <select class='DROPDOWN-GROUP' id="project-allocation" name="project-allocation" required>
                        <option value="">Project Example</option>
                    </select> -->
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
                            <button class="PILL-STATUS1">Status 1</button>
                            <button class="PILL-STATUS2">Status 2</button>
                            <button class="PILL-STATUS3">Status 3</button>
                        </div>
                    </div>
                </div>

                <div class="VIEW-COLUMN">
                    <div class="TASK-LABEL">Priority</div>
                    <div class="TASK-PILL-CONTAINER">
                        <div class="PILL">
                            <button class="PILL-URGENT">Urgent</button>
                            <button class="PILL-MODERATE">Moderate</button>
                            <button class="PILL-LOW">Low</button>
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
                    id="DESCRIPTION" class="TASK-TEXT-AREA" rows="6"
                    readonly>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </textarea>
            </div>

            <!-- ASSIGNED + LOGS -->
            <div class="TASK-ROW ASSIGNED-ROW">
                <div class="ASSIGNED-INFO">
                    <span class="ASSIGNED-LABEL">Assigned:</span>
                    <span class="ASSIGNED-LABEL-2">John Smith, Jane Smith...</span>
                    <span class="ADD-MORE-STAFF">+ Add more Staff</span>
                </div>
            </div>
            <!-- ASSIGNED + LOGS END -->

            <!-- BUTTONS -->
            <div class="TASK-BUTTONS">
                <button class="UPDATE-BUTTON">Update Task</button>
                <button class="CANCEL-BUTTON">Cancel</button>
                <button class="VIEW-LOGS-BUTTON">View Logs</button>
            </div>
            <!-- BUTTONS END -->
        </div>
    </div>
    <!-- TASK VIEW END -->
</div>
<!-- MIDDLE SECTION END -->