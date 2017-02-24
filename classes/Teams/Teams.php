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
     * @param $memberId
     * @return mixed
     */
    public function getTeamByMemberId($memberId)
    {
        return $this->teams_gateway->getTeamByMemberId($memberId);
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

    /**
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function deleteFromTeamsByProjectIdAndMemberId($projectId, $memberId)
    {
        return $this->teams_gateway->deleteFromTeamsWhereProjectIdEqualsAndMemberIdIn($projectId, $memberId);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteFromTeamsByProjectId($projectIds)
    {
        return $this->teams_gateway->deleteFromTeamsWhereProjectIdIn($projectIds);
    }

    /**
     * @param $memberIds
     * @return mixed
     */
    public function deleteTeamWhereMemberIn($memberIds)
    {
        return $this->teams_gateway->deleteFromTeamsWhereMemberIdIn($memberIds);
    }

    /**
     * @param $projectId
     * @param $memberIds
     * @return mixed
     */
    public function publishToSite($projectId, $memberIds)
    {
        return $this->publishToSite($projectId, $memberIds);
    }

    /**
     * @param $projectId
     * @param $memberIds
     * @return mixed
     */
    public function unPublishToSite($projectId, $memberIds)
    {
        return $this->unPublishToSite($projectId, $memberIds);
    }

}
