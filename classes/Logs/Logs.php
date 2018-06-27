<?php

namespace phpCollab\Logs;

use phpCollab\Database;


class Logs
{
    protected $logs_gateway;
    protected $db;

    /**
     * Logs constructor.
     */
    public function __construct()
    {
        $this->db = new Database();

        $this->logs_gateway = new LogsGateway($this->db);
    }

    /**
     * @param $memberLogin
     * @return mixed
     */
    public function getLogByLogin($memberLogin) {
        $data = $this->logs_gateway->getLogByLogin($memberLogin);

        return $data;
    }

    /**
     * @param $entryData
     * @return mixed
     */
    public function insertLogEntry($entryData) {
        $data = $this->logs_gateway->insertLogEntry($entryData);

        return $data;
    }

    /**
     * @param $entryData
     * @return mixed
     */
    public function updateLogEntry($entryData) {
        $data = $this->logs_gateway->updateLogEntry($entryData);

        return $data;
    }

    /**
     * @param string $login
     * @param boolean $connected
     */
    public function setConnectedByLogin($login, $connected)
    {
//        $tmpquery1 = "UPDATE {$tableCollab["logs"]} SET connected='' WHERE login = :login_id";
        $data = $this->logs_gateway->setConnectedByLogin($login, $connected);
    }
}
