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
     * @param int $projectId
     * @param int $memberId
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMember(int $projectId, int $memberId)
    {
        $whereStatement = " WHERE tea.project = :project_id AND tea.member = :member_id";
        $this->db->query($this->initrequest["teams"] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished(
        int $projectId,
        int $memberId
    ) {
        $whereStatement = " WHERE tea.member = :member_id AND pro.id = :project_id AND pro.status IN(0,2,3) AND pro.published = '0'";
        $this->db->query($this->initrequest["teams"] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * @param int $memberId
     * @return mixed
     */
    public function getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished(int $memberId)
    {
        $whereStatement = " WHERE tea.member = :member_id AND pro.status IN(0,2,3) AND pro.published = 0 ORDER BY pro.name";
        $this->db->query($this->initrequest["teams"] . $whereStatement);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * @param int $memberId
     * @param string|null $sorting
     * @return mixed
     */
    public function getTeamByMemberId(int $memberId, string $sorting = null)
    {
        $whereStatement = " WHERE tea.member = :member_id";
        $sql = $this->initrequest["teams"] . $whereStatement . $this->orderBy($sorting);
        $this->db->query($sql);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }


    /**
     * @param int $projectId
     * @param int $memberId
     * @return mixed
     */
    public function getOtherProjectTeamMembers(int $projectId, int $memberId)
    {
        $tmpquery = $this->initrequest["teams"] . " WHERE tea.project = :project_id AND tea.member != :member_id ORDER BY mem.id";
        $this->db->query($tmpquery);
        $this->db->bind(":project_id", $projectId);
        $this->db->bind(":member_id", $memberId);
        return $this->db->resultset();
    }

    /**
     * @param int $memberId
     * @param int $orgId
     * @return mixed
     */
    public function getTeamByTeamMemberAndOrgId(int $memberId, int $orgId)
    {
        $whereStatement = " WHERE tea.member = :member_id AND org2.id = :org_id";
        $this->db->query($this->initrequest["teams"] . $whereStatement);
        $this->db->bind(':org_id', $orgId);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param int|null $offset
     * @param int|null $limit
     * @param string|null $sorting
     * @return mixed
     * @internal param $orderBy
     */
    public function getTeamByProjectId(int $projectId, int $offset = null, int $limit = null, string $sorting = null)
    {
        $whereStatement = " WHERE tea.project = :project_id AND mem.profil != '3'";

        $sql = $this->initrequest["teams"] . $whereStatement . $this->orderBy($sorting) . $this->limit($offset, $limit);

        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getClientTeamMembersByProject(int $projectId, string $sorting = null)
    {
        $whereStatement = " WHERE tea.project = :project_id  AND mem.profil = '3'";
        $this->db->query($this->initrequest["teams"] . $whereStatement . $this->orderBy($sorting));
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param string $sorting
     * @return mixed
     */
    public function getProjectSiteContacts(int $projectId, string $sorting)
    {
        $tmpQuery = $this->initrequest["teams"] .
            " WHERE tea.project = :project_id AND tea.published = '0'" .
            $this->orderBy($sorting);

        $this->db->query($tmpQuery);
        $this->db->bind(":project_id", $projectId);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @return mixed
     */
    public function isTeamMember(int $projectId, int $memberId)
    {
        $whereStatement = " WHERE tea.project = :project_id AND tea.member = :member_id";

        $sql = $this->initrequest["teams"] . $whereStatement;

        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * @param int $memberId
     * @return mixed
     */
    public function getTeamsImAMemberOf(int $memberId)
    {
        $sql = "SELECT tea.id FROM {$this->db->getTableName("teams")} tea LEFT OUTER JOIN {$this->db->getTableName("projects")} pro ON pro.id = tea.project WHERE tea.member = :member_id AND pro.status IN(0,2,3)";
        $this->db->query($sql);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param string $memberIds
     * @return mixed
     */
    public function publishTeams(int $projectId, string $memberIds)
    {
        if (strpos($memberIds, ',')) {
            $memberIds = explode(',', $memberIds);
            $placeholders = str_repeat('?, ', count($memberIds) - 1) . '?';
            $sql = "UPDATE {$this->db->getTableName("teams")} SET published = 0 WHERE member IN($placeholders) AND project = ?";

            array_push($memberIds, $projectId);
            $this->db->query($sql);
            return $this->db->execute($memberIds);
        } else {
            $sql = "UPDATE {$this->db->getTableName("teams")} SET published = 0 WHERE member = :member_id AND project = :project_id";
            $this->db->query($sql);
            $this->db->bind(':member_id', $memberIds);
            $this->db->bind(':project_id', $projectId);
            return $this->db->execute();
        }
    }

    /**
     * @param int $projectId
     * @param string $memberIds
     * @return mixed
     */
    public function unPublishTeams(int $projectId, string $memberIds)
    {
        if (strpos($memberIds, ',')) {
            $memberIds = explode(',', $memberIds);
            $placeholders = str_repeat('?, ', count($memberIds) - 1) . '?';
            $sql = "UPDATE {$this->db->getTableName("teams")} SET published = 1 WHERE member IN($placeholders) AND project = ?";

            array_push($memberIds, $projectId);
            $this->db->query($sql);
            return $this->db->execute($memberIds);
        } else {
            $sql = "UPDATE {$this->db->getTableName("teams")} SET published = 1 WHERE member = :member_id AND project = :project_id";
            $this->db->query($sql);
            $this->db->bind(':member_id', $memberIds);
            $this->db->bind(':project_id', $projectId);
            return $this->db->execute();
        }

    }

    /**
     * @param int $projectId
     * @param string $memberId
     * @return mixed
     */
    public function deleteFromTeamsWhereProjectIdEqualsAndMemberIdIn(int $projectId, string $memberId)
    {
        // Generate placeholders
        $memberId = explode(',', $memberId);
        $placeholders = str_repeat('?, ', count($memberId) - 1) . '?';

        $sql = "DELETE FROM {$this->db->getTableName("teams")} WHERE project = ? AND member IN($placeholders)";

        if (is_array($memberId)) {
            array_unshift($memberId, $projectId);
        } else {
            $memberId = explode(',', $projectId . ',' . $memberId);
        }

        $this->db->query($sql);
        return $this->db->execute($memberId);
    }

    /**
     * @param int $projectId
     * @return mixed
     */
    public function deleteFromTeamsWhereProjectIdIn(int $projectId)
    {
        // Generate placeholders
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("teams")} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param string $memberId
     * @return mixed
     */
    public function deleteFromTeamsWhereMemberIdIn(string $memberId)
    {
        // Generate placeholders
        $memberId = explode(',', $memberId);
        $placeholders = str_repeat('?, ', count($memberId) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("teams")} WHERE member IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($memberId);
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @param int $published
     * @param int $authorized
     * @return string
     */
    public function addTeam(int $projectId, int $memberId, int $published, int $authorized): string
    {
        $sql = "INSERT INTO {$this->db->getTableName("teams")} (project,member,published,authorized) VALUES(:project,:member,:published,:authorized)";
        $this->db->query($sql);
        $this->db->bind(':project', $projectId);
        $this->db->bind(':member', $memberId);
        $this->db->bind(':published', $published);
        $this->db->bind(':authorized', $authorized);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Returns the LIMIT attribute for SQL strings
     * @param int|null $offset
     * @param int|null $limit
     * @return string
     */
    private function limit(int $offset = null, int $limit = null): string
    {
        if (!is_null($offset) && !is_null($limit)) {
            return " LIMIT $limit OFFSET $offset";
        }
        return '';
    }

    /**
     * @param string|null $sorting
     * @return string
     */
    private function orderBy(string $sorting = null): string
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = [
                "mem.name",
                "mem.title",
                "mem.login",
                "mem.phone_work",
                "log.connected",
                "tea.published",
                "tas.project"
            ];
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
