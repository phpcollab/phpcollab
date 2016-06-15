<?php
namespace phpCollab\Reports;
use phpCollab\Database;

/**
 * User: mindblender
 * Date: 5/5/16
 * Time: 10:33 PM
 */


class ReportsGateway
{
//    protected $db, $tableCollab, $initrequest;
    protected $db;

    protected $stmt = "SELECT id, owner, name, projects, members, priorities, status, date_due_start, date_due_end, created, 
        date_complete_start, date_complete_end, clients FROM reports rep";

    /**
     * Reports constructor.
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
//        global $tableCollab,
//               $initrequest;
//
//        $this->tableCollab = $tableCollab;
//        $this->initrequest = $initrequest;
//        $this->db = new \phpCollab\Database();

    }

    /**
     * Returns a list of reports owned by ownerId
     * @param $ownerId
     * @param $sorting
     * @return dataset
     */
    public function getAllByOwner($ownerId, $sorting)
    {
        // Todo: I'm sure this allows SQL injection.  How do I fix it?
        if (!is_null($sorting)) {
            $sortQry = 'ORDER BY ' . $sorting;
        } else {
            $sortQry = '';
        }

        $this->db->query($this->stmt . ' WHERE rep.owner = :owner_id ' . $sortQry);

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    public function getReportById($reportId)
    {
        $this->db->query($this->stmt . ' WHERE rep.id = :report_id ');

        $this->db->bind(':report_id', $reportId);

        return $this->db->resultset();
    }

}
