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
     * @param int $type
     * @param int $item
     * @param int $member
     * @param string $comments
     * @return string
     */
    public function addUpdate(int $type, int $item, int $member, string $comments): string
    {
        return $this->updates_gateway->addUpdate($type, $item, $member, $comments);
    }

    /**
     * @param int $type
     * @param int $taskId
     * @param string|null $sorting
     * @return mixed
     */
    public function getUpdates(int $type, int $taskId, string $sorting = null)
    {
        $type = filter_var($type, FILTER_VALIDATE_INT);
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        return $this->updates_gateway->getUpdates($type, $taskId, $sorting);
    }
}
