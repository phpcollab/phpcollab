<?php


namespace phpCollab\Teams;

use phpCollab\Database;

class Teams
{
    protected $teams_gateway;
    protected $db;

    /**
     * Teams constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->teams_gateway = new TeamsGateway($this->db);
    }

    public function getTeamByProjectIdAndTeamMember($projectId, $teamMember)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        $team = $this->teams_gateway->getTeamByProjectIdAndTeamMember($projectId, $teamMember);

        return $team;
    }
}