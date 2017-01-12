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
            $sorting = filter_var( (string) $sorting, FILTER_SANITIZE_STRING);
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

    public function getOpenAndCompletedTasksAssignedToMe($ownerId, $sorting)
    {
        if (isset($sorting)) {
            $sorting = filter_var( (string) $sorting, FILTER_SANITIZE_STRING);
        }
        $ownerId = filter_var( (string) $ownerId, FILTER_SANITIZE_STRING);

        return $this->tasks_gateway->getOpenAndCompletedTasksAssignedToMe($ownerId, $sorting);
    }

    public function getSubTaskById($taskId)
    {
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        $task = $this->tasks_gateway->getSubTaskById($taskId);
        return $task;
    }

    public function getSubtasksByParentTaskId($parentTaskId)
    {
        $parentTaskId = filter_var($parentTaskId, FILTER_VALIDATE_INT);
        $task = $this->tasks_gateway->getSubtasksByParentTaskId($parentTaskId);
        return $task;
    }

    public function addToSiteFile($ids)
    {
        return $this->tasks_gateway->addToSiteFile($ids);
    }

    public function removeToSiteFile($ids)
    {
        return $this->tasks_gateway->removeToSiteFile($ids);
    }

}