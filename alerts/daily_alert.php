<?php
/*
** Application name: phpCollab
** Last Edit page: 2006-09-30 
** Path by root: ../alerts/daily_alert.php
** Authors: Rich Cave (cavemansf)
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: daily_alert.php
**
** DESC: Notifications for daily alerts triggered by a cron job 
**
** HISTORY:
** -----------------------------------------------------------------------------
** TO-DO:
**  
**
** =============================================================================
*/

// Define directories for include files
define("PHPCOLLAB_INC_DIR", dirname(__FILE__) . "/../includes/");
define("PHPCOLLAB_CLASSES_DIR", dirname(__FILE__) . "/../classes/");
define("PHPCOLLAB_LANG_DIR", dirname(__FILE__) . "/../languages/");

// Include 
include(PHPCOLLAB_INC_DIR . "settings.php");
include(PHPCOLLAB_CLASSES_DIR . "request.php");
include(PHPCOLLAB_CLASSES_DIR . "notification.php");

if (!isset($langDefault) || ($langDefault == '')) {
    $langDefault = 'en';
}
include(PHPCOLLAB_LANG_DIR . "lang_" . $langDefault . ".php");

// Check if emailAlerts is set to true
if ($emailAlerts == "false") {
    // Return false
    exit(1);
}

// Check that database vars are set
if (!defined('MYSERVER') || !defined('MYLOGIN') || !defined('MYPASSWORD') || !defined('MYDATABASE')) {
    echo($strings['error_server']);
    exit(1);
}

// Create database requests
$alert = new Request();
$alert->connectClass();

$alertSub = new Request();
$alertSub->connectClass();

// Get table names
$membersTable = $tableCollab['members'];
$notificationsTable = $tableCollab['notifications'];
$tasksTable = $tableCollab['tasks'];
$subtasksTable = $tableCollab['subtasks'];
$projectsTable = $tableCollab['projects'];

// Get current date
$today = date("Y-m-d", time());

// Create new notification
$mail = new Notification();
$mail->message_type = "alt";

// Get list of members and notification settings
$query = "SELECT $membersTable.id, 
                 $membersTable.name,
                 $membersTable.email_work
          FROM   $membersTable, $notificationsTable 
          WHERE  $membersTable.id = $notificationsTable.member
          AND    $notificationsTable.dailyAlert = 0";

// Gpet result
$alert->query($query);

// Loop to retrieve all rows
while ($alert->fetch()) {
    // Set member
    $assignedTo = $row[0];

    // Clear all recipients in TO array
    $mail->ClearAddresses();

    // Set email info
    $mail->FromName = $row[1];
    $mail->From = $row[2];
    $mail->AddAddress($row[2], $row[1]);
    $mail->Subject = $strings['daily_alert_subject'];

    // Set default priority until otherwise notified
    $mail->Priority = "3";

    // Initialize body
    $body = '';

    // Initialize counts
    $taskCount = 0;
    $subtaskCount = 0;

    // See if there are any tasks pending 
    $query = "SELECT $tasksTable.id,
                     $tasksTable.name,
                     $tasksTable.priority, 
                     $tasksTable.status, 
                     $tasksTable.completion,
                     $tasksTable.start_date, 
                     $tasksTable.due_date,
                     $tasksTable.description,
                     $projectsTable.id, 
                     $projectsTable.name
              FROM  $tasksTable, $projectsTable, $membersTable
              WHERE $tasksTable.assigned_to = " . $assignedTo . "
              AND $tasksTable.status != 1 
              AND $tasksTable.due_date = '" . $today . "'
              AND $tasksTable.project = $projectsTable.id
              AND $tasksTable.assigned_to = $membersTable.id
              ORDER BY $tasksTable.priority";

    $alertSub->query($query);

    // Create email body with link
    while ($alertSub->fetch()) {
        // Set task body
        if ($taskCount == 0) {
            $body .= $strings['alert_daily_task'] . "\n";
            $body .= "----------------------------------\n\n";
        }
        $body .= $strings['task'] . " : " . $row[1] . " (" . $row[0] . ") \n";
        //$body .= $strings['project']." : ".$row[10]." (".$row[9].") \n";
        $body .= $strings['project'] . " : " . $row[9] . "\n";
        //$body .= $strings['link']." : ".$root."/general/login.php?url=tasks/viewtask.php?id=".$row[0]."\n";
        $body .= $strings['link'] . " : " . $root . "/tasks/viewtask.php?id=" . $row[0] . "\n";
        $body .= $strings['start_date'] . " : " . $row[5] . "\n";
        $body .= $strings['due_date'] . " : " . $row[6] . "\n";
        if ($row[4] == 0) {
            $body .= $strings['completion'] . " : " . $row[4] . "%\n";
        } else {
            $body .= $strings['completion'] . " : " . $row[4] . "0%\n";
        }
        $body .= $strings['priority'] . " : " . $row[2] . " - " . $priority[$row[2]] . "\n";
        $body .= $strings['status'] . " : " . $row[3] . " - " . $status[$row[3]] . "\n";
        //$body .= $strings['description']." :\n".$row[7]."\n";
        $body .= "\n\n--------\n\n";

        // Set priority level if high priority
        if ($row[2] == "4" || $row[2] == "5") {
            $mail->Priority = "1";
        }

        // Update task count
        $taskCount++;
    }

    // See if there are any subtasks pending 
    $query = "SELECT $subtasksTable.id, 
                     $subtasksTable.name,
                     $subtasksTable.priority, 
                     $subtasksTable.status, 
                     $subtasksTable.completion,
                     $subtasksTable.start_date, 
                     $subtasksTable.due_date,
                     $subtasksTable.description,
                     $tasksTable.id, 
                     $tasksTable.name
              FROM  $subtasksTable, $tasksTable, $membersTable
              WHERE $subtasksTable.assigned_to = " . $assignedTo . "
              AND $subtasksTable.status != 1 
              AND $subtasksTable.due_date = '" . $today . "'
              AND $subtasksTable.task = $tasksTable.id
              AND $subtasksTable.assigned_to = $membersTable.id
              ORDER BY $subtasksTable.priority";

    $alertSub->query($query);

    // Create email body with link
    while ($alertSub->fetch()) {
        // Set subtask body
        if ($subtaskCount == 0) {
            $body .= $strings['alert_daily_subtask'] . "\n";
            $body .= "----------------------------------\n\n";
        }
        $body .= $strings['task'] . " : " . $row[1] . " (" . $row[0] . ") \n";
        //$body .= $strings['link']." : ".$root."/general/login.php?url=subtasks/viewsubtask.php?id=".$row[0]."?task=".$row[8]."\n";
        $body .= $strings['link'] . " : " . $root . "/subtasks/viewsubtask.php?id=" . $row[0] . "&task=" . $row[8] . "\n";
        $body .= $strings['task'] . " : " . $row[9] . " (" . $row[8] . ") \n";
        $body .= $strings['start_date'] . " : " . $row[5] . "\n";
        $body .= $strings['due_date'] . " : " . $row[6] . "\n";
        if ($row[4] == 0) {
            $body .= $strings['completion'] . " : " . $row[4] . "%\n";
        } else {
            $body .= $strings['completion'] . " : " . $row[4] . "0%\n";
        }
        $body .= $strings['priority'] . " : " . $row[2] . " - " . $priority[$row[2]] . "\n";
        $body .= $strings['status'] . " : " . $row[3] . " - " . $status[$row[3]] . "\n";
        //$body .= $strings['description']." :\n".$row[7]."\n";
        $body .= "\n\n--------\n\n";

        // Set priority level if high priority
        if ($row[2] == "4" || $row[2] == "5") {
            $mail->Priority = "1";
        }

        // Update subtask count
        $subtaskCount++;
    }

    // If tasks found, send email alert
    if ($taskCount > 0 || $subtaskCount > 0) {
        // Set body
        $mailBody = $mail->header;
        $mailBody .= $body;
        $mailBody .= "\n" . $mail->footer;
        $mail->Body = $mailBody;

        // Send daily alert
        $mail->Send();
    }
}

// Close db connections
$alert->close();
$alertSub->close();

// Return successfully
exit(0);

?>
