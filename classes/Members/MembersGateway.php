<?php


namespace phpCollab\Members;

use phpCollab\Database;

class MembersGateway
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

    public function getAllMembers()
    {
//        $sorting = 'name';

        $this->db->query($this->initrequest["members"]);

        return $this->db->resultset();
    }


}