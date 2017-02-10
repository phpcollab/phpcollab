<?php


namespace phpCollab\Tasks;

use phpCollab\Database;

/**
 * Class Tasks
 * @package phpCollab\Tasks
 */
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


    /**
     * @param $userId
     * @param null $sorting
     * @return mixed
     */
    public function getMyTasks($userId, $sorting = null)
    {
        if (isset($sorting)) {
            $sorting = filter_var( (string) $sorting, FILTER_SANITIZE_STRING);
        }
        $userId = filter_var( (string) $userId, FILTER_SANITIZE_STRING);

        $data = $this->tasks_gateway->getMyTasks($userId, $sorting);
        return $data;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getSubtasksAssignedToMe($userId) {
        $userId = filter_var( (string) $userId, FILTER_SANITIZE_STRING);

        return $this->tasks_gateway->getSubtasksAssignedToMe($userId);
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public function getTaskById($taskId)
    {
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        $task = $this->tasks_gateway->getTaskById($taskId);
        return $task;
    }

    /**
     * @param $projectName
     * @return mixed
     */
    public function getTasksByProjectName($projectName)
    {
        $projectName = filter_var($projectName, FILTER_SANITIZE_STRING);
        $task = $this->tasks_gateway->getTasksByProjectName($projectName);
        return $task;
    }

    /**
     * @param int $projectId ID of the project
     * @param int $phaseId ID of parent phase
     * @param string $sorting column to sort on and direction
     * @return mixed
     */
    public function getTasksByProjectIdAndParentPhase($projectId, $phaseId, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $phaseId = filter_var($phaseId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksByProjectIdAndParentPhase($projectId, $phaseId, $sorting);
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getOpenAndCompletedSubTasksAssignedToMe($ownerId, $sorting)
    {
        if (isset($sorting)) {
            $sorting = filter_var( (string) $sorting, FILTER_SANITIZE_STRING);
        }
        $ownerId = filter_var( (string) $ownerId, FILTER_SANITIZE_STRING);

        return $this->tasks_gateway->getOpenAndCompletedSubTasksAssignedToMe($ownerId, $sorting);
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public function getSubTaskById($taskId)
    {
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        $task = $this->tasks_gateway->getSubTaskById($taskId);
        return $task;
    }

    /**
     * @param $parentTaskId
     * @return mixed
     */
    public function getSubtasksByParentTaskId($parentTaskId)
    {
        $parentTaskId = filter_var($parentTaskId, FILTER_VALIDATE_INT);
        $task = $this->tasks_gateway->getSubtasksByParentTaskId($parentTaskId);
        return $task;
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function addToSiteFile($ids)
    {
        return $this->tasks_gateway->addToSiteFile($ids);
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function removeToSiteFile($ids)
    {
        return $this->tasks_gateway->removeToSiteFile($ids);
    }

    /**
     * @param $tasksId
     * @return mixed
     */
    public function publishTasks($tasksId)
    {
        $tasksId = filter_var($tasksId, FILTER_SANITIZE_STRING);
        return $this->publishTasks($tasksId);
    }

    /**
     * @param $tasksId
     * @return mixed
     */
    public function unPublishTasks($tasksId)
    {
        $tasksId = filter_var($tasksId, FILTER_SANITIZE_STRING);
        return $this->unPublishTasks($tasksId);
    }
}