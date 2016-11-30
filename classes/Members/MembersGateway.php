<?php


namespace phpCollab\Members;

use phpCollab\Database;

class MembersGateway
{
    protected $db;
    protected $initrequest;

    /**
     * Reports constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    public function getMemberByLogin($loginData)
    {
        if ($loginData['demo'] != true) {
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

        return $this->db->single();
    }

    public function getAllMembers()
    {
        $this->db->query($this->initrequest["members"]);

        return $this->db->resultset();
    }


}