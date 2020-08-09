<?php

namespace phpCollab\LoginLogs;

use phpCollab\Database;


/**
 * Class Logs
 * @package phpCollab\Logs
 */
class LoginLogs
{
    protected $logs_gateway;
    protected $db;

    /**
     * Logs constructor.
     */
    public function __construct()
    {
        $this->db = new Database();

        $this->logs_gateway = new LoginLogsGateway($this->db);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getLogs($sorting = null)
    {
        return $this->logs_gateway->getLogs($sorting);
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
     * @param $date
     * @param $userId
     * @return mixed
     */
    public function updateConnectedTimeForUser($date, $userId) {
        $data = $this->logs_gateway->updateConnectedTimeForUser($date, $userId);

        return $data;
    }

    /**
     * @param string $login
     * @param boolean $connected
     * @return mixed
     */
    public function setConnectedByLogin($login, $connected)
    {
        return $this->logs_gateway->setConnectedByLogin($login, $connected);
    }

    /**
     *
     */
    public function getConnectedUsersCount()
    {
        $dateunix = date("U");
        $dateunix = $dateunix-5*60;
        $users = $this->logs_gateway->getConnectedUsers($dateunix);
        return count($users);
    }

    /**
     * @return mixed
     */
    public function getConnectedUsers()
    {
        $dateunix = date("U");
        $dateunix = $dateunix-5*60;
        $users = $this->logs_gateway->getConnectedUsers($dateunix);
        return $users;
    }
}
