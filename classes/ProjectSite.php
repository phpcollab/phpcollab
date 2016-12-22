<?php
/**
 * Created by mindblender.
 * User: mindblender
 * Date: 5/24/16
 * Time: 11:32 PM
 */

namespace phpCollab;

use \PDO;

/**
 * Class ProjectSite
 * @package phpCollab
 */
class ProjectSite
{
    protected $db, $tableCollab, $initrequest;

    /**
     * ProjectSite constructor.
     */
    public function __construct()
    {
        global $tableCollab,
               $initrequest;

        $this->tableCollab = $tableCollab;
        $this->initrequest = $initrequest;
        $this->db = new Database();

    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getTeamMembers($projectId)
    {
        // Todo: I'm sure this allows SQL injection.  How do I fix it?
//        if (!is_null($sorting)) {
//            $sortQry = 'ORDER BY ' . $sorting;
//        } else {
//            $sortQry = '';
//        }

//        $tmpquery = "WHERE tea.project = ':project_id' AND tea.published = '0' ORDER BY mem.name";

//        xdebug_var_dump( $this->db );

        $this->db->query( $this->initrequest['teams'] . ' WHERE tea.project = :project_id AND tea.published = 0 ORDER BY mem.name ' );

        $this->db->bind(':project_id', $projectId);

        return $this->db->resultset();
    }


    /**
     * @param $projId
     * @param $memId
     * @return mixed
     */
    public function getTeamMembersByProjAndMember($projId, $memId)
    {
        $projId = intval( $projId );
        $memId = intval( $memId );

        $this->db->query($this->initrequest['teams'] . ' WHERE tea.project = :proj_id AND tea.member = :mem_id');

        $this->db->bind(':proj_id', $projId, PDO::PARAM_INT);
        $this->db->bind(':mem_id', $memId, PDO::PARAM_INT);


//        $this->db->bind(':proj_id', $projId, PDO::PARAM_INT);

        $resultSet = $this->db->resultset();
//        xdebug_var_dump( $resultSet );
echo '<pre>';
        $this->db->debugDumpParams();
echo '</pre>';

        return $resultSet;
    }


}