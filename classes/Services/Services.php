<?php


namespace phpCollab\Services;

use phpCollab\Database;

/**
 * Class Services
 * @package phpCollab\Services
 */
class Services
{
    protected $services_gateway;
    protected $db;

    /**
     * Services constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->services_gateway = new ServicesGateway($this->db);
    }

    /**
     * @param $name
     * @param $displayName
     * @param $hourlyRate
     * @return mixed
     */
    public function addService($name, $displayName, $hourlyRate)
    {
        return $this->services_gateway->addService($name, $displayName, $hourlyRate);
    }

    /**
     * @param $serviceId
     * @return mixed
     */
    public function getService($serviceId)
    {
        return $this->services_gateway->getServiceById($serviceId);
    }

    /**
     * @param $serviceIds
     * @param null $sorting
     * @return mixed
     */
    public function getServicesByIds($serviceIds, $sorting = null)
    {
        return $this->services_gateway->getServicesById($serviceIds, $sorting);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getAllServices($sorting = null)
    {
        return $this->services_gateway->getAllServices($sorting);
    }

    /**
     * @param $serviceIds
     * @return mixed
     */
    public function deleteServices($serviceIds)
    {
        return $this->services_gateway->deleteServices($serviceIds);
    }

    /**
     * @param $serviceId
     * @param $name
     * @param $displayName
     * @param $hourlyRate
     * @return mixed
     */
    public function updateService($serviceId, $name, $displayName, $hourlyRate)
    {
        return $this->services_gateway->updateService($serviceId, $name, $displayName, $hourlyRate);
    }

}
