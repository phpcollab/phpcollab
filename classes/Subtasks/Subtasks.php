<?php


namespace phpCollab\Subtasks;

use Exception;
use phpCollab\Database;
use phpCollab\Notifications\Notifications;
use phpCollab\Notifications\SubtaskNotifications;
use Symfony\Component\HttpFoundation\Session\Session;

class Subtasks
{
    protected $subtasks_gateway;
    protected $db;
    protected $strings;
    protected $notifications;
    protected $notificationsList;
    protected $subtaskNotifications;
    protected $tableCollab;
    private $send;

    /**
     * Subtasks constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->subtasks_gateway = new SubtasksGateway($this->db);
        $this->notifications = new Notifications();
        $this->subtaskNotifications = new SubtaskNotifications();
        $this->strings = $GLOBALS["strings"];
        $this->tableCollab = $GLOBALS["tableCollab"];
    }

    /**
     * @param $subtaskId
     * @return mixed
     */
    public function getById($subtaskId)
    {
        $taskId = filter_var($subtaskId, FILTER_VALIDATE_INT);
        return $this->subtasks_gateway->getById($taskId);

    }

    /**
     * @param $parentTaskId
     * @param $name
     * @param $description
     * @param $owner
     * @param $assignedTo
     * @param $status
     * @param $priority
     * @param $startDate
     * @param $dueDate
     * @param $estimatedTime
     * @param $actualTime
     * @param $comments
     * @param $completion
     * @param $published
     * @return string
     */
    public function add($parentTaskId, $name, $description, $owner, $assignedTo, $status, $priority, $startDate,
                        $dueDate, $estimatedTime, $actualTime, $comments, $completion, $published)
    {
        $created = date('Y-m-d h:i');
        return $this->subtasks_gateway->addSubtask($parentTaskId, $name, $description, $owner, $assignedTo, $status, $priority,
            $startDate, $dueDate, $estimatedTime, $actualTime, $comments, $completion, $published, $created);
    }

    /**
     * @param $subtaskId
     * @param $name
     * @param $description
     * @param $assignedTo
     * @param $status
     * @param $priority
     * @param $startDate
     * @param $dueDate
     * @param $estimatedTime
     * @param $actualTime
     * @param $comments
     * @param $modified
     * @param $completion
     * @param $published
     * @return mixed
     */
    public function update($subtaskId, $name, $description, $assignedTo, $status, $priority, $startDate, $dueDate,
                           $estimatedTime, $actualTime, $comments, $modified, $completion, $published)
    {

        $this->subtasks_gateway->updateSubtask($subtaskId, $name, $description, $assignedTo, $status, $priority, $startDate, $dueDate,
            $estimatedTime, $actualTime, $comments, $modified, $completion, $published);
        return $this->getById($subtaskId);
    }

    /**
     * @param $subtaskId
     * @return mixed
     */
    public function publish($subtaskId)
    {
        return $this->subtasks_gateway->publishSubtask($subtaskId);
    }

    /**
     * @param $subtaskId
     * @return mixed
     */
    public function unpublish($subtaskId)
    {
        return $this->subtasks_gateway->unpublishSubtask($subtaskId);
    }

    /**
     * @param $subtaskId
     * @param $date
     * @return mixed
     */
    public function setCompletionDate($subtaskId, $date)
    {
        return $this->subtasks_gateway->setCompletionDate($subtaskId, $date);

    }

    /**
     * @param $subtaskId
     * @param $date
     * @return mixed
     */
    public function setAssignedDate($subtaskId, $date)
    {
        return $this->subtasks_gateway->setAssignedDate($subtaskId, $date);
    }


    /**
     * string @param $notification
     * array @param $subtaskDetails
     * array @param $projectDetails
     * @throws Exception
     */
    public function sendNotification($notification, $subtaskDetails, $projectDetails, Session $session)
    {
        $this->send = false;

        // Get a list of notifications to be used
        if (empty($this->notificationsList)) {
            $this->notificationsList = $this->notifications->getMemberNotifications($subtaskDetails["subtas_assigned_to"]);
        }

        if (!empty($this->notificationsList["email_work"])) {
            $this->subtaskNotifications->setWorkEmail($this->notificationsList["email_work"]);
            $this->subtaskNotifications->setUserName($this->notificationsList["name"]);
        }

        if (empty($this->subtaskNotifications->getTaskDetails())) {
            $this->subtaskNotifications->setTaskDetails($subtaskDetails);
        }

        if (empty($this->subtaskNotifications->getProjectDetails())) {
            $this->subtaskNotifications->setProjectDetails($projectDetails);
        }

        switch ($notification) {
            case "priority":
                if ($this->notificationsList["priorityTaskChange"] == "0") {
                    $this->subtaskNotifications->setSubject($this->strings["noti_prioritytaskchange1"]);
                    $this->subtaskNotifications->setBody($this->strings["noti_prioritytaskchange2"]);
                    $this->send = true;
                }
                break;
            case "status":
                if ($this->notificationsList["statusTaskChange"] == "0") {
                    $this->subtaskNotifications->setSubject($this->strings["noti_statustaskchange1"]);
                    $this->subtaskNotifications->setBody($this->strings["noti_statustaskchange2"]);
                    $this->send = true;
                }
                break;
            case "dueDate":
                if ($this->notificationsList["duedateTaskChange"] == "0") {
                    $this->subtaskNotifications->setSubject($this->strings["noti_duedatetaskchange1"]);
                    $this->subtaskNotifications->setBody($this->strings["noti_duedatetaskchange2"]);
                    $this->send = true;
                }
                break;
            case "assignment":
                if ($this->notificationsList["taskAssignment"] == "0") {
                    $this->subtaskNotifications->setSubject($this->strings["noti_taskassignment1"]);
                    $this->subtaskNotifications->setBody($this->strings["noti_taskassignment2"]);
                    $this->send = true;
                }
                break;
            default:
                throw new Exception('Error sending notification');
        }

        if ($this->send) {
            try {
                $this->subtaskNotifications->sendEmail($session);
            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($e->getMessage());
            }
        }
        $this->send = false;
    }
}
