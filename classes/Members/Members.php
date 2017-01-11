<?php

namespace phpCollab\Members;

use phpCollab\Database;

class Members
{
    protected $members_gateway;
    protected $db;

    public function __construct()
    {
        $this->db = new Database();

        $this->members_gateway = new MembersGateway($this->db);
    }

    public function getMemberByLogin($memberLogin) {
        $data = $this->members_gateway->getMemberByLogin($memberLogin);

        return $data;
    }

    public function getMemberById($memberId) {
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);

        $data = $this->members_gateway->getMemberById($memberId);

        return $data;
    }

    public function getMembersByOrg($orgId, $sorting) {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        $data = $this->members_gateway->getAllByOrg($orgId, $sorting);

        return $data;
    }

    public function getAllMembers() {
        $data = $this->members_gateway->getAllMembers();

        return $data;
    }

    public function deleteMemberByOrgId($orgId)
    {
        $orgId = filter_var($orgId, FILTER_SANITIZE_STRING);
        return $this->members_gateway->deleteMember($orgId);
    }

}