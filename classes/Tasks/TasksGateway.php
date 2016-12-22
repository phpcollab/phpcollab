<?php
namespace phpCollab\Tasks;

use phpCollab\Database;

class TasksGateway
{
    protected $db;
    protected $initrequest;

    /**
     * TasksGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    public function getSubtasksAssignedToMe($userId, $sorting = null)
    {
        $whereStatement = ' WHERE subtas.assigned_to = :user_id';

        $this->db->query($this->initrequest["subtasks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':user_id', $userId);

        return $this->db->resultset();
    }

    public function getTaskById($taskId)
    {
        $whereStatement = " WHERE tas.id = :task_id";

        $this->db->query($this->initrequest["tasks"] . $whereStatement);

        $this->db->bind(':task_id', $taskId);

        return $this->db->single();
    }

    public function getTasksByProjectName($projectName)
    {
        $whereStatement = " WHERE tas.project = :project_name";

        $this->db->query($this->initrequest["tasks"] . $whereStatement);

        $this->db->bind(':project_name', $projectName);

        return $this->db->resultset();
    }

    public function getSubTaskById($subtaskId)
    {
        $whereStatement = " WHERE subtas.id = :sub_task_id";

        $this->db->query($this->initrequest["subtasks"] . $whereStatement);

        $this->db->bind(':sub_task_id', $subtaskId);

        return $this->db->single();
    }


    public function getSubtasksByParentTaskId($parentTaskId)
    {
        $whereStatement = " WHERE task = :parent_task_id";

        $this->db->query($this->initrequest["tasks"] . $whereStatement);

        $this->db->bind(':parent_task_id', $parentTaskId);

        return $this->db->single();
    }


    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        return (!is_null($sorting)) ? ' ORDER BY ' . filter_var($sorting, FILTER_SANITIZE_STRING) : '';
    }

}