<?php


namespace phpCollab\Teams;

use phpCollab\Database;

/**
 * Class TeamsGateway
 * @package phpCollab\Teams
 */
class TeamsGateway
{
    protected $db;
    protected $initrequest;

    /**
     * TeamsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    /**
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMember($projectId, $memberId)
    {
        $whereStatement = " WHERE tea.project = :project_id AND tea.member = :member_id";
        $this->db->query($this->initrequest["teams"] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':member_id', $memberId);
        $results = $this->db->resultset();
        return $results;
    }

    /**
     * @param $projectId
     * @param $orderBy
     * @return mixed
     */
    public function getTeamByProjectIdAndOrderBy($projectId, $orderBy)
    {
        $whereStatement = " WHERE tea.project = :project_id";

        if (isset($orderBy)) {
            $orderBy = filter_var($orderBy, FILTER_SANITIZE_STRING);
            $orderByStatement = " ORDER BY " . $orderBy;
        } else {
            $orderByStatement = '';
        }
        $sql = $this->initrequest["teams"] . $whereStatement . $orderByStatement;

        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId);
        $results = $this->db->resultset();

        return $results;
    }
}
