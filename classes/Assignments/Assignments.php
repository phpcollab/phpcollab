<?php


namespace phpCollab\Assignments;

use phpCollab\Database;

/**
 * Class Assignments
 * @package phpCollab
 */
class Assignments
{
    protected $assignments_gateway;
    protected $db;

    /**
     * Assignments constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->assignments_gateway = new AssignmentsGateway($this->db);
    }

    public function deleteAssignments($assignmentIds)
    {
        return $this->assignments_gateway->deleteAssignments($assignmentIds);
    }

}
