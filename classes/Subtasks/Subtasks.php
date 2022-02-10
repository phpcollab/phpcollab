<?php


namespace phpCollab\Subtasks;

use Exception;
use Monolog\Logger;
use phpCollab\Container;
use phpCollab\Database;
use Symfony\Component\HttpFoundation\Session\Session;

class Subtasks
{
    protected $subtasks_gateway;
    protected $db;
    protected $strings;
    protected $notifications;
    protected $notificationsList;
    protected $subtaskNotifications;

    /**
     * Subtasks constructor.
     * @param Database $database
     * @param Container $container
     * @throws Exception
     */
    public function __construct(Database $database, Container $container)
    {
        $this->db = $database;
        $this->subtasks_gateway = new SubtasksGateway($this->db);
        $this->notifications = $container->getNotificationsManager();
        $this->subtaskNotifications = $container->getSubtasksNotificationsManager();
        $this->strings = $GLOBALS["strings"];
    }

    /**
     * @param int $subtaskId
     * @return mixed
     */
    public function getById(int $subtaskId)
    {
        $taskId = filter_var($subtaskId, FILTER_VALIDATE_INT);
        return $this->subtasks_gateway->getById($taskId);

    }

    /**
     * @param int $parentTaskId
     * @param string $name
     * @param string $description
     * @param int $owner
     * @param int $assignedTo
     * @param int $status
     * @param int $priority
     * @param string $startDate
     * @param string $dueDate
     * @param float $estimatedTime
     * @param float $actualTime
     * @param string $comments
     * @param int $completion
     * @param int $published
     * @return array
     */
    public function add(
        int $parentTaskId,
        string $name,
        string $description,
        int $owner,
        int $assignedTo,
        int $status,
        int $priority,
        string $startDate,
        string $dueDate,
        float $estimatedTime,
        float $actualTime,
        string $comments,
        int $completion,
        int $published
    ): array {
        $created = date('Y-m-d h:i');
        $id = $this->subtasks_gateway->addSubtask($parentTaskId, $name, $description, $owner, $assignedTo, $status,
            $priority,
            $startDate, $dueDate, $estimatedTime, $actualTime, $comments, $completion, $published, $created);
        return $this->getById($id);
    }

    /**
     * @param int $subtaskId
     * @param string $name
     * @param string $description
     * @param int $assignedTo
     * @param int $status
     * @param int $priority
     * @param string $startDate
     * @param string $dueDate
     * @param float $estimatedTime
     * @param float $actualTime
     * @param string $comments
     * @param string $modified
     * @param int $completion
     * @param int $published
     * @return mixed
     */
    public function update(
        int $subtaskId,
        string $name,
        string $description,
        int $assignedTo,
        int $status,
        int $priority,
        string $startDate,
        string $dueDate,
        float $estimatedTime,
        float $actualTime,
        string $comments,
        string $modified,
        int $completion,
        int $published
    ) {

        $this->subtasks_gateway->updateSubtask($subtaskId, $name, $description, $assignedTo, $status, $priority,
            $startDate, $dueDate,
            $estimatedTime, $actualTime, $comments, $modified, $completion, $published);
        return $this->getById($subtaskId);
    }

    /**
     * @param int $subtaskId
     * @return mixed
     */
    public function publish(int $subtaskId)
    {
        return $this->subtasks_gateway->publishSubtask($subtaskId);
    }

    /**
     * @param int $subtaskId
     * @return mixed
     */
    public function unpublish(int $subtaskId)
    {
        return $this->subtasks_gateway->unpublishSubtask($subtaskId);
    }

    /**
     * @param int $subtaskId
     * @param string $date
     * @return mixed
     */
    public function setCompletionDate(int $subtaskId,string $date)
    {
        return $this->subtasks_gateway->setCompletionDate($subtaskId, $date);

    }

    /**
     * @param int $subtaskId
     * @param string $date
     * @return mixed
     */
    public function setAssignedDate(int $subtaskId, string $date)
    {
        return $this->subtasks_gateway->setAssignedDate($subtaskId, $date);
    }

    /**
     * string
     * @param $notification
     * @param $subtaskDetails
     * array * @param $projectDetails
     * @param Session $session
     * @param Logger $logger
     * @throws Exception
     */
    public function sendNotification($notification, $subtaskDetails, $projectDetails, Session $session, Logger $logger)
    {
        $send = false;

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
                    $send = true;
                }
                break;
            case "status":
                if ($this->notificationsList["statusTaskChange"] == "0") {
                    $this->subtaskNotifications->setSubject($this->strings["noti_statustaskchange1"]);
                    $this->subtaskNotifications->setBody($this->strings["noti_statustaskchange2"]);
                    $send = true;
                }
                break;
            case "dueDate":
                if ($this->notificationsList["duedateTaskChange"] == "0") {
                    $this->subtaskNotifications->setSubject($this->strings["noti_duedatetaskchange1"]);
                    $this->subtaskNotifications->setBody($this->strings["noti_duedatetaskchange2"]);
                    $send = true;
                }
                break;
            case "assignment":
                if ($this->notificationsList["taskAssignment"] == "0") {
                    $this->subtaskNotifications->setSubject($this->strings["noti_taskassignment1"]);
                    $this->subtaskNotifications->setBody($this->strings["noti_taskassignment2"]);
                    $send = true;
                }
                break;
            default:
                throw new Exception('Error sending notification');
        }

        if ($send) {
            try {
                $this->subtaskNotifications->sendEmail($session, $logger);
            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($e->getMessage());
            }
        }
    }
}
