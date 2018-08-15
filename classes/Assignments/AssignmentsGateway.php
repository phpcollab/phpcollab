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
     * @param $assignmentData
     * @return mixed
     */
    public function addAssignment($assignmentData)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["assignments"]} 
(task, owner, assigned_to, assigned) 
VALUES 
(:task, :owner, :assigned_to, :assigned)
SQL;
        $this->db->query($sql);
        $this->db->bind("task", $assignmentData["task"]);
        $this->db->bind("owner", $assignmentData["owner"]);
        $this->db->bind("assigned_to", $assignmentData["assigned_to"]);
        $this->db->bind("assigned", $assignmentData["assigned"]);
        return $this->db->execute();

    }

    /**
     * @param $taskId
     * @param null $sorting
     * @return mixed
     */
    public function getAssignmentsByTaskId($taskId, $sorting = null)
    {
        $query = $this->initrequest["assignments"] .  " WHERE ass.task = :task_id" . $this->orderBy( ($sorting) ? $sorting : 'ass.id' );
        $this->db->query($query);
        $this->db->bind(':task_id', $taskId);
        return $this->db->resultset();
    }

    /**
     * @param $newAssignee
     * @param $assignedDate
     * @param $oldAssignee
     * @return mixed
     */
    public function reassignAssignmentByAssignedTo($newAssignee, $assignedDate, $oldAssignee)
    {
        $oldAssignee = explode(',', $oldAssignee);
        // Generate placeholders
        $placeholders = str_repeat ('?, ', count($oldAssignee)-1) . '?';
        $sql = "UPDATE {$this->tableCollab["assignments"]} SET assigned_to = ?, assigned = ? WHERE assigned_to IN($placeholders)";

        // Prepend the project id value
        if (is_array($oldAssignee)) {
            $data = $newAssignee . ',' . $assignedDate . ',' . implode(",", $oldAssignee);
        } else {
            $data = $newAssignee . ',' . $assignedDate . ',' . $oldAssignee;
        }
        $this->db->query($sql);
        return $this->db->execute(explode(',', $data));
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

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["ass_id", "ass_task", "ass_owner", "ass_assigned_to", "ass_comments", "ass_assigned", "ass_mem1_id", "ass_mem1_login", "ass_mem1_name", "ass_mem1_email_work", "ass_mem2_id", "ass_mem2_login", "ass_mem2_name", "ass_mem2_email_work"];
            $pieces = explode(' ', $sorting);

            if ($pieces) {
                $key = array_search($pieces[0], $allowedOrderedBy);

                if ($key !== false) {
                    $order = $allowedOrderedBy[$key];
                    return " ORDER BY $order $pieces[1]";
                }
            }
        }

        return '';
    }

}
