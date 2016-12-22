<?php


namespace phpCollab\Phases;

use phpCollab\Database;

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

    public function getPhasesByProjectIdAndPhaseOrderNum($projectId, $phaseNum)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $phaseNum = filter_var($phaseNum, FILTER_VALIDATE_INT);

        $phases = $this->phases_gateway->getPhasesByProjectIdAndPhaseOrderNum($projectId, $phaseNum);

        return $phases;
    }

}