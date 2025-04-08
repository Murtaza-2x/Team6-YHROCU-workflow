<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/logview_styles.css" rel="stylesheet">
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- LOG SECTION -->
<div class="VIEW-LOG-CONTAINER">
    <div class="VIEW-LOG-BOX">
        <div class='VIEW-HEAD'>
            <h1>Task Logs</h1>
            <p>Below are the archived versions of this task:</p>
        </div>

        <!-- ERROR/SUCCESS MESSAGES -->
        <?php if (!empty($errorMsg)): ?>
            <div class="LOGIN-ERROR-MESSAGE"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php elseif (!empty($successMsg)): ?>
            <div class="LOGIN-SUCCESS-MESSAGE"><?php echo htmlspecialchars($successMsg); ?></div>
        <?php endif; ?>

        <!-- TASK FILTER -->
        <div class="TASK-FILTER">
            <input type="text" id="searchTask" placeholder="Search tasks...">
            <button type="button" id="btnSearchTask">Filter</button>
        </div>
        <!-- TASK FILTER END -->

        <div class="LOG-LIST">
            <?php if ($logCount > 0) : ?>
                <table class="LOG-TABLE" id="TASK-TABLE">
                    <thead>
                        <tr class="LOG-HEAD">
                            <th>Edited By</th>
                            <th>Archived At</th>
                            <th>Created At</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Description</th>
                            <th>Comments Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logsArray as $log):
                            $logEditorId = $log['edited_by'] ?? 'unknown';
                            $logEditor   = htmlspecialchars($user_map[$logEditorId] ?? $logEditorId);
                            $archivedAt  = $log['archived_at'] ?? '-';
                            $createdAt   = $log['created_at'] ?? '-';
                            $subject     = htmlspecialchars($log['subject'] ?? '');
                            $status      = htmlspecialchars($log['status'] ?? '');
                            $priority    = htmlspecialchars($log['priority'] ?? '');
                            $description = nl2br(htmlspecialchars($log['description'] ?? ''));
                            $archivedAtTime = strtotime($archivedAt);

                            // Pills
                            $statusPill = match ($status) {
                                'New'         => "<button class='PILL-NEW' id='PILL-ACTIVE'>New</button>",
                                'In Progress' => "<button class='PILL-IN-PROGRESS' id='PILL-ACTIVE'>In Progress</button>",
                                'Complete'    => "<button class='PILL-COMPLETE' id='PILL-ACTIVE'>Complete</button>",
                                default       => "<button class='PILL-INACTIVE'>$status</button>",
                            };

                            $priorityPill = match ($priority) {
                                'Urgent'   => "<button class='PILL-URGENT' id='PILL-ACTIVE'>Urgent</button>",
                                'Moderate' => "<button class='PILL-MODERATE' id='PILL-ACTIVE'>Moderate</button>",
                                'Low'      => "<button class='PILL-LOW' id='PILL-ACTIVE'>Low</button>",
                                default    => "<button class='PILL-INACTIVE'>$priority</button>",
                            };

                            // Filter comments based on archive time
                            $archivedComments = array_filter($commentsArray, function ($c) use ($archivedAtTime) {
                                return strtotime($c['created_at']) <= $archivedAtTime;
                            });

                            $commentText = implode("<br>", array_map(function ($c) {
                                return "<span class='COMMENT-TEXT'>" . htmlspecialchars($c['comment']) . "</span>";
                            }, $archivedComments));

                            $commentCount = count($archivedComments);
                        ?>

                            <tr>
                                <td><?php echo $logEditor; ?></td>
                                <td><?php echo $archivedAt; ?></td>
                                <td><?php echo $createdAt; ?></td>
                                <td><?php echo $subject; ?></td>
                                <td><?php echo $statusPill; ?></td>
                                <td><?php echo $priorityPill; ?></td>
                                <td class="LOG-DESC"><?php echo $description; ?></td>
                                <td><?php echo $commentCount; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h1 class="USER-MESSAGE">No logs available for this task.</h1>
            <?php endif; ?>

            <!-- BUTTONS -->
            <button class="BACK-BUTTON" onclick="window.location.href='view-task-page.php?id=<?php echo urlencode($taskId); ?>'">Back to Task</button>
            <button class="EXPORT-BUTTON" onclick="window.location.href='view-task-logs-page.php?id=<?php echo urlencode($taskId); ?>&export=1'">Export Logs</button>
            <!-- BUTTONS END -->
        </div>
    </div>
</div>
<!-- LOG SECTION END -->

<script src="JS/SEARCH-TABLE.js"></script>