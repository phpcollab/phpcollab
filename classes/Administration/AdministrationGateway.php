<?php
namespace phpCollab\Administration;

use phpCollab\Database;


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
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }
}
