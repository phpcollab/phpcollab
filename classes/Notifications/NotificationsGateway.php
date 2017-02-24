<?php


namespace phpCollab\Notifications;

use phpCollab\Database;

/**
 * Class NotificationsGateway
 * @package phpCollab\Notifications
 */
class NotificationsGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * NotificationsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

}