<?php


namespace phpCollab\Alerts;


use Exception;
use phpCollab\Container;
use phpCollab\Notification;

/**
 * Class DailyAlertEmail
 * @package phpCollab\Alerts
 */
class DailyAlertEmail extends Notification
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * @param $member
     * @param $tasks
     * @param $subTasks
     * @throws Exception
     */
    public function sendEmail($member, $tasks, $subTasks)
    {
        if ($member && $tasks && $subTasks) {
            try {
                // Empty the To addresses so we start fresh
                $this->clearAddresses();

                $this->FromName = $member["name"];
                $this->From = $member["email_work"];
                $this->AddAddress($member["email_work"], $member["name"]);
                $this->Subject = $this->strings['daily_alert_subject'];
                $this->Priority = "3";

                $body = '';

                // Initialize counts
                $taskCount = 0;
                $subtaskCount = 0;

                foreach ($tasks as $task) {
                    if ($taskCount == 0) {
                        $body .= $this->strings['alert_daily_task'] . "\n";
                        $body .= "----------------------------------\n\n";
                    }

                    /**
                     * array (size=8)
                     * 0 'id' => string '262' (length=3)
                     * 1 'name' => string 'Task to be copied from copied project' (length=37)
                     * 2 'priority' => string '0' (length=1)
                     * 3 'status' => string '2' (length=1)
                     * 4 'completion' => string '0' (length=1)
                     * 5 'start_date' => string '2019-05-07' (length=10)
                     * 6 'due_date' => string '2019-07-18' (length=10)
                     * 7 'description' => string 'blah blah blah' (length=14)
                     * 8 'project_id' => string '35' (length=2)
                     * 9 'project_name' => string 'Project for copying purposes' (length=28)
                     */

                    if ($task["completion"] == 0) {
                        $completion = $task["completion"] . "%";
                    } else {
                        $completion = $task["completion"] . "0%";
                    }

                    $body .= <<<BODY
{$this->strings['task']} : {$task["name"]} ({$task["id"]})
{$this->strings['project']} : {$task["project_name"]}
{$this->strings['link']} : {$this->root}/tasks/viewtask.php?id={$task["id"]}
{$this->strings['start_date']} : {$task["start_date"]}
{$this->strings['due_date']} : {$task["due_date"]}
{$this->strings['completion']} : $completion 
{$this->strings['priority']} : {$task["priority"]} - {$this->priority[$task["priority"]]}
{$this->strings['status']} : {$task["status"]} - {$this->status[$task["status"]]}

--------


BODY;

                    // Set priority level if high priority
                    if ($task["priority"] == "4" || $task["priority"] == "5") {
                        $this->Priority = "1";
                    }

                    // Update task count
                    $taskCount++;
                }

                foreach ($subTasks as $subtask) {
                    // Set subtask body
                    if ($subtaskCount == 0) {
                        $body .= <<<BODY
{$this->strings["alert_daily_subtask"]}
--------------------------------------


BODY;
                    }

                    /**
                     * 0 'id' => string '262' (length=3)
                     * 1 'name' => string 'Task to be copied from copied project' (length=37)
                     * 2 'priority' => string '3' (length=1)
                     * 3 'status' => string '2' (length=1)
                     * 4 'completion' => string '0' (length=1)
                     * 5 'start_date' => string '2019-05-07' (length=10)
                     * 6 'due_date' => string '2019-07-18' (length=10)
                     * 7 'description' => string '' (length=0)
                     */

                    if ($subtask["completion"] == 0) {
                        $subTaskCompletion = $subtask["completion"] . "%";
                    } else {
                        $subTaskCompletion = $subtask["completion"] . "0%";
                    }

                    $body .= <<<BODY
{$this->strings["task"]} : {$subtask["name"]}  ({$subtask["id"]} ) 
{$this->strings["link"]} : {$this->root}/subtasks/viewsubtask.php?id={$subtask["id"]} &task={$subtask["parent_id"]} 
{$this->strings["task"]} : {$subtask["parent_name"]}  ({$subtask["parent_id"]} ) 
{$this->strings["start_date"]} : {$subtask["start_date"]} 
{$this->strings["due_date"]} : {$subtask["due_date"]} 
{$this->strings["completion"]} : $subTaskCompletion
{$this->strings["priority"]} : {$subtask["priority"]}  - {$this->priority[$subtask["priority"]]} 
{$this->strings["status"]} : {$subtask["status"]}  - {$this->status[$subtask["status"]]} 


--------
BODY;

                    // Set priority level if high priority
                    if ($subtask["priority"] == "4" || $subtask["priority"] == "5") {
                        $this->Priority = "1";
                    }

                    // Update subtask count
                    $subtaskCount++;
                } // end subtasks

                $body .= "\n\n" . $this->footer;

                if (!empty($member["email_work"]) && ($taskCount > 0 || $subtaskCount > 0)) {
                    // Set body
                    $this->Body = $body;

                    // Send daily alert
                    $this->Send();
                    $this->clearAddresses();
                }
            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($this->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending mail');
        }
    }
}
