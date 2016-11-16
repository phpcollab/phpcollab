<?php

//TODO: Refactor projects to use this, instead of the gateway

namespace phpCollab\Projects;

use phpCollab\Database;

class Projects
{
    protected $projects_gateway;
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->projects_gateway = new ProjectsGateway($this->db);
    }

    public function getProjectsByOwner($ownerId, $sorting)
    {
        return $this->projects_gateway->getAllByOwner($ownerId, $sorting);
    }
}