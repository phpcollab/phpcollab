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
     * @param $name
     * @param $address
     * @param $phone
     * @param $url
     * @param $email
     * @param $comments
     * @param $owner
     * @param $hourlyRate
     * @param $extension
     * @param $created
     * @return string
     */
    public function addClientOrganization($name, $address, $phone, $url, $email, $comments, $owner, $hourlyRate, $extension, $created)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["organizations"]} (
    name, address1, phone, url, email, comments, extension_logo, owner, hourly_rate, created
) VALUES ( 
    :name, :address, :phone, :url, :email, :comments, :extension, :owner, :hourly_rate, :created
)
SQL;

        $this->db->query($sql);
        $this->db->bind(":name", $name);
        $this->db->bind(":address", $address);
        $this->db->bind(":phone", $phone);
        $this->db->bind(":url", $url);
        $this->db->bind(":email", $email);
        $this->db->bind(":comments", $comments);
        $this->db->bind(":extension", $extension);
        $this->db->bind(":owner", $owner);
        $this->db->bind(":hourly_rate", $hourlyRate);
        $this->db->bind(":created", $created);
        $this->db->execute();
        return $this->db->lastInsertId();


    }

    /**
     * @param $clientId
     * @param $name
     * @param $address
     * @param $phone
     * @param $url
     * @param $email
     * @param $comments
     * @param $owner
     * @param $hourlyRate
     * @return mixed
     */
    public function updateClientOrganization($clientId, $name, $address, $phone, $url, $email, $comments, $owner, $hourlyRate)
    {
        $sql = <<<SQL
UPDATE {$this->tableCollab["organizations"]}
SET
    name = :name,
    address1 = :address,
    phone = :phone,
    url = :url,
    email = :email,
    comments = :comments,
    owner = :owner,
    hourly_rate = :hourly_rate
WHERE id = :client_id

SQL;

        $this->db->query($sql);
        $this->db->bind(":client_id", $clientId);
        $this->db->bind(":name", $name);
        $this->db->bind(":address", $address);
        $this->db->bind(":phone", $phone);
        $this->db->bind(":url", $url);
        $this->db->bind(":email", $email);
        $this->db->bind(":comments", $comments);
        $this->db->bind(":owner", $owner);
        $this->db->bind(":hourly_rate", $hourlyRate);
        return $this->db->execute();
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

    /**
     * @param $orgIds
     * @param null $sorting
     * @return mixed
     */
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
     * @param $query
     * @param null $sorting
     * @param null $limit
     * @param null $rowLimit
     * @return mixed
     */
    public function searchResultOrganizations($query, $sorting = null, $limit = null, $rowLimit = null)
    {
        $sql = $this->initrequest['organizations'] . ' ' . $query . $this->orderBy($sorting) . $this->limit($limit, $rowLimit);
        $this->db->query($sql);
        $this->db->execute();
        return $this->db->resultset();
    }

    /**
     * Returns the LIMIT attribute for SQL strings
     * @param $start
     * @param $rowLimit
     * @return string
     */
    private function limit($start, $rowLimit)
    {
        if (!is_null($start) && !is_null($rowLimit)) {
            return " LIMIT {$start},{$rowLimit}";
        }
        return '';

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
