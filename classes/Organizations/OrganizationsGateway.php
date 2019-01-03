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
     * @param $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getOrginizationsByOwnerId($ownerId, $sorting = null)
    {
        $whereStatement = ' WHERE org.owner = :owner_id AND org.id != 1';

        $this->db->query($this->initrequest["organizations"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @param $clientId
     * @param null $sorting
     * @return mixed
     */
    public function getClientIn($clientId, $sorting = null)
    {
        $clientId = explode(',', $clientId);

        $placeholders = str_repeat('?, ', count($clientId) - 1) . '?';

        $whereStatement = "WHERE org.id IN($placeholders)";

        $this->db->query($this->initrequest["organizations"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':client_id', $clientId);

        return $this->db->single();
    }

    /**
     * @param $sorting
     * @return mixed
     */
    public function getAllOrganizations($sorting)
    {
        $whereStatement = " WHERE org.id != '1'";

        $this->db->query($this->initrequest["organizations"] . $whereStatement . $this->orderBy($sorting));

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

        $whereStatement = " WHERE org.id IN ($placeholders)";

        $this->db->query($this->initrequest["organizations"] . $whereStatement . $this->orderBy('org.name'));

        $this->db->execute($orgId);
        return $this->db->fetchAll();

    }

    public function getOrganizationsIn($orgIds, $sorting = null)
    {
        $orgIds = explode(',', $orgIds);
        $placeholders = str_repeat('?, ', count($orgIds) - 1) . '?';

        $whereStatement = " WHERE org.id IN ($placeholders) AND org.id != 1";

        $this->db->query($this->initrequest["organizations"] . $whereStatement . $this->orderBy($sorting));

        $this->db->execute($orgIds);
        return $this->db->fetchAll();
    }

    /**
     * @param $orgId
     * @param $ownerId
     * @return mixed
     */
    public function getOrgByIdAndOwner($orgId, $ownerId)
    {
        $whereStatement = ' WHERE org.owner = :owner_id AND org.id = :org_id';

        $this->db->query($this->initrequest["organizations"] . $whereStatement);

        $this->db->bind(':owner_id', $ownerId);
        $this->db->bind(':org_id', $orgId);

        return $this->db->single();
    }

    /**
     * @param $organizationInfo
     * @return mixed
     */
    public function updateOrganizationInformation($organizationInfo)
    {
        $query = <<<SQL
UPDATE {$this->tableCollab["organizations"]} 
SET 
name= :org_name,
address1= :org_address1,
phone= :org_phone,
url= :org_url,
email= :org_email,
comments= :org_comments 
WHERE id = 1
SQL;
        $this->db->query($query);
        $this->db->bind(':org_name', $organizationInfo['name']);
        $this->db->bind(':org_address1', $organizationInfo['address1']);
        $this->db->bind(':org_phone', $organizationInfo['phone']);
        $this->db->bind(':org_url', $organizationInfo['url']);
        $this->db->bind(':org_email', $organizationInfo['email']);
        $this->db->bind(':org_comments', $organizationInfo['comments']);
        return $this->db->execute();

    }

    /**
     * @param $orgId
     * @param $logoExtension
     * @return mixed
     */
    public function setLogoExtensionByOrgId($orgId, $logoExtension)
    {
        $query = "UPDATE {$this->tableCollab["organizations"]} SET extension_logo = :logo_ext WHERE id = :org_id";
        $this->db->query($query);
        $this->db->bind(':logo_ext', $logoExtension);
        $this->db->bind(':org_id', $orgId);
        return $this->db->execute();
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

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["org.name", "org.phone", "org.url"];
            $pieces = explode(' ', $sorting);

            if ($pieces) {
                $key = array_search($pieces[0], $allowedOrderedBy);

                if ($key !== false) {
                    $order = $allowedOrderedBy[$key];
                    return " ORDER BY $order $pieces[1]";
                }
            }
        }

        return '';
    }

}
