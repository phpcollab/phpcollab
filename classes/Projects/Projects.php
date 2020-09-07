<?php

namespace phpCollab\Projects;

use InvalidArgumentException;
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
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->projects_gateway = new ProjectsGateway($this->db);
    }

    /**
     * @param $name
     * @param $organization
     * @param $owner
     * @param $priority
     * @param $status
     * @param $description
     * @param $published
     * @param int $phase
     * @param int $maxUploadSize
     * @param null $urlDev
     * @param null $urlProd
     * @param int $invoicing
     * @param float $hourlyRate
     * @param null $modified
     * @return string
     */
    public function createProject(
        $name,
        $organization,
        $owner,
        $priority,
        $status,
        $description,
        $published,
        $phase = 0,
        $maxUploadSize = 2048000,
        $urlDev = null,
        $urlProd = null,
        $invoicing = 0,
        $hourlyRate = 0.00,
        $modified = null
    ) {
        $created = date('Y-m-d h:i');

        $modified = (is_null($modified)) ? date('Y-m-d h:i') : $modified;

        return $this->projects_gateway->createProject($name, $organization, $owner, $priority, $status, $description,
            $published,
            $phase, $maxUploadSize, $urlDev, $urlProd, $invoicing, $hourlyRate, $modified, $created);
    }

    /**
     * @param $id
     * @param $name
     * @param $organization
     * @param $owner
     * @param $priority
     * @param $status
     * @param $description
     * @param $published
     * @param int $phase
     * @param int $maxUploadSize
     * @param null $urlDev
     * @param null $urlProd
     * @param int $invoicing
     * @param float $hourlyRate
     * @param null $modified
     * @return mixed
     */
    public function updateProject(
        $id,
        $name,
        $organization,
        $owner,
        $priority,
        $status,
        $description,
        $published,
        $phase = 0,
        $maxUploadSize = 2048000,
        $urlDev = null,
        $urlProd = null,
        $invoicing = 0,
        $hourlyRate = 0.00,
        $modified = null
    ) {
        $modified = (is_null($modified)) ? date('Y-m-d h:i') : $modified;

        return $this->projects_gateway->updateProject($id, $name, $organization, $owner, $priority, $status,
            $description, $published,
            $phase, $maxUploadSize, $urlDev, $urlProd, $invoicing, $hourlyRate, $modified);
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

    /**
     * @param $query
     * @param $userId
     * @param null $sorting
     * @param null $limit
     * @param null $rowLimit
     * @return mixed
     */
    public function searchProjects(string $query, $userId, $sorting = null, $limit = null, $rowLimit = null)
    {
        return $this->projects_gateway->searchProjects($query, $userId, $sorting, $limit, $rowLimit);
    }

    /**
     * @param int $projectId
     * @param bool $flag
     * @return mixed
     */
    public function publishProject(int $projectId, bool $flag = false)
    {
        if (!is_int(filter_var($projectId, FILTER_VALIDATE_INT))) {
            throw new InvalidArgumentException('Project ID is missing or invalid.');
        }
        return $this->projects_gateway->publishProject($projectId, $flag);
    }
}
