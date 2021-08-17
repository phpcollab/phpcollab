<?php

namespace phpCollab\Projects;

use InvalidArgumentException;
use Laminas\Escaper\Escaper;
use phpCollab\Database;

/**
 * Class Projects
 * @package phpCollab\Projects
 */
class Projects
{
    protected $projects_gateway;
    protected $db;
    protected $escaper;

    /**
     * Projects constructor.
     * @param Database $database
     * @param Escaper $escaper
     */
    public function __construct(Database $database, Escaper $escaper)
    {
        $this->db = $database;
        $this->escaper = $escaper;
        $this->projects_gateway = new ProjectsGateway($this->db);
    }

    /**
     * @param string $name
     * @param int $organization
     * @param int $owner
     * @param int $priority
     * @param int $status
     * @param string $description
     * @param int $published
     * @param int $phase
     * @param int $maxUploadSize
     * @param string|null $urlDev
     * @param string|null $urlProd
     * @param int $invoicing
     * @param float $hourlyRate
     * @param string|null $modified
     * @return string
     */
    public function createProject(
        string $name,
        int $organization,
        int $owner,
        int $priority,
        int $status,
        string $description,
        int $published,
        int $phase = 0,
        int $maxUploadSize = 2048000,
        string $urlDev = null,
        string $urlProd = null,
        int $invoicing = 0,
        float $hourlyRate = 0.00,
        string $modified = null
    ): string {
        $created = date('Y-m-d h:i');

        $modified = (is_null($modified)) ? date('Y-m-d h:i') : $modified;

        return $this->projects_gateway->createProject(
            $name, $organization, $owner, $priority, $status, $description,
            $published, $phase, $maxUploadSize, $urlDev, $urlProd, $invoicing, $hourlyRate, $modified, $created
        );
    }

    /**
     * @param int $id
     * @param string $name
     * @param int $organization
     * @param int $owner
     * @param int $priority
     * @param int $status
     * @param string $description
     * @param int $published
     * @param int $phase
     * @param int $maxUploadSize
     * @param string|null $urlDev
     * @param string|null $urlProd
     * @param int $invoicing
     * @param float $hourlyRate
     * @param string|null $modified
     * @return mixed
     */
    public function updateProject(
        int $id,
        string $name,
        int $organization,
        int $owner,
        int $priority,
        int $status,
        string $description,
        int $published,
        int $phase = 0,
        int $maxUploadSize = 2048000,
        string $urlDev = null,
        string $urlProd = null,
        int $invoicing = 0,
        float $hourlyRate = 0.00,
        string $modified = null
    ) {
        $modified = (is_null($modified)) ? date('Y-m-d h:i') : $modified;

        return $this->projects_gateway->updateProject($id, $name, $organization, $owner, $priority, $status,
            $description, $published,
            $phase, $maxUploadSize, $urlDev, $urlProd, $invoicing, $hourlyRate, $modified);
    }

    /**
     * @param string|null $sorting
     * @return mixed
     */
    public function getAllProjects(string $sorting = null)
    {
        return $this->projects_gateway->getAllProjects($sorting);
    }

    /**
     * @param int $memberId
     * @param string|null $sorting
     * @return mixed
     */
    public function getFilteredProjectsByTeamMember(int $memberId, string $sorting = null)
    {
        return $this->projects_gateway->getProjectsFilteredByTeamMember($memberId, $sorting);
    }

    /**
     * @param int $projectId
     * @return mixed
     */
    public function getProjectById(int $projectId)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);

        if (empty($projectId)) {
            throw new InvalidArgumentException('Invalid project ID');
        }

        $projectDetails = $this->projects_gateway->getProjectById($projectId);

        return $this->escapeOutput($projectDetails);
    }

    /**
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getProjectByIdIn(int $projectId, string $sorting = null)
    {
        $projects = $this->projects_gateway->getProjectByIdIn($projectId, $sorting);

        if ($projects) {
            foreach ($projects as $key => $project) {
                $projects[$key] = $this->escapeOutput($project);
            }
        }

        return $projects;
    }

    /**
     * @param int $ownerId
     * @param string|null $sorting
     * @return mixed
     */
    public function getProjectsByOwner(int $ownerId, string $sorting = null)
    {
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->projects_gateway->getAllByOwner($ownerId, $sorting);
    }

    /**
     * @param int $ownerId
     * @param string|null $projectType
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $sorting
     * @return mixed
     */
    public function getProjectList(int $ownerId, string $projectType = null, int $limit = null, int $offset = null, string $sorting = null)
    {
        $projectType = filter_var($projectType, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        if (empty($projectType)) {
            $projectType = "active";
        }
        $projects =  $this->projects_gateway->getProjectList($ownerId, $projectType, $limit, $offset, $sorting);

        if ($projects) {
            foreach ($projects as $key => $project) {
                $projects[$key] = $this->escapeOutput($project);
            }
        }

        return $projects;
    }

    /**
     * @param int $orgId
     * @param string $sorting
     * @return mixed
     */
    public function getProjectsByOrganization(int $orgId, string $sorting)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        return $this->projects_gateway->getAllByOrganization($orgId, $sorting);
    }

    /**
     * @param int $orgId
     * @param int $memberId
     * @param string $sorting
     * @return mixed
     */
    public function getFilteredProjectsByOrganization(int $orgId, int $memberId, string $sorting)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        return $this->projects_gateway->getFilteredAllByOrganization($orgId, $memberId, $sorting);
    }

    /**
     * @param int $orgId
     * @return mixed
     */
    public function setDefaultOrg(int $orgId)
    {
        $orgId = filter_var($orgId, FILTER_SANITIZE_STRING);
        return $this->projects_gateway->setDefaultOrg($orgId);
    }

    /**
     * @param int $oldOwner
     * @param int $newOwner
     * @return mixed
     */
    public function reassignProject(int $oldOwner, int $newOwner)
    {
        return $this->projects_gateway->reassignProject($oldOwner, $newOwner);
    }

    /**
     * @param int $projectId
     * @return mixed
     */
    public function deleteProject(int $projectId)
    {
        return $this->projects_gateway->deleteProject($projectId);
    }

    /**
     * @param string $query
     * @param int $userId
     * @param string|null $sorting
     * @param int|null $limit
     * @param int|null $rowLimit
     * @return mixed
     */
    public function searchProjects(string $query, int $userId, string $sorting = null, int $limit = null, int $rowLimit = null)
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

    private function escapeOutput($project)
    {
        if (is_array($project)) {
            if (!empty($project["pro_name"])) {
                $project["pro_name"] = $this->escaper->escapeHtml($project["pro_name"]);
            }
            if (!empty($project["pro_org_name"])) {
                $project["pro_org_name"] = $this->escaper->escapeHtml($project["pro_org_name"]);
            }
        }

        return $project;
    }
}
