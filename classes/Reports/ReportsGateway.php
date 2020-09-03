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
     * @param $owner
     * @param $name
     * @param $projects
     * @param $clients
     * @param $members
     * @param $priorities
     * @param $status
     * @param $dateDueStart
     * @param $dateDueEnd
     * @param $dateCompleteStart
     * @param $dateCompleteEnd
     * @param $created
     * @return string
     */
    public function addReport(
        $owner,
        $name,
        $projects,
        $clients,
        $members,
        $priorities,
        $status,
        $dateDueStart,
        $dateDueEnd,
        $dateCompleteStart,
        $dateCompleteEnd,
        $created
    ) {
        $sql = "INSERT INTO {$this->tableCollab["reports"]} (owner,name,projects,clients,members,priorities,status,date_due_start,date_due_end,date_complete_start,date_complete_end,created) VALUES(:owner,:name,:projects,:clients,:members,:priorities,:status,:date_due_start,:date_due_end,:date_complete_start,:date_complete_end,:created)";
        $this->db->query($sql);
        $this->db->bind(":owner", $owner);
        $this->db->bind(":name", $name);
        $this->db->bind(":projects", $projects);
        $this->db->bind(":clients", $clients);
        $this->db->bind(":members", $members);
        $this->db->bind(":priorities", $priorities);
        $this->db->bind(":status", $status);
        $this->db->bind(":date_due_start", $dateDueStart);
        $this->db->bind(":date_due_end", $dateDueEnd);
        $this->db->bind(":date_complete_start", $dateCompleteStart);
        $this->db->bind(":date_complete_end", $dateCompleteEnd);
        $this->db->bind(":created", $created);
        $this->db->execute();
        return $this->db->lastInsertId();
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
     * @param mixed $reportIds
     * @param null $sorting
     * @return mixed
     */
    public function getReportsByIds($reportIds, $sorting = null)
    {
        $reportIds = explode(',', $reportIds);
        $placeholders = str_repeat('?, ', count($reportIds) - 1) . '?';
        $sql = $this->initrequest["reports"] . " WHERE rep.id IN({$placeholders}) " . $this->orderBy($sorting);
        $this->db->query($sql);
        $this->db->execute($reportIds);
        return $this->db->resultset();


    }

    /**
     * @param $reportId
     * @return mixed
     */
    public function deleteReports($reportId)
    {
        $reportId = explode(',', $reportId);
        $placeholders = str_repeat('?, ', count($reportId) - 1) . '?';
        $query = "DELETE FROM {$this->tableCollab["reports"]} WHERE id IN ($placeholders)";
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
            $allowedOrderedBy = [
                "rep.id",
                "rep.owner",
                "rep.name",
                "rep.projects",
                "rep.members",
                "rep.priorities",
                "rep.status",
                "rep.date_due_start",
                "rep.date_due_end",
                "rep.created",
                "rep.date_complete_start",
                "rep.date_complete_end",
                "rep.clients"
            ];
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
