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
     * @internal param $inactive
     */
    public function getAllByOwner($ownerId, $sorting)
    {
        if (!is_null($sorting)) {
            $sortQry = 'ORDER BY :order_by';
        } else {
            $sortQry = '';
        }

        $this->db->query($this->initrequest['teams'] . ' WHERE tea.member = :owner_id AND pro.status IN(2,3) ' . $sortQry);

        $this->db->bind(':owner_id', $ownerId);

        if (!is_null($sorting)) {
            $this->db->bind(':order_by', $sorting);
        }

        return $this->db->resultset();
    }

    /**
     * @param $ownerId
     * @param $typeProjects
     * @param $sorting
     * @return
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
            $sortQry = 'ORDER BY :order_by';
        } else {
            $sortQry = '';
        }

        $query = $this->initrequest["projects"] . ' ' . $tmpQuery . ' ' . $sortQry;

        $this->db->query($query);

        $this->db->bind(':owner_id', $ownerId);

        if (!is_null($sorting)) {
            $this->db->bind(':order_by', $sorting);
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

}
