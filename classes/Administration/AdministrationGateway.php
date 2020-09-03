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

    /**
     * Delete logs
     * @return boolean
     */
    public function deleteLogs()
    {
        $query = "DELETE FROM {$this->db->getTableName("logs")}";

        $this->db->query($query);

        return $this->db->execute();
    }

    /**
     * @return mixed
     */
    public function deleteMyCompanyLogo() {
        $query = "UPDATE {$this->db->getTableName("organizations")} organizations SET extension_logo='' WHERE id=1";

        $this->db->query($query);

        return $this->db->execute();
    }

}
