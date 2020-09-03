<?php


namespace phpCollab\Phases;

use phpCollab\Database;

/**
 * Class PhasesGateway
 * @package phpCollab\Phases
 */
class PhasesGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * PhasesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $projectId
     * @param $orderNumber
     * @param $status
     * @param $name
     * @return string
     */
    public function addPhase($projectId, $orderNumber, $status, $name)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["phases"]} (
project_id, order_num, status, name
) VALUES (
:project_id, :order_num, :status, :name)
SQL;
        $this->db->query($sql);
        $this->db->bind(":project_id", $projectId);
        $this->db->bind(":order_num", $orderNumber);
        $this->db->bind(":status", $status);
        $this->db->bind(":name", $name);
        $this->db->execute();
        return $this->db->lastInsertId();
    }


    /**
     * @param $projectId
     * @param $phaseNum
     * @return mixed
     */
    public function getPhasesByProjectIdAndPhaseOrderNum($projectId, $phaseNum)
    {
        $whereStatement = " WHERE pha.project_id = :project_id AND pha.order_num = :phase_number";
        $this->db->query($this->initrequest["phases"] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':phase_number', $phaseNum);
        return $this->db->single();
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getPhasesByProjectIdAndIsCompleted($projectId, $sorting = null)
    {
        $whereStatement = " WHERE pha.project_id = :project_id AND status = 1";
        $this->db->query($this->initrequest["phases"] . $whereStatement . $this->orderBy($sorting));
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();

    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getPhasesByProjectId($projectId, $sorting = null)
    {
        $whereStatement = " WHERE pha.project_id = :project_id";
        $this->db->query($this->initrequest["phases"] . $whereStatement . $this->orderBy($sorting));
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();

    }

    /**
     * @param $phaseId
     * @return mixed
     */
    public function getPhaseById($phaseId)
    {
        $whereStatement = " WHERE pha.id = :phase_id";
        $this->db->query($this->initrequest["phases"] . $whereStatement);
        $this->db->bind(':phase_id', $phaseId);
        return $this->db->single();
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deletePhasesByProjectId($projectIds)
    {
        $projectIds = explode(',', $projectIds);
        $placeholders = str_repeat('?, ', count($projectIds) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['phases']} WHERE project_id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectIds);
    }

    /**
     * @param $phaseId
     * @param $status
     * @param $startDate
     * @param $endDate
     * @param $comments
     * @return mixed
     */
    public function updatePhase($phaseId, $status, $startDate, $endDate, $comments)
    {
        $query = "UPDATE {$this->tableCollab["phases"]} SET status = :status, date_start = :date_start, date_end = :date_end, comments = :comments WHERE id = :phase_id";
        $this->db->query($query);
        $this->db->bind(":phase_id", $phaseId);
        $this->db->bind(":status", $status);
        $this->db->bind(":date_start", $startDate);
        $this->db->bind(":date_end", $endDate);
        $this->db->bind(":comments", $comments);
        return $this->db->execute();
    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["pha.order_num", "pha.name", "pha.status", "pha.date_start", "pha.date_end"];
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
