<?php


namespace phpCollab\Logs;

use phpCollab\Database;


class LogsGateway
{
    /**
     * @var Database
     */
    protected $db;
    /**
     * @var mixed
     */
    protected $initrequest;
    /**
     * @var mixed
     */
    protected $tableCollab;

    /**
     * Logs constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $memberLogin
     * @return mixed
     */
    public function getLogByLogin($memberLogin)
    {
        $this->db->query($this->initrequest["logs"] . ' WHERE log.login = :member_login');

        $this->db->bind(':member_login', $memberLogin);

        return $this->db->single();
    }

    /**
     * @param $entryData
     * @return mixed
     */
    public function insertLogEntry($entryData) {
        $query = <<<SQL
INSERT INTO {$this->tableCollab["logs"]} 
(login, password, ip, session, compt, last_visite) 
VALUES (
  :member_login,
  :ip,
  :session,
  :compt,
  :timestamp
)
SQL;

        $this->db->query($query);

        $this->db->bind(':member_login', $entryData['login']);
        $this->db->bind(':ip', $entryData['ip']);
        $this->db->bind(':session', $entryData['session']);
        $this->db->bind(':compt', $entryData['compt']);
        $this->db->bind(':timestamp', $entryData['last_viste']);

        return $this->db->execute();
    }

    /**
     * @param $login
     * @param string $connected
     * @return mixed
     */
    public function setConnectedByLogin($login, $connected = '') {
        $query = <<<SQL
UPDATE {$this->tableCollab["logs"]} SET connected = :connected WHERE login = :login_id
SQL;
        $this->db->query($query);
        $this->db->bind(':login_id', $login);
        $this->db->bind(':connected', $connected);
        return $this->db->execute();
    }

    /**
     * @param $entryData
     * @return mixed
     */
    public function updateLogEntry($entryData) {
        $query = <<<SQL
UPDATE {$this->tableCollab["logs"]} 
SET 
ip = :ip, 
password = null,
session = :session, 
compt = :compt, 
last_visite = :last_visit 
WHERE login = :login
SQL;
        $this->db->query($query);

        $this->db->bind(':ip', $entryData['ip']);
        $this->db->bind(':session', $entryData['session']);
        $this->db->bind(':compt', $entryData['compt']);
        $this->db->bind(':last_visit', $entryData['last_visite']);
        $this->db->bind(':login', $entryData['login']);

        return $this->db->execute();
    }

    /**
     * @param $date
     * @param $userId
     * @return mixed
     */
    public function updateConnectedTimeForUser($date, $userId) {
        $query = <<<SQL
UPDATE {$this->tableCollab["logs"]} 
SET connected = :date_unix 
WHERE login = :login_session
SQL;

        $this->db->query($query);
        $this->db->bind(':date_unix', $date);
        $this->db->bind(':login_session', $userId);
        return $this->db->execute();
    }

    /**
     * @param $dateunix
     * @return mixed
     */
    public function getConnectedUsers($dateunix)
    {
        $query = "SELECT * FROM {$this->tableCollab["logs"]} WHERE connected > :date_unix";
        $this->db->query($query);
        $this->db->bind(':date_unix', $dateunix);
        return $this->db->resultset();
    }
}
