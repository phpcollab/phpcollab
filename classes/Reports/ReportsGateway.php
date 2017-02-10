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
     * @param null $sorting
     * @return mixed
     */
    public function getAllByOwner($ownerId, $sorting = null)
    {
        $this->db->query($this->initrequest["reports"] . ' WHERE rep.owner = :owner_id ' . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @param $reportId
     * @return mixed
     */
    public function getReportById($reportId)
    {
        $this->db->query($this->initrequest["reports"] . ' WHERE rep.id = :report_id ');

        $this->db->bind(':report_id', $reportId);

        return $this->db->single();
    }

    /**
     * @param $reportId
     * @return mixed
     */
    public function deleteReports($reportId)
    {
        $reportId = explode(',', $reportId);
        $placeholders = str_repeat('?, ', count($reportId) - 1) . '?';
        $query = "DELETE FROM " . $this->tableCollab["reports"] . " WHERE id IN (".$placeholders.")";
        $this->db->query($query);
        return $this->db->execute($reportId);
    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["rep.id","rep.owner","rep.name","rep.projects","rep.members","rep.priorities","rep.status","rep.date_due_start","rep.date_due_end","rep.created","rep.date_complete_start","rep.date_complete_end","rep.clients"];
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
