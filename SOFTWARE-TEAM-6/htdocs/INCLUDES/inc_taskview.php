<?php
$clearance = $_SESSION["clearance"];
?>

<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/pill_styles.css" rel="stylesheet">
    <link href="CSS/dropdown_styles.css" rel="stylesheet">
    <link href="CSS/taskview_styles.css" rel="stylesheet">
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- TASK VIEW -->
<div class='VIEW-TASK-CONTAINER'>
    <div class='VIEW-TASK-BOX'>
        <div class='VIEW-HEAD'>
            <h1>View Task</h1>
            <p>See Task Details below</p>
        </div>

        <!-- HEADER -->
        <div class="VIEW-ROW">
            <div class="VIEW-COLUMN">
                <h1 class="TASK-LABEL">Task Title</h1>
                <div class='INPUT-GROUP'>
                    <input
                        type='text'
                        id='task-title'
                        name='task-title'
                        value="<?php echo htmlspecialchars($subject); ?>"
                        placeholder='Task Title' disabled />
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
                        id='project-title'
                        name='project-title'
                        value="<?php echo htmlspecialchars($projectName); ?>"
                        placeholder='Project'
                        disabled />
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
                        <button class="PILL-NEW <?php echo ($status === 'New')         ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">New</button>
                        <button class="PILL-IN-PROGRESS <?php echo ($status === 'In Progress') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">In Progress</button>
                        <button class="PILL-COMPLETE <?php echo ($status === 'Complete')    ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Complete</button>
                    </div>
                </div>
            </div>

            <div class="VIEW-COLUMN">
                <div class="TASK-LABEL">Priority</div>
                <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                        <button class="PILL-URGENT   <?php echo ($priority === 'Urgent')   ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Urgent</button>
                        <button class="PILL-MODERATE <?php echo ($priority === 'Moderate') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Moderate</button>
                        <button class="PILL-LOW      <?php echo ($priority === 'Low')       ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Low</button>
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
                id="description"
                name="description"
                class="TASK-TEXT-AREA"
                rows="6"
                readonly><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <!-- ASSIGNED -->
        <div class="TASK-ROW ASSIGNED-ROW">
            <div class="ASSIGNED-INFO">
                <span class="ASSIGNED-LABEL">Assigned:</span>
                <span class="ASSIGNED-LABEL-2">
                    <?php echo htmlspecialchars($assignedUsers) ?: 'None'; ?>
                </span>
            </div>
        </div>
        <!-- ASSIGNED END -->

        <!-- BUTTONS -->
        <div class="TASK-BUTTONS">

            <?php if ($_SESSION["clearance"] != 'User'): ?>
                <button class="UPDATE-BUTTON"
                    onclick="window.location.href='edit-task-page.php?id=<?php echo $id; ?>'">
                    Update Task
                </button>
            <?php endif; ?>

            <button
                class="CANCEL-BUTTON"
                onclick="window.location.href='list-task-page.php?clearance=<?php echo $_SESSION['clearance']; ?>&id=<?php echo $_SESSION['id']; ?>'">
                Cancel
            </button>

            <?php if ($_SESSION["clearance"] != 'User'): ?>
                <button class="VIEW-LOGS-BUTTON"
                onclick="window.location.href='view-logs-page.php?id=<?php echo $id; ?>'">
                    View Logs
                </button>
            <?php endif; ?>
        </div>
        <!-- BUTTONS END -->

        <!-- COMMENTS SECTION -->
        <div class="COMMENTS-SECTION">
            <div class="TOP-BAR"></div>
            <h1>Comments</h1>
            <div class="COMMENT-LIST">
                <?php
                $sql_comments = "SELECT c.*, u.username FROM comments c
                       JOIN users u ON c.user_id = u.id
                       WHERE c.task_id = $id
                       ORDER BY c.created_at ASC";
                $result_comments = $conn->query($sql_comments);
                if ($result_comments && $result_comments->num_rows > 0) {
                    while ($comment = $result_comments->fetch_assoc()) {
                        $commenter = htmlspecialchars($comment['username']);
                        $commentText = nl2br(htmlspecialchars($comment['comment']));
                        $createdAt = $comment['created_at'];
                        echo "<div class='COMMENT'>";
                        echo "<p class='COMMENT-USER'>{$commenter} - <span class='COMMENT-DATE'>{$createdAt}</span></p>";
                        echo "<p class='COMMENT-TEXT'>{$commentText}</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No comments yet.</p>";
                }
                ?>
            </div>
            <div class="ADD-COMMENT">
                <div id="comment-form">
                    <form action="view-task-page.php?id=<?php echo $id; ?>" method="post">
                        <textarea
                            name="comment"
                            placeholder="Enter your comment here"
                            required></textarea>
                        <button type="submit" name="submit_comment">Submit Comment</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- COMMENTS SECTION END -->

    </div>
</div>
<!-- TASK VIEW END -->