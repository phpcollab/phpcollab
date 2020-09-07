<?php


namespace phpCollab\Tasks;

use phpCollab\Database;

class TaskUpdates
{
    protected $updates_gateway;
    protected $db;

    /**
     * TaskUpdates constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->updates_gateway = new TaskUpdatesGateway($this->db);
    }

    /**
     * @param $type
     * @param $item
     * @param $member
     * @param $comments
     * @return string
     */
    public function addUpdate($type, $item, $member, $comments)
    {
        return $this->updates_gateway->addUpdate($type, $item, $member, $comments);
    }

    /**
     * @param $type
     * @param $taskId
     * @param null $sorting
     * @return mixed
     */
    public function getUpdates($type, $taskId, $sorting = null)
    {
        $type = filter_var($type, FILTER_VALIDATE_INT);
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        return $this->updates_gateway->getUpdates($type, $taskId, $sorting);
    }
}
