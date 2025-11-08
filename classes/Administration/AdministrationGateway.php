<?php
namespace phpCollab\Administration;

use phpCollab\Database;
use phpCollab\RequestData;


/**
 * Class AdminsGateway
 * @package phpCollab\Admins
 */
class AdministrationGateway
{
    protected $db;
    protected $initrequest;

    /**
     * Reports constructor.
     * @param Database $db
     */
    public function __construct(Database $db, RequestData $requestData)
    {
        $this->db = $db;
        $this->initrequest = $requestData->all(); // Maintain BC with existing code
    }
}
