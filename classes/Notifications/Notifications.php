<?php


namespace phpCollab\Notifications;

use phpCollab\Database;
use Exception;

/**
 * Class Notifications
 * @package phpCollab\Notifications
 */
class Notifications
{
    protected $notifications_gateway;
    protected $db;

    /**
     * Members constructor.
     */
    public function __construct()
    {
        $this->db = new Database();

        $this->notifications_gateway = new NotificationsGateway($this->db);
    }

    /**
     * @param $memberId
     * @return mixed
     * @throws Exception
     */
    public function addMember($memberId)
    {
        if (filter_var($memberId, FILTER_VALIDATE_INT)) {
            return $this->notifications_gateway->addMember($memberId);
        } else {
            throw new Exception('Invalid member id');
        }
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getNotificationsWhereMemeberIn($memberId)
    {
        return $this->notifications_gateway->getNotificationsWhereMemeberIn($memberId);
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getMemberNotifications($memberId)
    {
        return $this->notifications_gateway->getNotificationsByMemberId($memberId);
    }

    /**
     * @param $memberId
     */
    public function deleteNotificationsByMemberIdIn($memberId)
    {
        $this->notifications_gateway->deleteNotificationsByMemberIdIn($memberId);
    }

    /**
     * Sets the various flags for notifications and alerts
     * @param $memberId
     * @param $taskAssignment
     * @param $removeProjectTeam
     * @param $addProjectTeam
     * @param $newTopic
     * @param $newPost
     * @param $statusTaskChange
     * @param $priorityTaskChange
     * @param $dueDateTaskChange
     * @param $clientAddTask
     * @param $uploadFile
     * @param $dailyAlert
     * @param $weeklyAlert
     * @param $pastDueAlert
     * @return mixed
     */
    public function setAlerts($memberId, $taskAssignment, $removeProjectTeam, $addProjectTeam, $newTopic, $newPost,
                              $statusTaskChange, $priorityTaskChange, $dueDateTaskChange, $clientAddTask,
                              $uploadFile, $dailyAlert, $weeklyAlert, $pastDueAlert)
    {
        // Gather all the attributes into an array
        $arr = get_defined_vars();

        // Remove $memberId from the array
        unset($arr['memberId']);

        // This loops through the array of arguments and sets their default value if they are null.
        // Loop through each of the arguments and set it to 1 or 0 .
        foreach ($arr as $key => $value) {
            if (array_key_exists($key, $arr)) {
                if (is_null($value) || $value === '1') {
                    ${$key} = 1;
                } else if ($value === 'true') {
                    ${$key} = 0;
                }
            }
        }

        return $this->notifications_gateway->updateAlerts($memberId, $taskAssignment, $removeProjectTeam,
                $addProjectTeam, $newTopic, $newPost, $statusTaskChange, $priorityTaskChange, $dueDateTaskChange,
                $clientAddTask, $uploadFile, $dailyAlert, $weeklyAlert, $pastDueAlert);
    }

}
