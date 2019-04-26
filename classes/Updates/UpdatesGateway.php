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

    /**
     * @param $type
     * @param $item
     * @param $member
     * @param $comments
     * @return string
     */
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

    /**
     * @param $type
     * @param $taskId
     * @param null $sorting
     * @return mixed
     */
    public function getUpdates($type, $taskId, $sorting = null)
    {
        if (is_null($sorting)) {
            $sorting = 'upd.created DESC';
        }
        $whereStatement = " WHERE upd.type=:type AND upd.item = :task_id";

        $this->db->query($this->initrequest["updates"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':type', $type);
        $this->db->bind(':task_id', $taskId);

        return $this->db->resultset();

    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["upd.created"];
            $pieces = explode(' ', $sorting);

            if ($pieces) {
                $key = array_search($pieces[0], $allowedOrderedBy);

                if ($key !== false) {
                    $order = $allowedOrderedBy[$key];
                    return " ORDER BY $order $pieces[1]";
                }
            }
        }

        return '';
    }


}
