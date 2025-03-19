<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/taskcreate_styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="JS/SELECT-MULTI-DROPDOWN.js"></script>

</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<div class='CREATE-TASK-CONTAINER'>
    <div class='CREATE-TASK-BOX'>
        <h1>Create a New Task</h1>
        <p>Enter task details below</p>

        <form id='CREATE-TASK-FORM' action='create-task-page.php' method='post'>
            <!-- SUBJECT -->
            <div class='INPUT-GROUP'>
                <input
                    type='text'
                    id='subject'
                    name='subject'
                    placeholder='Subject'
                    required />
            </div>

            <!-- PROJECT -->
            <div class='INPUT-GROUP'>
                <input
                    type='text'
                    id='project'
                    name='project'
                    placeholder='Project'
                    required />
            </div>

            <!-- ASSIGN USERS -->
            <?php
            // Query the DB for all users
            $sql_users = "SELECT id, username FROM users ORDER BY username";
            $result_users = $conn->query($sql_users);
            ?>

            <div class='INPUT-GROUP'>
                <select class='DROPDOWN-GROUP' id="assign" name="assign[]" multiple required>
                    <option disabled>Select one or more users</option>

                    <?php
                    if ($result_users && $result_users->num_rows > 0) {
                        while ($row = $result_users->fetch_assoc()) {
                            $id = $row['id'];
                            $username = htmlspecialchars($row['username']);
                            echo "<option value='{$id}'>{$username}</option>";
                        }
                    }
                    ?>

                </select>
            </div>

            <!-- STATUS -->
            <div class='INPUT-GROUP'>
                <select class='DROPDOWN-GROUP' id="status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="New">New</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Complete">Complete</option>
                </select>
            </div>

            <!-- PRIORITY -->
            <div class='INPUT-GROUP'>
                <select class='DROPDOWN-GROUP' id="priority" name="priority" required>
                    <option value="">Select Priority</option>
                    <option value="Low">Low</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>

            <!-- DESCRIPTION -->
            <div class="DESC-GROUP">
                <label for="description" class="DESCRIPTION-LABEL">Description:</label>
                <textarea id="description" name="description" class="TASK-TEXT-AREA" rows="6" required></textarea>
            </div>

            <div class="TASK-BUTTONS">
                <button class='CREATE-BUTTON' type='submit'>
                    Create Task
                </button>
                <button
                    class="CANCEL-BUTTON"
                    type="button"
                    onclick="window.location.href='list-task-page.php?clearance=<?php echo $_SESSION["clearance"]; ?>&id=<?php echo $_SESSION["id"]; ?>'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<!-- CREATE-TASK BOX END -->