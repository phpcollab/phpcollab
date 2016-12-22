<?php
namespace phpCollab\Projects;

use phpCollab\Database;

/**
 * User: mindblender
 * Date: 10/11/16
 * Time: 10:33 PM
 */
class ProjectsGateway
{
    protected $db;
    protected $projectsFilter;
    protected $initrequest;

    protected $stmt = <<<SQL
SELECT 
tea.id AS tea_id, 
tea.project AS tea_project, 
tea.member AS tea_member, 
tea.published AS tea_published, 
tea.authorized AS tea_authorized, 
mem.id AS tea_mem_id, 
mem.login AS tea_mem_login, 
mem.name AS tea_mem_name, 
mem.email_work AS tea_mem_email_work, 
mem.title AS tea_mem_title, 
mem.phone_work AS tea_mem_phone_work, 
mem.profil AS tea_mem_profile, 
org.name AS tea_org_name, 
pro.id AS tea_pro_id, 
pro.name AS tea_pro_name, 
pro.priority AS tea_pro_priority, 
pro.status AS tea_pro_status, 
pro.published AS tea_pro_published, 
org2.name AS tea_org2_name, 
org2.id AS tea_org2_id, 
mem2.id AS tea_mem2_id, 
mem2.login AS tea_mem2_login, 
mem2.email_work AS tea_mem2_email_work, 
log.connected AS tea_log_connected
FROM teams tea
LEFT OUTER JOIN members mem ON mem.id = tea.member
LEFT OUTER JOIN projects pro ON pro.id = tea.project
LEFT OUTER JOIN organizations org ON org.id = mem.organization
LEFT OUTER JOIN organizations org2 ON org2.id = pro.organization
LEFT OUTER JOIN members mem2 ON mem2.id = pro.owner
LEFT OUTER JOIN logs log ON log.login = mem.login
SQL;

    /**
     * Reports constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->projectsFilter = $GLOBALS['projectsFilter']; // TODO: refactor this
        $this->initrequest = $GLOBALS['initrequest'];
    }

    /**
     * Returns a list of projects owned by ownerId
     * @param $ownerId
     * @param $sorting
     * @param $inactive
     * @return dataset
     */
    public function getAllByOwner($ownerId, $sorting)
    {
        if (!is_null($sorting)) {
            $sortQry = 'ORDER BY ' . $sorting;
        } else {
            $sortQry = '';
        }

        $this->db->query($this->stmt . ' WHERE tea.member = :owner_id AND pro.status IN(2,3) ' . $sortQry);

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @param $typeProjects
     * @return Project List
     */
    public function getProjectList( $ownerId, $typeProjects, $sorting )
    {
        if ($typeProjects == "inactive") {
            if ($this->projectsFilter == "true") {
                $tmpQuery = "LEFT OUTER JOIN teams ON teams.project = pro.id ";
                $tmpQuery .= " WHERE pro.status IN(0,1,4) AND teams.member = :owner_id";
            } else {
                $tmpQuery = "WHERE pro.status IN(0,1,4)";
            }
        } else if ($typeProjects == "active") {
            if ($this->projectsFilter == "true") {
                $tmpQuery = "LEFT OUTER JOIN teams teams ON teams.project = pro.id ";
                $tmpQuery .= "WHERE pro.status IN(2,3) AND teams.member = :owner_id";
            } else {
                $tmpQuery = "WHERE pro.status IN(2,3)";
            }
        }

        if (!is_null($sorting)) {
            $sortQry = 'ORDER BY ' . $sorting;
        } else {
            $sortQry = '';
        }

        $query = $this->initrequest["projects"] . ' ' . $tmpQuery . ' ' . $sortQry;

        $this->db->query($query);

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    public function getProjectById($projectId)
    {
        $whereStatement = ' WHERE pro.id = :project_id';
        $this->db->query($this->initrequest['projects'] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        return $this->db->single();
    }

}
