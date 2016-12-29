<?php


namespace phpCollab\Members;

use phpCollab\Database;

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

    public function getMemberById($memberId)
    {
        $whereStatement = "WHERE mem.id = :member_id";

        $this->db->query($this->initrequest["members"] . ' ' . $whereStatement);

        $this->db->bind(':member_id', $memberId);

        return $this->db->single();
    }

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
        $sql = "DELETE FROM " . $this->tableCollab['members'] . " WHERE organization IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($orgId);
    }


}