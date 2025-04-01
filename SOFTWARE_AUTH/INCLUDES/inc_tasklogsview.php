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

        <!-- LOG SECTION LIST -->
        <div class="LOG-LIST">
            <?php if ($logCount > 0): ?>
                <table class="LOG-TABLE">
                    <thead>
                        <tr class="LOG-HEAD">
                            <th>Edited By</th>
                            <th>Archived At</th>
                            <th>Created At</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logsArray as $log):
                            $logEditor   = htmlspecialchars($log['username'] ?? 'Unknown');
                            $archivedAt  = $log['archived_at'];
                            $createdAt   = $log['created_at'];
                            $logSubject  = htmlspecialchars($log['subject']);
                            $logStatus   = htmlspecialchars($log['status']);
                            $logPriority = htmlspecialchars($log['priority']);
                            $logDesc     = nl2br(htmlspecialchars($log['description']));

                            // Pill logic
                            $statusPill = match ($logStatus) {
                                'New'         => "<button class='PILL-NEW' id='PILL-ACTIVE'>New</button>",
                                'In Progress' => "<button class='PILL-IN-PROGRESS' id='PILL-ACTIVE'>In Progress</button>",
                                'Complete'    => "<button class='PILL-COMPLETE' id='PILL-ACTIVE'>Complete</button>",
                                default       => "<button class='PILL-INACTIVE'>$logStatus</button>",
                            };

                            $priorityPill = match ($logPriority) {
                                'Urgent'   => "<button class='PILL-URGENT' id='PILL-ACTIVE'>Urgent</button>",
                                'Moderate' => "<button class='PILL-MODERATE' id='PILL-ACTIVE'>Moderate</button>",
                                'Low'      => "<button class='PILL-LOW' id='PILL-ACTIVE'>Low</button>",
                                default    => "<button class='PILL-INACTIVE'>$logPriority</button>",
                            };
                        ?>
                            <tr>
                                <td><?php echo $logEditor; ?></td>
                                <td><?php echo $archivedAt; ?></td>
                                <td><?php echo $createdAt; ?></td>
                                <td><?php echo $logSubject; ?></td>
                                <td><?php echo $statusPill; ?></td>
                                <td><?php echo $priorityPill; ?></td>
                                <td class="LOG-DESC"><?php echo $logDesc; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h1 class="USER-MESSAGE">No logs available for this task.</h1>
            <?php endif; ?>

            <!-- BUTTONS -->
            <button class="BACK-BUTTON" onclick="window.location.href='view-task-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($id); ?>'">
                Back to Task
            </button>
            <button class="EXPORT-BUTTON" onclick="window.location.href='view-task-logs-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($id); ?>&export=1'">
                Export Logs
            </button>
        </div>
        <!-- LOG SECTION LIST END -->
    </div>
</div>
<!-- MIDDLE SECTION END -->