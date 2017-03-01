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
     * @param $ownerId
     * @return mixed
     */
    public function getSortingValues($ownerId)
    {
        echo 'SortingGateway getSortingValues';
        $whereStatement = "WHERE sor.member = :owner_id";

        $this->db->query($this->initrequest["sorting"] . $whereStatement);

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @param $ownerId
     */
    public function updateSortingValues($ownerId)
    {
//        $sql = "UPDATE sorting SET $sor_cible='$sor_champs $sor_ordre' WHERE member = '$idSession'";
//
//        $this->db->query($this->initrequest["sorting"] . $whereStatement);
//
//        $this->db->bind(':owner_id', $ownerId);
//
//        return $this->db->resultset();
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