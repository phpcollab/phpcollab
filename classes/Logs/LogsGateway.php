<?php


namespace phpCollab\Logs;

use phpCollab\Database;


class LogsGateway
{
    protected $db;
    protected $initrequest;

    /**
     * Logs constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    public function getLogByLogin($memberLogin)
    {
        $this->db->query($this->initrequest["logs"] . ' WHERE log.login = :member_login');

        $this->db->bind(':member_login', $memberLogin);

        return $this->db->single();
    }

    public function insertLogEntry($entryData) {
        $query = <<<SQL
INSERT INTO logs
(login, password, ip, session, compt, last_visite) 
VALUES (
  :member_login,
  :password,
  :ip,
  :session,
  :compt,
  :timestamp
)
SQL;

        $this->db->query($query);

        $this->db->bind(':member_login', $entryData['login']);
        $this->db->bind(':password', $entryData['password']);
        $this->db->bind(':ip', $entryData['ip']);
        $this->db->bind(':session', $entryData['session']);
        $this->db->bind(':compt', $entryData['compt']);
        $this->db->bind(':timestamp', $entryData['last_viste']);

        return $this->db->execute();
    }

    public function updateLogEntry($entryData) {
        $query = <<<SQL
UPDATE logs 
SET 
ip=:ip, 
session=:session, 
compt=:compt, 
last_visite=:last_visite 
WHERE login = :login
SQL;

        $this->db->query($query);

        $this->db->bind(':ip', $entryData['id']);
        $this->db->bind(':session', $entryData['session']);
        $this->db->bind(':compt', $entryData['compt']);
        $this->db->bind(':last_visite', $entryData['last_visite']);
        $this->db->bind(':login', $entryData['login']);

        return $this->db->execute();
    }
}
