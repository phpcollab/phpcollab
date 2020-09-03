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
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
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
    public function getLogByLogin($memberLogin)
    {
        return $this->logs_gateway->getLogByLogin($memberLogin);
    }

    /**
     * @param $entryData
     * @return mixed
     */
    public function insertLogEntry($entryData)
    {
        return $this->logs_gateway->insertLogEntry($entryData);
    }

    /**
     * @param $entryData
     * @return mixed
     */
    public function updateLogEntry($entryData)
    {
        return $this->logs_gateway->updateLogEntry($entryData);
    }

    /**
     * @param $date
     * @param $userId
     * @return mixed
     */
    public function updateConnectedTimeForUser($date, $userId)
    {
        return $this->logs_gateway->updateConnectedTimeForUser($date, $userId);
    }

    /**
     * @param string $login
     * @param boolean $connected
     * @return mixed
     */
    public function setConnectedByLogin(string $login, bool $connected)
    {
        return $this->logs_gateway->setConnectedByLogin($login, $connected);
    }

    /**
     *
     */
    public function getConnectedUsersCount()
    {
        $dateunix = date("U");
        $dateunix = $dateunix - 5 * 60;
        $users = $this->logs_gateway->getConnectedUsers($dateunix);
        return count($users);
    }

    /**
     * @return mixed
     */
    public function getConnectedUsers()
    {
        $dateunix = date("U");
        $dateunix = $dateunix - 5 * 60;
        return $this->logs_gateway->getConnectedUsers($dateunix);
    }
}
