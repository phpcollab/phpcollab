<?php


namespace phpCollab\Phases;

use phpCollab\Database;

class PhasesGateway
{
    protected $db;
    protected $initrequest;

    /**
     * PhasesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    public function getPhasesByProjectIdAndPhaseOrderNum($projectId, $phaseNum)
    {
        $whereStatement = " WHERE pha.project_id = :project_id AND pha.order_num = :phase_number";
        $this->db->query($this->initrequest["phases"] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':phase_number', $phaseNum);
        $results = $this->db->resultset();
        return $results;
    }

}