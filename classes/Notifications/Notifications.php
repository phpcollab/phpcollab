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
     */
    public function deleteNotificationsByMemberIdIn($memberId)
    {
        $this->notifications_gateway->deleteNotificationsByMemberIdIn($memberId);
    }

}
