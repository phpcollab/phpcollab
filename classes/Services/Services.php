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
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->services_gateway = new ServicesGateway($this->db);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getAllServices($sorting = null)
    {
        return $this->services_gateway->getAllServices($sorting);
    }

}