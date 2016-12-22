<?php


namespace phpCollab\Updates;

use phpCollab\Database;

class UpdatesGateway
{
    protected $db;
    protected $initrequest;

    /**
     * UpdatesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    public function getUpdates($type, $taskId)
    {
        $whereStatement = " WHERE upd.type=:type AND upd.item = :task_id ORDER BY upd.created DESC";

        $this->db->query($this->initrequest["updates"] . $whereStatement);

        $this->db->bind(':type', $type);
        $this->db->bind(':task_id', $taskId);

        return $this->db->resultset();

    }

}