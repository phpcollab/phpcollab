<?php


namespace phpCollab\Teams;

use phpCollab\Database;

/**
 * Class Teams
 * @package phpCollab\Teams
 */
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

    /**
     * @param $projectId
     * @param $teamMember
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMember($projectId, $teamMember)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        $team = $this->teams_gateway->getTeamByProjectIdAndTeamMember($projectId, $teamMember);

        return $team;
    }

    /**
     * @param $teamMember
     * @param $orgId
     * @return mixed
     */
    public function getTeamByTeamMemberAndOrgId($teamMember, $orgId)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        $team = $this->teams_gateway->getTeamByTeamMemberAndOrgId($teamMember, $orgId);

        return $team;
    }

    /**
     * @param $projectId
     * @param null $orderBy
     * @return mixed
     */
    public function getTeamByProjectIdAndOrderedBy($projectId, $orderBy = null)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        if (isset($orderBy)) {
            $orderBy = filter_var($orderBy, FILTER_SANITIZE_STRING);
        }

        $team = $this->teams_gateway->getTeamByProjectIdAndOrderBy($projectId, $orderBy);

        return $team;
    }

    /**
     * @param $projectId
     * @param $memberId
     * @return bool
     */
    public function isTeamMember($projectId, $memberId)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        return (count($this->teams_gateway->isTeamMember($projectId, $memberId)) > 0) ? true : false;
    }

}
