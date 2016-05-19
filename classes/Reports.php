<?php

/**
 * Created by PhpStorm.
 * User: mindblender
 * Date: 5/5/16
 * Time: 10:33 PM
 */
class Reports
{
    protected $db, $tableCollab, $initrequest;

    public function __construct()
    {
        global $tableCollab,
               $initrequest;

        $this->tableCollab = $tableCollab;
        $this->initrequest = $initrequest;
        $this->db = new Database();

    }

    /**
     * Returns a list of reports owned by ownerId
     * @param $ownerId
     * @param $sorting
     * @return dataset
     */
    public function getReportsByOwner($ownerId, $sorting)
    {
//        $db = new Database();

        // Todo: I'm sure this allows SQL injection.  How do I fix it?
        if (!is_null($sorting)) {
            $sortQry = 'ORDER BY ' . $sorting;
        } else {
            $sortQry = '';
        }

        $this->db->query($this->initrequest['reports'] . ' WHERE rep.owner = :owner_id ' . $sortQry);

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    public function getByReportId($reportId)
    {
        $this->db->query($this->initrequest['reports'] . ' WHERE rep.id = :report_id ');

        $this->db->bind(':report_id', $reportId);

        return $this->db->resultset();
    }

}