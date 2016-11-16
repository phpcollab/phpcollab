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

    public function getAllMembers() {
        $data = $this->members_gateway->getAllMembers();

        return $data;
    }

}