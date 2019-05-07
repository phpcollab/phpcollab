<?php

namespace phpCollab\Sorting;

use phpCollab\Database;

/**
 * Class SortingGateway
 * @package phpCollab\Sorting
 */
class SortingGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * Reports constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function addMember($userId)
    {
        $queruy = "INSERT INTO {$this->tableCollab["sorting"]} (member) VALUES (:user_id)";
        $this->db->query($queruy);
        $this->db->bind(":user_id", $userId);
        return $this->db->execute();
    }

    /**
     * @param $ownerId
     * @return mixed
     */
    public function getSortingValues($ownerId)
    {
        $whereStatement = "WHERE sor.member = :owner_id";

        $this->db->query($this->initrequest["sorting"] . $whereStatement);

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->single();
    }

    /**
     * @param $ownerId
     */
    public function updateSortingValues($ownerId)
    {
    }

    /**
     * @param $target
     * @param $value
     * @param $userId
     * @return mixed
     */
    public function updateSortingTargetByUserId($target, $value, $userId)
    {
        $query = <<<SQL
UPDATE {$this->tableCollab["sorting"]} 
SET {$target} = :sort_value
WHERE member = :user_id
SQL;

        $this->db->query($query);

        $this->db->bind(':sort_value', $value);
        $this->db->bind(':user_id', $userId);

        return $this->db->execute();
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function deleteByMember($memberId)
    {
        $memberId = explode(',', $memberId);
        $placeholders = str_repeat ('?, ', count($memberId)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['sorting']} WHERE member IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($memberId);
    }

}
