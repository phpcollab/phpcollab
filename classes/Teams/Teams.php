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
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function getOtherProjectTeamMembers($projectId, $memberId)
    {
        return $this->teams_gateway->getOtherProjectTeamMembers($projectId, $memberId);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getClientTeamMembersByProject($projectId)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getClientTeamMembersByProject($projectId);
    }

    /**
     * @param $projectId
     * @param $teamMember
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished($projectId, $teamMember)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        $team = $this->teams_gateway->getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished($projectId, $teamMember);

        return $team;
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getProjectSiteContacts($projectId, $sorting = null)
    {
        return $this->teams_gateway->getProjectSiteContacts($projectId, $sorting);
    }

    /**
     * @param $teamMember
     * @return mixed
     */
    public function getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished($teamMember)
    {
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished($teamMember);
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
     * @param null $offset
     * @param null $limit
     * @param null $sorting
     * @return mixed
     */
    public function getTeamByProjectId($projectId, $offset = null, $limit = null, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }

        $team = $this->teams_gateway->getTeamByProjectId($projectId, $offset, $limit, $sorting);

        return $team;
    }

    /**
     * @param $projectId
     * @return int
     */
    public function getTopicCountByProject($projectId)
    {
        $team = $this->getTeamByProjectId($projectId);
        return count($team);
    }

    /**
     * @param $memberId
     * @param null $sorting
     * @return mixed
     */
    public function getTeamByMemberId($memberId, $sorting = null)
    {
        return $this->teams_gateway->getTeamByMemberId($memberId, $sorting);
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getTeamsImAMemberOf($memberId)
    {
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getTeamsImAMemberOf($memberId);
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
        return (count($this->teams_gateway->isTeamMember($projectId, $memberId)) > 0) ? "true" : "false";
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
     * @param $memberId
     * @param $published
     * @param $authorized
     * @return mixed
     */
    public function addTeam($projectId, $memberId, $published, $authorized)
    {
        return $this->teams_gateway->addTeam($projectId, $memberId, $published, $authorized);
    }

    /**
     * @param $projectId
     * @param $memberIds
     * @return mixed
     */
    public function publishToSite($projectId, $memberIds)
    {
        return $this->teams_gateway->publishToSite($projectId, $memberIds);
    }

    /**
     * @param $projectId
     * @param $memberIds
     * @return mixed
     */
    public function unPublishToSite($projectId, $memberIds)
    {
        return $this->teams_gateway->unPublishToSite($projectId, $memberIds);
    }

}

