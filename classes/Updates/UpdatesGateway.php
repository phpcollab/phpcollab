<?php


namespace phpCollab\Updates;

use phpCollab\Database;

class UpdatesGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * UpdatesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    public function addUpdate($type, $item, $member, $comments)
    {
        $sql = "INSERT INTO {$this->tableCollab["updates"]} (type, item, member, comments, created) VALUES (:type, :item, :member, :comments, :created)";
        $this->db->query($sql);
        $this->db->bind(':type', $type);
        $this->db->bind(':item', $item);
        $this->db->bind(':member', $member);
        $this->db->bind(':comments', $comments);
        $this->db->bind(':created', date('Y-m-d h:i'));
        $this->db->execute();
        return $this->db->lastInsertId();
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
