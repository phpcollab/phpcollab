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
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->phases_gateway = new PhasesGateway($this->db);
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
        return $this->phases_gateway->addPhase($projectId, $orderNumber, $status, $name);
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

        return $this->phases_gateway->getPhasesByProjectIdAndPhaseOrderNum($projectId, $phaseNum);
    }

    /**
     * @param $phaseId
     * @return mixed
     */
    public function getPhasesById($phaseId)
    {
        $phaseId = filter_var($phaseId, FILTER_VALIDATE_INT);

        return $this->phases_gateway->getPhaseById($phaseId);
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getPhasesByProjectIdAndIsCompleted($projectId, $sorting = null)
    {
        return $this->phases_gateway->getPhasesByProjectIdAndIsCompleted($projectId, $sorting);
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
        return $this->phases_gateway->updatePhase($phaseId, $status, $startDate, $endDate, $comments);
    }

}
