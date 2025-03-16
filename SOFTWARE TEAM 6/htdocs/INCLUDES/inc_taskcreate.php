<head>
    <link href="CSS/taskcreate_styles.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"rel="stylesheet" />


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="JS/SELECT-MULTI-DROPDOWN.js"></script>

</head>

<body>
    <div class='MIDDLE-SECTION'>
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
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
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

                    <button class='CREATE-TASK-BUTTON' type='submit'>
                        Create Task
                    </button>
                </form>
            </div>
        </div>
        <!-- CREATE-TASK BOX END -->

    </div>
    <!-- MIDDLE SECTION END -->