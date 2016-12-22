<?php


namespace phpCollab\Tasks;

use phpCollab\Database;

class Tasks
{
    protected $tasks_gateway;
    protected $db;

    /**
     * Tasks constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->tasks_gateway = new TasksGateway($this->db);
    }


    public function getMyTasks($userId, $sorting = null)
    {
        if (isset($sorting)) {
            // Todo: wire this up

        }

        $userId = filter_var( (string) $userId, FILTER_SANITIZE_STRING);

        $data = $this->tasks_gateway->getMyTasks($userId, $sorting);
        return $data;
    }

    public function getSubtasksAssignedToMe($userId) {
        $userId = filter_var( (string) $userId, FILTER_SANITIZE_STRING);

        return $this->tasks_gateway->getSubtasksAssignedToMe($userId);
    }

    public function getTaskById($taskId)
    {
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        $task = $this->tasks_gateway->getTaskById($taskId);
        return $task;
    }

    public function getTasksByProjectName($projectName)
    {
        $projectName = filter_var($projectName, FILTER_SANITIZE_STRING);
        $task = $this->tasks_gateway->getTasksByProjectName($projectName);
        return $task;
    }

}