<?php
namespace phpCollab\Admins;

use phpCollab\Database;


/**
 * Class AdminsGateway
 * @package phpCollab\Admins
 */
class AdminsGateway
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
        $query = 'DELETE FROM logs';

        $this->db->query($query);

        return $this->db->execute();
    }

    public function deleteMyCompanyLogo() {
        $query = 'UPDATE organizations SET extension_logo="" WHERE id=1';

        $this->db->query($query);

        return $this->db->execute();
    }

}
