<?php


namespace phpCollab\Members;

use phpCollab\Database;

/**
 * Class MembersGateway
 * @package phpCollab\Members
 */
class MembersGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * Reports constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $loginData
     * @return mixed
     */
    public function getMemberByLogin($loginData)
    {
        if (is_array($loginData)) {
            if ($loginData['demo'] !== true) {
                if ($loginData['ssl']) {
                    $whereStatement = "WHERE mem.email_work = :ssl_email AND mem.login != 'demo' AND mem.profil != 4";
                } else {
                    $whereStatement = "WHERE mem.login = :member_login AND mem.login != 'demo' AND mem.profil != 4";
                }
            } else {
                $whereStatement = "WHERE mem.login = :member_login AND mem.profil != 4";
            }

            $this->db->query($this->initrequest["members"] . ' ' . $whereStatement);

            $this->db->bind(':member_login', $loginData['login']);

            if ($loginData['ssl']) {
                $this->db->bind(':ssl_email', $loginData['ssl_email']);
            }
        } else {
            $whereStatement = "WHERE mem.login = :member_login AND mem.profil != 4";
            $this->db->query($this->initrequest["members"] . ' ' . $whereStatement);
            $this->db->bind(':member_login', $loginData);
        }


        return $this->db->single();
    }

    /**
     * @param $memberLogin
     * @param null $memberLoginOld
     * @return mixed
     */
    public function checkMemberExists($memberLogin, $memberLoginOld = null)
    {
        $tmpquery = "WHERE mem.login = :member_login AND mem.login != :member_login_old";
        $this->db->query($this->initrequest["members"] . ' ' . $tmpquery);
        $this->db->bind(':member_login', $memberLogin);
        $this->db->bind(':member_login_old', $memberLoginOld);
        return $this->db->resultset();
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getMemberById($memberId)
    {
        $whereStatement = "WHERE mem.id = :member_id";

        $this->db->query($this->initrequest["members"] . ' ' . $whereStatement);

        $this->db->bind(':member_id', $memberId);

        return $this->db->single();
    }

    /**
     * @param $memberIds
     * @return mixed
     */
    public function getMembersIn($memberIds)
    {
        $memberIds = explode(',', $memberIds);
        $placeholders = str_repeat('?, ', count($memberIds) - 1) . '?';
        $whereStatement = "WHERE mem.id IN ($placeholders)";
        $this->db->query($this->initrequest["members"] . ' ' . $whereStatement);
        $this->db->execute($memberIds);
        return $this->db->fetchAll();
    }

    /**
     * @param $memberIds
     * @return mixed
     */
    public function getNonClientMembersNotIn($memberIds)
    {
        $memberIds = explode(',', $memberIds);
        $placeholders = str_repeat('?, ', count($memberIds) - 1) . '?';
        $whereStatement = "WHERE mem.profil != '3' AND mem.id NOT IN($placeholders) " . $this->orderBy('mem.name'); //ORDER BY mem.name";
        $this->db->query($this->initrequest["members"] . ' ' . $whereStatement);
        $this->db->execute($memberIds);
        return $this->db->fetchAll();
    }

    /**
     * @param $orgId
     * @param null $sorting
     * @return mixed
     */
    public function getAllByOrg($orgId, $sorting = null)
    {
        $whereStatement = "WHERE mem.organization = :org_id";

        $this->db->query($this->initrequest["members"] . ' ' . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':org_id', $orgId);

        return $this->db->resultset();
    }

    /**
     * @param $orgId
     * @param null $membersTeam
     * @param null $sorting
     * @return mixed
     */
    public function getClientMembersByOrgIdAndNotInTeam($orgId, $membersTeam = null, $sorting = null)
    {
        $whereStatement = "WHERE mem.organization = ?";
        $queryParams = [$orgId];

        if ($membersTeam) {
            $membersTeam = explode(',', $membersTeam);
            $placeholders = str_repeat('?, ', count($membersTeam) - 1) . '?';
            $whereStatement .= " AND mem.id NOT IN($placeholders)";
            $queryParams = array_merge($queryParams, $membersTeam);
        }
        $whereStatement .= " AND mem.profil = 3";

        $this->db->query($this->initrequest["members"] . ' ' . $whereStatement . $this->orderBy($sorting));
        $this->db->execute($queryParams);
        return $this->db->fetchAll();
    }

    /**
     * @return mixed
     */
    public function getAllMembers()
    {
        $this->db->query($this->initrequest["members"]);

        return $this->db->resultset();
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function deleteMember($orgId)
    {
        $orgId = explode(',', $orgId);
        $placeholders = str_repeat('?, ', count($orgId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['members']} WHERE organization IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($orgId);
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function deleteMemberByIdIn($memberId)
    {
        $memberId = explode(',', $memberId);
        $placeholders = str_repeat('?, ', count($memberId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['members']} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($memberId);
    }

    /**
     * @param $memberId
     * @param $login
     * @param $name
     * @param $title
     * @param $organization
     * @param $emailWork
     * @param $phoneWork
     * @param $phoneHome
     * @param $phoneMobile
     * @param $fax
     * @param $lastPage
     * @param $comments
     * @return mixed
     */
    public function updateMember($memberId, $login, $name, $title, $organization, $emailWork, $phoneWork, $phoneHome, $phoneMobile, $fax, $lastPage, $comments)
    {
        $sql = <<<SQL
UPDATE {$this->tableCollab["members"]} SET 
login = :login, 
name = :name, 
title = :title, 
organization = :organization, 
email_work = :email_work, 
phone_work = :phone_work, 
phone_home = :phone_home, 
mobile = :phone_mobile, 
fax = :fax, 
last_page = :last_page, 
comments = :comments 
WHERE id = :member_id
SQL;

        $this->db->query($sql);
        $this->db->bind(':login', $login);
        $this->db->bind(':name', $name);
        $this->db->bind(':title', $title);
        $this->db->bind(':organization', $organization);
        $this->db->bind(':email_work', $emailWork);
        $this->db->bind(':phone_work', $phoneWork);
        $this->db->bind(':phone_home', $phoneHome);
        $this->db->bind(':phone_mobile', $phoneMobile);
        $this->db->bind(':fax', $fax);
        $this->db->bind(':last_page', $lastPage);
        $this->db->bind(':comments', $comments);
        $this->db->bind(':member_id', $memberId);

        return $this->db->execute();
    }

    /**
     * @param $user
     * @param $page
     * @return mixed
     */
    public function setLastPageVisited($user, $page)
    {
        $query = <<<SQL
UPDATE {$this->tableCollab["members"]} 
SET last_page = :page 
WHERE id = :user_id
SQL;

        $this->db->query($query);
        $this->db->bind(':user_id', $user);
        $this->db->bind(':page', $page);
        return $this->db->execute();
    }

    /**
     * @param String $username
     * @param String $page
     * @return mixed
     */
    public function setLastPageVisitedByLogin($username, $page)
    {
        $query = <<<SQL
UPDATE {$this->tableCollab["members"]} 
SET last_page = :page 
WHERE login = :user_name
SQL;

        $this->db->query($query);
        $this->db->bind(':user_id', $username);
        $this->db->bind(':page', $page);
        return $this->db->execute();
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["mem.name", "mem.login", "mem.email_work", "mem.phone_work", "connected"];
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
