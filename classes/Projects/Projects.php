<?php
namespace phpCollab\Projects;

use phpCollab\Database;

/**
 * Class Projects
 * @package phpCollab\Projects
 */
class Projects
{
    protected $projects_gateway;
    protected $db;

    /**
     * Projects constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->projects_gateway = new ProjectsGateway($this->db);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getProjectById($projectId)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);

        return $this->projects_gateway->getProjectById($projectId);
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getProjectsByOwner($ownerId, $sorting)
    {
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->projects_gateway->getAllByOwner($ownerId, $sorting);
    }
}