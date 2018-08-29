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
     * @param null $sorting
     * @return mixed
     */
    public function getAllProjects($sorting = null)
    {
        return $this->projects_gateway->getAllProjects($sorting);
    }

    /**
     * @param $memberId
     * @param null $sorting
     * @return mixed
     */
    public function getFilteredProjectsByTeamMember($memberId, $sorting = null)
    {
        return $this->projects_gateway->getProjectsFilteredByTeamMember($memberId, $sorting);
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
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getProjectByIdIn($projectId, $sorting = null)
    {
        return $this->projects_gateway->getProjectByIdIn($projectId, $sorting);
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getProjectsByOwner($ownerId, $sorting = null)
    {
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->projects_gateway->getAllByOwner($ownerId, $sorting);
    }

    /**
     * @param $ownerId
     * @param $projectType
     * @param $limit
     * @param $offset
     * @param $sorting
     * @return mixed
     */
    public function getProjectList($ownerId, $projectType, $limit = null, $offset = null, $sorting = null)
    {
        $projectType = filter_var($projectType, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        if (empty($projectType)) {
            $projectType = "active";
        }
        return $this->projects_gateway->getProjectList($ownerId, $projectType, $limit, $offset, $sorting);
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
     * @param $oldOwner
     * @param $newOwner
     * @return mixed
     */
    public function reassignProject($oldOwner, $newOwner)
    {
        return $this->projects_gateway->reassignProject($oldOwner, $newOwner);
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
