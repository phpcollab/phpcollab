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

    /**
     * @param $taskId
     * @param null $sorting
     * @return mixed
     */
    public function getAssignmentsByTaskId($taskId, $sorting = null)
    {
        return $this->assignments_gateway->getAssignmentsByTaskId($taskId, $sorting);
    }

    /**
     * @param $newAssignee
     * @param $assignedDate
     * @param $oldAssignee
     * @return mixed
     */
    public function reassignAssignmentByAssignedTo($newAssignee, $assignedDate, $oldAssignee)
    {
        return $this->assignments_gateway->reassignAssignmentByAssignedTo($newAssignee, $assignedDate, $oldAssignee);
    }

    /**
     * @param $assignmentIds
     * @return mixed
     */
    public function deleteAssignments($assignmentIds)
    {
        return $this->assignments_gateway->deleteAssignments($assignmentIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteAssignmentsByProjectId($projectIds)
    {
        return $this->assignments_gateway->deleteAssignmentsByProjectId($projectIds);
    }

    /**
     * @param $subtaskIds
     * @return mixed
     */
    public function deleteAssignmentsBySubtasks($subtaskIds)
    {
        return $this->assignments_gateway->deleteAssignmentsBySubtask($subtaskIds);
    }

}
