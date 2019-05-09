<?php
namespace phpCollab\Projects;

use phpCollab\Database;


/**
 * Class ProjectsGateway
 * @package phpCollab\Projects
 */
class ProjectsGateway
{
    protected $db;
    protected $projectsFilter;
    protected $initrequest;
    protected $tableCollab;

    /**
     * Reports constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->projectsFilter = $GLOBALS['projectsFilter'];
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $name
     * @param $organization
     * @param $owner
     * @param $priority
     * @param $status
     * @param $description
     * @param $published
     * @param $phase
     * @param $maxUploadSize
     * @param $urlDev
     * @param $urlProd
     * @param $invoicing
     * @param $hourlyRate
     * @param $modified
     * @param $created
     * @return string
     */
    public function createProject($name, $organization, $owner, $priority, $status, $description, $published,
                                  $phase, $maxUploadSize, $urlDev, $urlProd, $invoicing, $hourlyRate, $modified, $created)
    {

        $sql = <<<SQL
INSERT INTO {$this->tableCollab["projects"]} 
(name, priority, description, owner, organization, status, published, upload_max, url_dev, url_prod, phase_set, 
invoicing, hourly_rate, modified, created) 
VALUES 
(:name, :priority, :description, :owner, :organization, :status, :published, :upload_max, :url_dev, :url_prod,
:phase_set, :invoicing, :hourly_rate, :modified, :created)
SQL;

        $this->db->query($sql);

        $this->db->bind(":name", $name);
        $this->db->bind(":priority", $priority);
        $this->db->bind(":description", $description);
        $this->db->bind(":owner", $owner);
        $this->db->bind(":organization", $organization);
        $this->db->bind(":status", $status);
        $this->db->bind(":published", $published);
        $this->db->bind(":upload_max", $maxUploadSize);
        $this->db->bind(":url_dev", $urlDev);
        $this->db->bind(":url_prod", $urlProd);
        $this->db->bind(":phase_set", $phase);
        $this->db->bind(":invoicing", $invoicing);
        $this->db->bind(":hourly_rate", $hourlyRate);
        $this->db->bind(":modified", $modified);
        $this->db->bind(":created", $created);

        $this->db->execute();
        return $this->db->lastInsertId();
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
     * @param $phase
     * @param $maxUploadSize
     * @param $urlDev
     * @param $urlProd
     * @param $invoicing
     * @param $hourlyRate
     * @param $modified
     * @return mixed
     */
    public function updateProject($id, $name, $organization, $owner, $priority, $status, $description, $published,
                                  $phase, $maxUploadSize, $urlDev, $urlProd, $invoicing, $hourlyRate, $modified)
    {
        $sql = <<<SQL
UPDATE {$this->tableCollab["projects"]}
SET
    name = :name,
    priority = :priority,
    description = :description, 
    owner = :owner, 
    organization = :organization, 
    status = :status, 
    published = :published, 
    phase_set = :phase, 
    upload_max = :upload_max, 
    url_dev = :url_dev, 
    url_prod = :url_prod, 
    invoicing = :invoicing, 
    hourly_rate = :hourly_rate,
    modified = :modified
WHERE id = :project_id
SQL;
        $this->db->query($sql);
        $this->db->bind(":project_id", $id);
        $this->db->bind(":name", $name);
        $this->db->bind(":priority", $priority);
        $this->db->bind(":description", $description);
        $this->db->bind(":owner", $owner);
        $this->db->bind(":organization", $organization);
        $this->db->bind(":status", $status);
        $this->db->bind(":published", $published);
        $this->db->bind(":phase", $phase);
        $this->db->bind(":upload_max", $maxUploadSize);
        $this->db->bind(":url_dev", $urlDev);
        $this->db->bind(":url_prod", $urlProd);
        $this->db->bind(":invoicing", $invoicing);
        $this->db->bind(":hourly_rate", $hourlyRate);
        $this->db->bind(":modified", $modified);

        return $this->db->execute();
    }
    
    /**
     * @param null $sorting
     * @return mixed
     */
    public function getAllProjects($sorting = null)
    {
        $this->db->query($this->initrequest['projects'] . $this->orderBy($sorting));
        return $this->db->resultset();
    }

    /**
     * @param $memberId
     * @param $sorting
     * @return mixed
     */
    public function getProjectsFilteredByTeamMember($memberId, $sorting)
    {
        $tmpquery = " LEFT OUTER JOIN " . $this->tableCollab["teams"] . " teams ON teams.project = pro.id";
        $tmpquery .= " WHERE teams.member = :member_id";

        $sql = $this->initrequest['projects'] . $tmpquery . $this->orderBy($sorting);
        $this->db->query($sql);

        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * Returns a list of projects owned by ownerId
     * @param $ownerId
     * @param $sorting
     * @internal param $inactive
     * @return mixed
     */
    public function getAllByOwner($ownerId, $sorting = null)
    {
        $ids = explode(',', $ownerId);
        $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
        $whereStatement = " WHERE pro.owner IN($placeholders) ";
        $this->db->query($this->initrequest["projects"] . $whereStatement . $this->orderBy($sorting));
        $this->db->execute($ids);
        return $this->db->fetchAll();
    }

    /**
     * @param $orgId
     * @param $memberId
     * @param $sorting
     * @return mixed
     */
    public function getFilteredAllByOrganization($orgId, $memberId, $sorting = null)
    {

        $tmpquery = " LEFT OUTER JOIN {$this->tableCollab["teams"]} teams ON teams.project = pro.id";
        $tmpquery .= " WHERE pro.organization = :org_id AND teams.member = :member_id";

        $this->db->query($this->initrequest['projects'] . $tmpquery . $this->orderBy($sorting));

        $this->db->bind(':org_id', $orgId);
        $this->db->bind(':member_id', $memberId);

        return $this->db->resultset();
    }

    /**
     * @param $orgId
     * @param $sorting
     * @return mixed
     */
    public function getAllByOrganization($orgId, $sorting = null)
    {

        $tmpquery = " WHERE pro.organization = :org_id";

        $this->db->query($this->initrequest['projects'] . $tmpquery . $this->orderBy($sorting));

        $this->db->bind(':org_id', $orgId);

        return $this->db->resultset();
    }

    /**
     * @param $ownerId
     * @param $typeProjects
     * @param null $limit
     * @param null $offset
     * @param $sorting
     * @return mixed
     */
    public function getProjectList($ownerId, $typeProjects = null, $limit = null, $offset = null, $sorting = null)
    {
        $tmpQuery = '';
        if ($typeProjects == "inactive") {
            if ($this->projectsFilter == "true") {
                $tmpQuery = "LEFT OUTER JOIN ". $this->tableCollab["teams"] . " ON teams.project = pro.id ";
                $tmpQuery .= " WHERE pro.status IN(1,4) AND teams.member = :owner_id";
            } else {
                $tmpQuery = "WHERE pro.status IN(1,4)";
            }
        } else if ($typeProjects == "active") {
            if ($this->projectsFilter == "true") {
                $tmpQuery = "LEFT OUTER JOIN teams " . $this->tableCollab["teams"] . " ON teams.project = pro.id ";
                $tmpQuery .= "WHERE pro.status IN(0,2,3) AND teams.member = :owner_id";
            } else {
                $tmpQuery = "WHERE pro.status IN(0,2,3)";
            }
        }

        if (isset($limit) && isset($offset)) {
            $limitQuery = ' LIMIT :offset, :limit';
        } else {
            $limitQuery = null;
        }

        $query = $this->initrequest["projects"] . ' ' . $tmpQuery . $this->orderBy($sorting) . $limitQuery;

        $this->db->query($query);

        if ($this->projectsFilter == "true") {
            $this->db->bind(':owner_id', (int)$ownerId);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->bind(':limit', (int)$limit);
            $this->db->bind(':offset', (int)$offset);
        }

        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getProjectById($projectId)
    {
        $whereStatement = ' WHERE pro.id = :project_id';
        $this->db->query($this->initrequest['projects'] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        return $this->db->single();
    }

    /**
     * @param $projectId
     * @param $sorting
     * @return mixed
     */
    public function getProjectByIdIn($projectId, $sorting)
    {
        $ids = explode(',', $projectId);
        $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
        $whereStatement = " WHERE pro.id IN($placeholders) ";
        $this->db->query($this->initrequest["projects"] . $whereStatement . $this->orderBy($sorting));
        $this->db->execute($ids);
        return $this->db->fetchAll();
    }


    /**
     * @param $orgId
     * @return mixed
     */
    public function setDefaultOrg($orgId)
    {
        $orgId = explode(',', $orgId);
        $placeholders = str_repeat('?, ', count($orgId) - 1) . '?';
        $sql = "UPDATE {$this->tableCollab['projects']} SET organization=1 WHERE organization IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($orgId);

    }

    /**
     * @param $oldOwner
     * @param $newOwner
     * @return mixed
     */
    public function reassignProject($oldOwner, $newOwner)
    {
        $data = explode(',', $oldOwner);
        $placeholders = str_repeat('?, ', count($data) - 1) . '?';
        $sql = "UPDATE {$this->tableCollab["projects"]} SET owner = ? WHERE owner IN($placeholders)";
        // Place newOwner at the beginning of array
        if (is_array($data)) {
            array_unshift($data, $newOwner);
        } else {
            $data = explode(',', $newOwner . ',' . $oldOwner);
        }
        $this->db->query($sql);
        return $this->db->execute($data);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function deleteProject($projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['projects']} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);

    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["tea.id", "tea.project", "tea.member", "tea.published", "tea.authorized", "mem.id", "mem.login", "mem.name", "mem.email_work", "mem.title", "mem.phone_work", "org.name", "pro.id", "pro.name", "pro.priority", "pro.status", "pro.published", "org2.name", "mem2.login", "mem2.email_work", "org2.id", "log.connected", "mem.profil", "mem.password"];
            $pieces = explode(' ', $sorting);

            if ($pieces) {
                $key = array_search($pieces[0], $allowedOrderedBy);

                if ($key !== false) {
                    $order = $allowedOrderedBy[$key];
                    return " ORDER BY $order $pieces[1]";
                }
            }
        }

        return '';
    }
}

