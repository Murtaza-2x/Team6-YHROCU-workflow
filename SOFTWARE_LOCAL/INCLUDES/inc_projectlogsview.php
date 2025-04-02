<?php
if (!isset($project_id)) {
    $project_id = $_GET['id'] ?? 0;
}
?>

<head>
    <title><?php echo $title; ?></title>
    <link href="CSS/pill_styles.css" rel="stylesheet">
    <link href="CSS/dropdown_styles.css" rel="stylesheet">
    <link href="CSS/logview_styles.css" rel="stylesheet">
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- LOG SECTION -->
<div class="VIEW-LOG-CONTAINER">
    <div class="VIEW-LOG-BOX">
        <div class="VIEW-HEAD">
            <h1>Project Logs</h1>
            <p>Below are the archived versions for this project:</p>
        </div>

        <!-- TASK CHANGE LOG LIST -->
        <div class="LOG-LIST">
            <h2>Task Change Logs</h2>

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

                            // Status pill
                            switch ($logStatus) {
                                case 'New':
                                    $statusPill = "<button class='PILL-NEW' id='PILL-ACTIVE'>New</button>"; break;
                                case 'In Progress':
                                    $statusPill = "<button class='PILL-IN-PROGRESS' id='PILL-ACTIVE'>In Progress</button>"; break;
                                case 'Complete':
                                    $statusPill = "<button class='PILL-COMPLETE' id='PILL-ACTIVE'>Complete</button>"; break;
                                default:
                                    $statusPill = "<button class='PILL-INACTIVE'>$logStatus</button>"; break;
                            }

                            // Priority pill
                            switch ($logPriority) {
                                case 'Urgent':
                                    $priorityPill = "<button class='PILL-URGENT' id='PILL-ACTIVE'>Urgent</button>"; break;
                                case 'Moderate':
                                    $priorityPill = "<button class='PILL-MODERATE' id='PILL-ACTIVE'>Moderate</button>"; break;
                                case 'Low':
                                    $priorityPill = "<button class='PILL-LOW' id='PILL-ACTIVE'>Low</button>"; break;
                                default:
                                    $priorityPill = "<button class='PILL-INACTIVE'>$logPriority</button>"; break;
                            }
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
                <h3 class="USER-MESSAGE">No task logs available for this project.</h3>
            <?php endif; ?>
        </div>

        <!-- PROJECT CHANGE LOG LIST -->
        <div class="LOG-LIST" style="margin-top: 40px;">
            <h2>Project Change Logs</h2>

            <?php if (!empty($projectLogs)): ?>
                <table class="LOG-TABLE">
                    <thead>
                        <tr class="LOG-HEAD">
                            <th>Edited By</th>
                            <th>Archived At</th>
                            <th>Project Name</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projectLogs as $log): 
                            $logEditor   = htmlspecialchars($log['username'] ?? 'Unknown');
                            $archivedAt  = $log['archived_at'];
                            $logName     = htmlspecialchars($log['project_name']);
                            $logStatus   = htmlspecialchars($log['status']);
                            $logPriority = htmlspecialchars($log['priority']);
                            $logDueDate  = $log['due_date'];
                            $logDesc     = nl2br(htmlspecialchars($log['description']));

                            // Status pill
                            switch ($logStatus) {
                                case 'New':
                                    $statusPill = "<button class='PILL-NEW' id='PILL-ACTIVE'>New</button>"; break;
                                case 'In Progress':
                                    $statusPill = "<button class='PILL-IN-PROGRESS' id='PILL-ACTIVE'>In Progress</button>"; break;
                                case 'Complete':
                                    $statusPill = "<button class='PILL-COMPLETE' id='PILL-ACTIVE'>Complete</button>"; break;
                                default:
                                    $statusPill = "<button class='PILL-INACTIVE'>$logStatus</button>"; break;
                            }

                            // Priority pill
                            switch ($logPriority) {
                                case 'Urgent':
                                    $priorityPill = "<button class='PILL-URGENT' id='PILL-ACTIVE'>Urgent</button>"; break;
                                case 'Moderate':
                                    $priorityPill = "<button class='PILL-MODERATE' id='PILL-ACTIVE'>Moderate</button>"; break;
                                case 'Low':
                                    $priorityPill = "<button class='PILL-LOW' id='PILL-ACTIVE'>Low</button>"; break;
                                default:
                                    $priorityPill = "<button class='PILL-INACTIVE'>$logPriority</button>"; break;
                            }
                        ?>
                        <tr>
                            <td><?php echo $logEditor; ?></td>
                            <td><?php echo $archivedAt; ?></td>
                            <td><?php echo $logName; ?></td>
                            <td><?php echo $statusPill; ?></td>
                            <td><?php echo $priorityPill; ?></td>
                            <td><?php echo $logDueDate; ?></td>
                            <td class="LOG-DESC"><?php echo $logDesc; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h3 class="USER-MESSAGE">No project change logs available.</h3>
            <?php endif; ?>
        </div>

<!-- LOG ACTION BUTTONS -->
<div class="LOG-ACTION-BUTTONS">
  <button class="BACK-BUTTON" onclick="window.location.href='view-project-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($project_id); ?>'">
    Back to Project
  </button>

  <button class="EXPORT-BUTTON" onclick="window.location.href='view-project-logs-page.php?clearance=<?php echo urlencode($_SESSION['clearance']); ?>&id=<?php echo urlencode($project_id); ?>&export=1'">
    Export Logs
  </button>
</div>

</div>
