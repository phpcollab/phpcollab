<?php


namespace phpCollab\Updates;

use phpCollab\Database;

class Updates
{
    protected $updates_gateway;
    protected $db;

    /**
     * Updates constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->updates_gateway = new UpdatesGateway($this->db);
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
