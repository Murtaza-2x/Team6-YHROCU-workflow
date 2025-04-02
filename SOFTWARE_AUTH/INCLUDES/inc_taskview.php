<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/taskview_styles.css" rel="stylesheet">
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- TASK VIEW -->
<div class='VIEW-TASK-CONTAINER'>
    <div class='VIEW-TASK-BOX'>
        <div class='VIEW-HEAD'>
            <h1>View Task</h1>
            <p>See task details below</p>
        </div>

        <!-- HEADER -->
        <div class="VIEW-ROW">
            <div class="VIEW-COLUMN">
                <h1 class="TASK-LABEL">Task Title</h1>
                <div class='INPUT-GROUP'>
                    <input type='text' value="<?php echo htmlspecialchars($task['subject']); ?>" disabled />
                </div>
            </div>

            <div class="VIEW-COLUMN">
                <h1 class="TASK-LABEL">Project Allocation</h1>
                <h2 class="TASK-LABEL">(where the task is assigned)</h2>
                <div class='INPUT-GROUP'>
                    <input type='text' value="<?php echo htmlspecialchars($projectName); ?>" disabled />
                </div>
            </div>
        </div>

        <!-- PRIORITY & STATUS -->
        <div class="VIEW-ROW">
            <div class="VIEW-COLUMN">
                <div class="TASK-LABEL">Status</div>
                <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                        <button class="PILL-NEW <?php echo ($task['status'] === 'New') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">New</button>
                        <button class="PILL-IN-PROGRESS <?php echo ($task['status'] === 'In Progress') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">In Progress</button>
                        <button class="PILL-COMPLETE <?php echo ($task['status'] === 'Complete') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Complete</button>
                    </div>
                </div>
            </div>

            <div class="VIEW-COLUMN">
                <div class="TASK-LABEL">Priority</div>
                <div class="TASK-PILL-CONTAINER">
                    <div class="PILL">
                        <button class="PILL-URGENT <?php echo ($task['priority'] === 'Urgent') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Urgent</button>
                        <button class="PILL-MODERATE <?php echo ($task['priority'] === 'Moderate') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Moderate</button>
                        <button class="PILL-LOW <?php echo ($task['priority'] === 'Low') ? 'PILL-ACTIVE' : 'PILL-INACTIVE'; ?>">Low</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DESCRIPTION -->
        <div class="VIEW-ROW">
            <label class="TASK-LABEL DESCRIPTION-LABEL">Description</label>
            <textarea class="TASK-TEXT-AREA" rows="6" readonly><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>

        <!-- ASSIGNED -->
        <div class="TASK-ROW ASSIGNED-ROW">
            <div class="ASSIGNED-INFO">
                <span class="ASSIGNED-LABEL">Assigned:</span>
                <span class="ASSIGNED-LABEL-2">
                    <?php
                    $displayNames = array_map(function ($uid) use ($user_map) {
                        return $user_map[$uid] ?? $uid;
                    }, $assignedUsers);
                    echo htmlspecialchars(implode(', ', $displayNames)) ?: 'None';
                    ?>
                </span>
            </div>
        </div>

        <!-- LAST EDITED -->
        <?php if (!empty($lastEditor) && !empty($lastEditTime)): ?>
            <div class="TASK-ROW ASSIGNED-ROW">
                <div class="ASSIGNED-INFO">
                    <div class="ASSIGNED-LABEL">
                        <span>Last Edited By: <strong><?php echo htmlspecialchars($lastEditor); ?></strong></span>
                        <span>Archived At: <strong><?php echo htmlspecialchars($lastEditTime); ?></strong></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- BUTTONS -->
        <div class="TASK-BUTTONS">
            <?php if (has_role('Admin')): ?>
                <button class="UPDATE-BUTTON" onclick="window.location.href='edit-task-page.php?id=<?php echo urlencode($taskId); ?>'">Update Task</button>
            <?php endif; ?>
            <button class="CANCEL-BUTTON" onclick="window.location.href='list-task-page.php'">Cancel</button>
            <?php if (is_admin()): ?>
                <button class="VIEW-LOGS-BUTTON" onclick="window.location.href='view-task-logs-page.php?id=<?php echo urlencode($taskId); ?>'">
                    View Task Logs
                </button>
            <?php endif; ?>
        </div>

        <!-- COMMENTS -->
        <div class="COMMENTS-SECTION">
            <div class="TOP-BAR"></div>
            <h1>Comments</h1>
            <div class="COMMENT-LIST">
                <?php
                $stmt_comments = $conn->prepare("SELECT * FROM comments WHERE task_id = ? ORDER BY created_at ASC");
                $stmt_comments->bind_param("i", $taskId);
                $stmt_comments->execute();
                $res_comments = $stmt_comments->get_result();
                if ($res_comments && $res_comments->num_rows > 0) {
                    while ($comment = $res_comments->fetch_assoc()) {
                        echo "<div class='COMMENT'>";
                        $authorName = $user_map[$comment['user_id']] ?? $comment['user_id'];
                        echo "<p class='COMMENT-USER'>" . htmlspecialchars($authorName) . " - <span class='COMMENT-DATE'>" . $comment['created_at'] . "</span></p>";
                        echo "<p class='COMMENT-TEXT'>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No comments yet.</p>";
                }
                ?>
            </div>

            <div class="ADD-COMMENT">
                <div id="comment-form">
                    <form action="view-task-page.php?id=<?php echo urlencode($taskId); ?>" method="post">
                        <textarea name="comment" placeholder="Enter your comment here" required></textarea>
                        <button type="submit" name="submit_comment">Submit Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- TASK VIEW END -->