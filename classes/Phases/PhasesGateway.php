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
     * @param $phaseNum
     * @return mixed
     */
    public function getPhasesByProjectIdAndPhaseOrderNum($projectId, $phaseNum)
    {
        $whereStatement = " WHERE pha.project_id = :project_id AND pha.order_num = :phase_number";
        $this->db->query($this->initrequest["phases"] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':phase_number', $phaseNum);
        $results = $this->db->resultset();
        return $results;
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getPhasesByProjectIdAndIsCompleted($projectId)
    {
        $whereStatement = " WHERE pha.project_id = :project_id AND status = 1";
        $this->db->query($this->initrequest["phases"] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        $results = $this->db->resultset();
        return $results;

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
        $results = $this->db->resultset();
        return $results;
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

}