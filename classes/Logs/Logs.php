<?php

namespace phpCollab\Logs;

use phpCollab\Database;


class Logs
{
    protected $logs_gateway;
    protected $db;

    public function __construct()
    {
        $this->db = new Database();

        $this->logs_gateway = new LogsGateway($this->db);
    }

    public function getLogByLogin($memberLogin) {
        $data = $this->logs_gateway->getLogByLogin($memberLogin);

        return $data;
    }

    public function insertLogEntry($entryData) {
        $data = $this->logs_gateway->insertLogEntry($entryData);

        return $data;
    }

    public function updateLogEntry($entryData) {
        $data = $this->logs_gateway->updateLogEntry($entryData);

        return $data;
    }


}