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

    // Todo: refactor to use the initrequest strings
    protected $stmt = "SELECT id, owner, name, projects, members, priorities, status, date_due_start, date_due_end, created, 
        date_complete_start, date_complete_end, clients FROM reports rep";

    /**
     * Reports constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getAllByOwner($ownerId, $sorting = null)
    {
        if (isset($sorting)) {
            $sortQry = 'ORDER BY :order_by';
        } else {
            $sortQry = '';
        }

        $this->db->query($this->stmt . ' WHERE rep.owner = :owner_id ' . $sortQry);

        $this->db->bind(':owner_id', $ownerId);
        if (isset($sorting)) {
            $this->db->bind(':order_by', $$sorting);
        }

        return $this->db->resultset();
    }

    /**
     * @param $reportId
     * @return mixed
     */
    public function getReportById($reportId)
    {
        $this->db->query($this->stmt . ' WHERE rep.id = :report_id ');

        $this->db->bind(':report_id', $reportId);

        return $this->db->resultset();
    }

}
