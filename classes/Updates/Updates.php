<?php


namespace phpCollab\Updates;

use phpCollab\Database;

class Updates
{
    protected $updates_gateway;
    protected $db;

    /**
     * Updates constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->updates_gateway = new UpdatesGateway($this->db);
    }

    public function addUpdate($type, $item, $member, $comments)
    {
        return $this->updates_gateway->addUpdate($type, $item, $member, $comments);
    }

    public function getUpdates($type, $taskId)
    {
        $type = filter_var($type, FILTER_VALIDATE_INT);
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        $updates = $this->updates_gateway->getUpdates($type, $taskId);
        return $updates;
    }
}
