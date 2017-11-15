<?php
namespace phpCollab\Organizations;

use phpCollab\Database;

/**
 * Class OrganizationsGateway
 * @package phpCollab\Organizations
 */
class OrganizationsGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * OrganizationsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $clientName
     * @return mixed
     */
    public function getClientByName($clientName)
    {
        $whereStatement = ' WHERE org.name = :client_name';

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        $this->db->bind(':client_name', $clientName);

        return $this->db->single();
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public function getClientById($clientId)
    {
        $whereStatement = ' WHERE org.id = :client_id';

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        $this->db->bind(':client_id', $clientId);

        return $this->db->single();
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public function getClientIn($clientId)
    {
        $clientId = explode(',', $clientId);

        $placeholders = str_repeat('?, ', count($clientId) - 1) . '?';

        $whereStatement = "WHERE org.id IN($placeholders)";

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        $this->db->bind(':client_id', $clientId);

        return $this->db->single();
    }

    /**
     * @return mixed
     */
    public function getAllOrganizations()
    {
        $whereStatement = " WHERE org.id != '1' ORDER BY org.name";

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        return $this->db->resultset();
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function getOrganizationsOrderedByName($orgId)
    {
        $orgId = explode(',', $orgId);
        $placeholders = str_repeat('?, ', count($orgId) - 1) . '?';

        $whereStatement = " WHERE org.id IN ($placeholders) ORDER BY org.name";

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        return $this->db->resultset();
    }

    public function getOrgByIdAndOwner($orgId, $ownerId)
    {
        $whereStatement = ' WHERE org.owner = :owner_id AND org.id = :org_id';

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        $this->db->bind(':owner_id', $ownerId);
        $this->db->bind(':org_id', $orgId);

        return $this->db->resultset();
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public function deleteClient($clientId)
    {
        $clientId = explode(',', $clientId);

        $placeholders = str_repeat('?, ', count($clientId) - 1) . '?';

        $sql = "DELETE FROM {$this->tableCollab['organizations']} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($clientId);
    }
}
