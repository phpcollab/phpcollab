<?php


namespace phpCollab\Tasks;

use phpCollab\Database;

class TaskUpdatesGateway
{
    protected $db;
    protected $initrequest;

    /**
     * TaskUpdatesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];

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
        $sql = "INSERT INTO {$this->db->getTableName("updates")} (type, item, member, comments, created) VALUES (:type, :item, :member, :comments, :created)";
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
     * @param int $type
     * @param int $taskId
     * @param string|null $sorting
     * @return mixed
     */
    public function getUpdates(int $type, int $taskId, string $sorting = null)
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
     * @param string|null $sorting
     * @return string
     */
    private function orderBy(string $sorting = null): string
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
