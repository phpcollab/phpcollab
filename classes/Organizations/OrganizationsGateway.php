<?php
namespace phpCollab\Organizations;

use phpCollab\Database;

class OrganizationsGateway
{
    protected $db;
    protected $initrequest;

    /**
     * OrganizationsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    public function getClientByName($clientName)
    {
        $whereStatement = ' WHERE org.name = :client_name';

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        $this->db->bind(':client_name', $clientName);

        return $this->db->single();
    }

    public function getClientById($clientId)
    {
        $whereStatement = ' WHERE org.id = :client_id';

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        $this->db->bind(':client_id', $clientId);

        return $this->db->single();
    }

}