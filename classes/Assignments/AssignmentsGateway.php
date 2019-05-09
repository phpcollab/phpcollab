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
     * @param $taskId
     * @param $taskOwner
     * @param $assignedTo
     * @param $assignedDate
     * @param null $comments
     * @return mixed
     */
    public function addAssignment($taskId, $taskOwner, $assignedTo, $assignedDate, $comments = null)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["assignments"]} 
(task, owner, assigned_to, assigned, comments) 
VALUES 
(:task, :owner, :assigned_to, :assigned, :comments)
SQL;
        $this->db->query($sql);
        $this->db->bind("task", $taskId);
        $this->db->bind("owner", $taskOwner);
        $this->db->bind("assigned_to", $assignedTo);
        $this->db->bind("assigned", $assignedDate);
        $this->db->bind("comments", $comments);
        $this->db->execute();
        return $this->db->lastInsertId();

    }

    /**
     * @param $taskId
     * @param null $sorting
     * @return mixed
     */
    public function getAssignmentsByTaskId($taskId, $sorting = null)
    {
        $query = $this->initrequest["assignments"] .  " WHERE ass.task = :task_id" . $this->orderBy( (!empty($sorting)) ? $sorting : 'ass.id' );
        $this->db->query($query);
        $this->db->bind(':task_id', $taskId);
        return $this->db->resultset();
    }

    /**
     * @param $subtaskId
     * @param null $sorting
     * @return mixed
     */
    public function getAssignmentsBySubtaskId($subtaskId, $sorting = null)
    {
        $query = $this->initrequest["assignments"] .  " WHERE ass.subtask = :subtask_id" . $this->orderBy( (!empty($sorting)) ? $sorting : 'ass.id' );
        $this->db->query($query);
        $this->db->bind(':subtask_id', $subtaskId);
        return $this->db->resultset();
    }

    /**
     * @return mixed
     */
    public function getLastId()
    {
        $sql = "SELECT id FROM {$this->tableCollab["assignments"]} ORDER BY id DESC LIMIT 1";
        $this->db->query($sql);
        return $this->db->single()["id"];
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
            $allowedOrderedBy = ["ass_id", "ass_task", "ass_owner", "ass_assigned_to", "ass_comments", "ass.comments", "ass_assigned", "ass.assigned", "ass_mem1_id", "ass_mem1_login", "ass_mem1_name", "ass_mem1_email_work", "ass_mem2_id", "ass_mem2_login", "ass_mem2_name", "ass_mem2_email_work", "mem1.login", "mem2.login"];
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
