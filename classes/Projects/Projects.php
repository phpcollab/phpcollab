<?php

//TODO: Refactor projects to use this, instead of the gateway

namespace phpCollab\Projects;


class Projects
{
    protected $projects_gateway;

    public function __construct(ProjectsGateway $projects_gateway)
    {
        $this->projects_gateway = $projects_gateway;
    }

    public function getProjectsByOwner($ownerId)
    {
        $rows = $this->projects_gateway->getAllByOwner($ownerId);
        return $rows;
    }
}