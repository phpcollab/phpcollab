<?php


namespace phpCollab\Phases;

use phpCollab\Database;

/**
 * Class Phases
 * @package phpCollab\Phases
 */
class Phases
{
    protected $phases_gateway;
    protected $db;

    /**
     * Phases constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->phases_gateway = new PhasesGateway($this->db);
    }

    /**
     * @param $projectId
     * @param $phaseNum
     * @return mixed
     */
    public function getPhasesByProjectIdAndPhaseOrderNum($projectId, $phaseNum)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $phaseNum = filter_var($phaseNum, FILTER_VALIDATE_INT);

        $phases = $this->phases_gateway->getPhasesByProjectIdAndPhaseOrderNum($projectId, $phaseNum);

        return $phases;
    }

    /**
     * @param $phaseId
     * @return mixed
     */
    public function getPhasesById($phaseId)
    {
        $phaseId = filter_var($phaseId, FILTER_VALIDATE_INT);

        $phase = $this->phases_gateway->getPhaseById($phaseId);

        return $phase;
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getPhasesByProjectIdAndIsCompleted($projectId)
    {
        return $this->phases_gateway->getPhasesByProjectIdAndIsCompleted($projectId);
    }

    /**
     * @param $projectId
     * @param $sorting
     * @return mixed
     */
    public function getPhasesByProjectId($projectId, $sorting = null)
    {
        return $this->phases_gateway->getPhasesByProjectId($projectId, $sorting);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deletePhasesByProjectId($projectIds)
    {
        return $this->phases_gateway->deletePhasesByProjectId($projectIds);
    }

}
