<?php


namespace phpCollab\Organizations;

use phpCollab\Database;
use Exception;
use phpCollab\Util;

/**
 * Class Organizations
 * @package phpCollab\Organizations
 */
class Organizations
{
    protected $organizations_gateway;
    protected $db;

    /**
     * Organizations constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->organizations_gateway = new OrganizationsGateway($this->db);
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
     * @param null $extension
     * @param null $created
     * @return string
     */
    public function addClient($name, $address, $phone, $url, $email, $comments, $owner, $hourlyRate, $extension = null, $created = null)
    {
        if (is_null($extension)) {
            $extension = '';
        }

        if (is_null($created)) {
            $created = date('Y-m-d h:i');
        }

        return $this->organizations_gateway->addClientOrganization($name, $address, $phone, $url, $email, $comments, $owner, $hourlyRate, $extension, $created);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getAllOrganizations($sorting = null)
    {
        return $this->organizations_gateway->getAllOrganizations($sorting);
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
    public function updateClient($clientId, $name, $address, $phone, $url, $email, $comments, $owner, $hourlyRate)
    {
        return $this->organizations_gateway->updateClientOrganization($clientId, $name, $address, $phone, $url, $email, $comments, $owner, $hourlyRate);
    }

    /**
     * @param $clientName
     * @return mixed
     */
    public function checkIfClientExistsByName($clientName)
    {
        $clientName = filter_var($clientName, FILTER_SANITIZE_STRING);

        $data = $this->organizations_gateway->getClientByName($clientName);

        return !empty($data);
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public function checkIfClientExistsById($clientId)
    {
        $clientId = filter_var($clientId, FILTER_VALIDATE_INT);

        return $this->organizations_gateway->getClientById($clientId);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getListOfOrganizations($sorting = null)
    {
        $orgs = $this->organizations_gateway->getAllOrganizations($sorting);

        foreach ($orgs as $key => $org) {
           $orgs[$key]["org_phone"] = (!empty($org["org_phone"])) ? $org["org_phone"] : Util::doubleDash();
        }

        return $orgs;
    }

    /**
     * @param $clientIds
     * @param null $sorting
     * @return mixed
     */
    public function getFilteredOrganizations($clientIds, $sorting = null)
    {
        return $this->organizations_gateway->getOrganizationsIn($clientIds, $sorting);
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function getOrganizationsOrderedByName($orgId)
    {
        return $this->organizations_gateway->getOrganizationsOrderedByName($orgId);
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function getOrganizationById($orgId)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);

        return $this->organizations_gateway->getClientById($orgId);

    }

    /**
     * @param $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getOrganizationsByOwner($ownerId, $sorting = null)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        return $this->organizations_gateway->getOrginizationsByOwnerId($ownerId, $sorting);
    }

    /**
     * @param $orgId
     * @param $ownerId
     * @return mixed
     */
    public function getOrganizationByIdAndOwner($orgId, $ownerId) {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);

        return $this->organizations_gateway->getOrgByIdAndOwner($orgId, $ownerId);
    }

    /**
     * @param $organizationInfo
     * @return mixed
     */
    public function updateOrganizationInformation($organizationInfo)
    {
        return $this->organizations_gateway->updateOrganizationInformation($organizationInfo);
    }

    /**
     * @param $orgId
     * @param $logoExtension
     * @return mixed
     */
    public function setLogoExtensionByOrgId($orgId, $logoExtension)
    {
        return $this->organizations_gateway->setLogoExtensionByOrgId($orgId, $logoExtension);
    }

    /**
     * @param $clientId
     * @return mixed
     * @throws Exception
     */
    public function deleteClient($clientId)
    {
        if (empty($clientId)) {
            throw new Exception('Invalid client id');
        }

        return $this->organizations_gateway->deleteClient($clientId);
    }

    /**
     * @param $tmpQuery
     * @param null $sorting
     * @param null $limit
     * @param null $rowLimit
     * @return mixed
     */
    public function getSearchOrganizations($tmpQuery, $sorting = null, $limit = null, $rowLimit = null)
    {
        return $this->organizations_gateway->searchResultOrganizations($tmpQuery, $sorting, $limit, $rowLimit);
    }
}
