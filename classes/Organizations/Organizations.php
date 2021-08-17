<?php


namespace phpCollab\Organizations;

use Exception;
use InvalidArgumentException;
use Laminas\Escaper\Escaper;
use phpCollab\Database;
use phpCollab\Util;

/**
 * Class Organizations
 * @package phpCollab\Organizations
 */
class Organizations
{
    protected $organizations_gateway;
    protected $db;
    protected $escaper;

    /**
     * Organizations constructor.
     * @param Database $database
     * @param Escaper $escaper
     */
    public function __construct(Database $database, Escaper $escaper)
    {
        $this->db = $database;
        $this->escaper = $escaper;
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
    public function addClient(
        $name,
        $address,
        $phone,
        $url,
        $email,
        $comments,
        $owner,
        $hourlyRate,
        $extension = null,
        $created = null
    ): string {
        if (is_null($extension)) {
            $extension = '';
        }

        if (is_null($created)) {
            $created = date('Y-m-d h:i');
        }

        return $this->organizations_gateway->addClientOrganization($name, $address, $phone, $url, $email, $comments,
            $owner, $hourlyRate, $extension, $created);
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
        return $this->organizations_gateway->updateClientOrganization($clientId, $name, $address, $phone, $url, $email,
            $comments, $owner, $hourlyRate);
    }

    /**
     * @param $clientName
     * @return bool
     */
    public function checkIfClientExistsByName($clientName): bool
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
     * @param int $orgId
     * @return mixed
     */
    public function getOrganizationById(int $orgId)
    {
        if (empty($orgId)) {
            throw new InvalidArgumentException('Invalid Organization ID');
        }

        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);

        $org = $this->organizations_gateway->getClientById($orgId);

        if ($org) {
            $org = $this->escapeOrg($org);
        }

        return $org;

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
    public function getOrganizationByIdAndOwner($orgId, $ownerId)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);

        return $this->organizations_gateway->getOrgByIdAndOwner($orgId, $ownerId);
    }

    /**
     * @param string $name
     * @param string|null $address
     * @param string|null $phone
     * @param string|null $url
     * @param string|null $email
     * @param string|null $comments
     * @return mixed
     */
    public function updateOrganizationInformation(
        string $name,
        string $address = null,
        string $phone = null,
        string $url = null,
        string $email = null,
        string $comments = null
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Organization Name is empty');
        }

        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('URL is invalid');
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('EMail is invalid');
        }

        return $this->organizations_gateway->updateOrganizationInformation($name, $address, $phone, $url, $email,
            $comments);
    }

    /**
     * @param int $orgId
     * @param string $logoExtension
     * @return mixed
     */
    public function setLogoExtensionByOrgId(int $orgId, string $logoExtension)
    {
        if (empty($orgId) || !filter_var($orgId, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Invalid Org ID');
        }

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

    /**
     * @param $org
     * @return array|mixed
     */
    protected function escapeOrg($org)
    {
        if (is_array($org)) {
            $org["org_name"] = $this->escaper->escapeHtml($org["org_name"]);
            if (!empty($org["org_address1"])) {
                $org["org_address1"] = $this->escaper->escapeHtml($org["org_address1"]);
            }
            if (!empty($org["org_phone"])) {
                $org["org_phone"] = $this->escaper->escapeHtml($org["org_phone"]);
            }
            if (!empty($org["org_comments"])) {
                $org["org_comments"] = $this->escaper->escapeHtml($org["org_comments"]);
            }
        }
        return $org;
    }
}
