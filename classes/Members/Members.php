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

    public function getAllMembers() {
        $data = $this->members_gateway->getAllMembers();

        return $data;
    }

}