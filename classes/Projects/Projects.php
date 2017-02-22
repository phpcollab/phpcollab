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

    /**
     * @param $ownerId
     * @param $projectType
     * @param $sorting
     * @return mixed
     */
    public function getProjectList($ownerId, $projectType, $sorting)
    {
        $projectType = filter_var($projectType, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        return $this->projects_gateway->getProjectList($ownerId, $projectType, $sorting);
    }

    /**
     * @param $orgId
     * @param $sorting
     * @return mixed
     */
    public function getProjectsByOrganization($orgId, $sorting)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        return $this->projects_gateway->getAllByOrganization($orgId, $sorting);
    }

    /**
     * @param $orgId
     * @param $memberId
     * @param $sorting
     * @return mixed
     */
    public function getFilteredProjectsByOrganization($orgId, $memberId, $sorting)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        return $this->projects_gateway->getFilteredAllByOrganization($orgId, $memberId, $sorting);
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function setDefaultOrg($orgId)
    {
        $orgId = filter_var($orgId, FILTER_SANITIZE_STRING);

        return $this->projects_gateway->setDefaultOrg($orgId);
        
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function deleteProject($projectId)
    {
        return $this->projects_gateway->deleteProject($projectId);
    }
}