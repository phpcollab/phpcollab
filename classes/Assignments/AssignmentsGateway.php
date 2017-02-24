<?php


namespace phpCollab\Assignments;

use phpCollab\Database;

/**
 * Class AssignmentsGateway
 * @package phpCollab\Assignments
 */
class AssignmentsGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * AssignmentsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $newAssignee
     * @param $assignedDate
     * @param $oldAssignee
     * @return mixed
     */
    public function reassignAssignmentByAssignedTo($newAssignee, $assignedDate, $oldAssignee)
    {
        // Generate placeholders
        $placeholders = str_repeat ('?, ', count($oldAssignee)-1) . '?';
        $sql = "UPDATE {$this->tableCollab["assignments"]} SET assigned_to = ?, assigned = ? WHERE assigned_to IN($placeholders)";

        // Prepend the project id value
        if (is_array($oldAssignee)) {
            $data = $newAssignee . ',' . $assignedDate . ',' . implode($oldAssignee);
        } else {
            $data = $newAssignee . ',' . $assignedDate . ',' . $oldAssignee;
        }
        $this->db->query($sql);
        $this->db->execute(explode(',', $data));
        return $this->db->fetchAll();
    }

    /**
     * @param $assignmentIds
     * @return mixed
     */
    public function deleteAssignments($assignmentIds)
    {
        $assignmentIds = explode(',', $assignmentIds);
        $placeholders = str_repeat ('?, ', count($assignmentIds)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['assignments']} WHERE task IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($assignmentIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteAssignmentsByProjectId($projectIds)
    {
        $projectIds = explode(',', $projectIds);
        $placeholders = str_repeat ('?, ', count($projectIds)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['assignments']} WHERE task IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectIds);
    }

    /**
     * @param $subtaskIds
     * @return mixed
     */
    public function deleteAssignmentsBySubtask($subtaskIds)
    {
        $subtaskIds = explode(',', $subtaskIds);
        $placeholders = str_repeat ('?, ', count($subtaskIds)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['assignments']} WHERE subtask IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($subtaskIds);
    }


}
