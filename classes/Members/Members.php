<?php

namespace phpCollab\Members;

use phpCollab\Database;

/**
 * Class Members
 * @package phpCollab\Members
 */
class Members
{
    protected $members_gateway;
    protected $db;

    /**
     * Members constructor.
     */
    public function __construct()
    {
        $this->db = new Database();

        $this->members_gateway = new MembersGateway($this->db);
    }

    /**
     * @param $memberLogin
     * @return mixed
     */
    public function getMemberByLogin($memberLogin) {
        $data = $this->members_gateway->getMemberByLogin($memberLogin);

        return $data;
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getMemberById($memberId) {
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);

        $data = $this->members_gateway->getMemberById($memberId);

        return $data;
    }

    /**
     * @param $memberIds
     * @return mixed
     */
    public function getNonClientMembersExcept($memberIds)
    {
        $memberIds = filter_var($memberIds, FILTER_SANITIZE_STRING);
        return $this->members_gateway->getNonClientMembersNotIn($memberIds);

    }

    /**
     * @param $memberIds
     * @return mixed
     */
    public function getMembersByIdIn($memberIds) {
        $memberIds = filter_var($memberIds, FILTER_SANITIZE_STRING);
        return $this->members_gateway->getMembersIn($memberIds);
    }

    /**
     * @param $orgId
     * @param $sorting
     * @return mixed
     */
    public function getMembersByOrg($orgId, $sorting) {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        $data = $this->members_gateway->getAllByOrg($orgId, $sorting);

        return $data;
    }

    /**
     * @param $orgId
     * @param null $membersTeam
     * @param null $sorting
     * @return mixed
     */
    public function getClientMembersByOrgIdAndNotInTeam($orgId, $membersTeam = null, $sorting = null) {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $membersTeam = filter_var($membersTeam, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        return $this->members_gateway->getClientMembersByOrgIdAndNotInTeam($orgId, $membersTeam, $sorting);
    }



    /**
     * @return mixed
     */
    public function getAllMembers() {
        $data = $this->members_gateway->getAllMembers();

        return $data;
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function deleteMemberByOrgId($orgId)
    {
        $orgId = filter_var($orgId, FILTER_SANITIZE_STRING);
        return $this->members_gateway->deleteMember($orgId);
    }

    /**
     * @param $memberIds
     * @return mixed
     */
    public function deleteMemberByIdIn($memberIds)
    {
        return $this->members_gateway->deleteMemberByIdIn($memberIds);
    }

    public function setLastPageVisited($userId, $page)
    {
        return $this->members_gateway->setLastPageVisited($userId, $page);
    }
}
