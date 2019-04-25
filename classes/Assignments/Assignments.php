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
     * @param $taskOwner
     * @param $assignedTo
     * @param $assignedDate
     * @param null $comments
     * @return mixed
     */
    public function addAssignment($taskId, $taskOwner, $assignedTo, $assignedDate, $comments = null)
    {
        return $this->assignments_gateway->addAssignment($taskId, $taskOwner, $assignedTo, $assignedDate, $comments);
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
     * @param $subtaskId
     * @param null $sorting
     * @return mixed
     */
    public function getAssignmentsBySubtaskId($subtaskId, $sorting = null)
    {
        return $this->assignments_gateway->getAssignmentsBySubtaskId($subtaskId, $sorting);
    }

    /**
     * @return mixed
     */
    public function getLastEntryId()
    {
        return $this->assignments_gateway->getLastId();
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
