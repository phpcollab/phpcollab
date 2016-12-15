<?php


namespace phpCollab\Organizations;

use phpCollab\Database;

class Organizations
{
    protected $organizations_gateway;
    protected $db;

    /**
     * Organizations constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->organizations_gateway = new OrganizationsGateway($this->db);
    }

    public function checkIfClientExistsByName($clientName)
    {
        $clientName = filter_var($clientName, FILTER_SANITIZE_STRING);

        $data = $this->organizations_gateway->getClientByName($clientName);

        return $data;
    }

    public function checkIfClientExistsById($clientId)
    {
        $clientId = filter_var($clientId, FILTER_VALIDATE_INT);

        $data = $this->organizations_gateway->getClientById($clientId);

        return $data;
    }

}