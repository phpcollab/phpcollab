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
     * @param $assignedTo
     * @return mixed
     */
    public function getTasksAssignedTo($assignedTo)
    {
        $assignedTo = filter_var($assignedTo, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksAssignedTo($assignedTo);
    }

    /**
     * @param $assignedTo
     * @return mixed
     */
    public function getTasksAssignedToMeThatAreNotCompletedOrSuspended($assignedTo)
    {
        $assignedTo = filter_var($assignedTo, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksAssignedToMeThatAreNotCompletedOrSuspended($assignedTo);
    }

    /**
     * @param $taskIds
     * @return mixed
     */
    public function getTasksById($taskIds)
    {
        return $this->tasks_gateway->getTasksById($taskIds);
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
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getTasksByProjectId($projectId, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        $task = $this->tasks_gateway->getTasksByProjectId($projectId, $sorting);
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
     * @param $date
     * @param $assignedTo
     * @return mixed
     */
    public function getTasksWhereStartDateAndEndDateLikeNotPublishedAndAssignedToUserId($date, $assignedTo)
    {
        return $this->getTasksWhereStartDateAndEndDateLikeNotPublishedAndAssignedToUserId($date, $assignedTo);
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
     * @param $subTaskId
     * @return mixed
     */
    public function getSubTaskByIdIn($subTaskId)
    {
        return $this->tasks_gateway->getSubTaskByIdIn($subTaskId);
    }

    /**
     * @param $parentTaskId
     * @param null $sorting
     * @return mixed
     */
    public function getSubtasksByParentTaskId($parentTaskId, $sorting = null)
    {
        $parentTaskId = filter_var($parentTaskId, FILTER_VALIDATE_INT);
        $task = $this->tasks_gateway->getSubtasksByParentTaskId($parentTaskId, $sorting);
        return $task;
    }

    /**
     * @param $parentTaskId
     * @return mixed
     */
    public function getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmptyAndNotPublished($parentTaskId)
    {
        return $this->tasks_gateway->getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmptyAndNotPublished($parentTaskId);
    }

    /**
     * @param $parentTaskId
     * @return mixed
     */
    public function getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmpty($parentTaskId)
    {
        return $this->tasks_gateway->getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmpty($parentTaskId);
    }

    /**
     * @param $taskDate
     * @param $assignedTo
     * @return mixed
     */
    public function getTasksByStartDateEndDateAssignedTo($taskDate, $assignedTo)
    {
        return $this->tasks_gateway->getTasksByStartDateEndDateAssignedTo($taskDate, $assignedTo);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getTasksByProjectIdWhereStartAndEndAreNotEmpty($projectId)
    {
        return $this->tasks_gateway->getTasksByProjectIdWhereStartAndEndAreNotEmpty($projectId);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getTasksByProjectIdWhereStartAndEndAreNotEmptyAndNotPublished($projectId)
    {
        return $this->tasks_gateway->getTasksByProjectIdWhereStartAndEndAreNotEmptyAndNotPublished($projectId);
    }

    /**
     * @param $projectId
     * @param $phaseId
     * @return mixed
     */
    public function getTasksByProjectIdAndParentPhaseAndStartEndDateNotBlank($projectId, $phaseId)
    {
        return $this->tasks_gateway->getTasksByProjectIdAndParentPhaseAndStartEndDateNotBlank($projectId, $phaseId);
    }

    /**
     * @param $newAsignee
     * @param $assignedTo
     */
    public function setTasksAssignedToWhereAssignedToIn($newAsignee, $assignedTo)
    {
        $this->tasks_gateway->setTasksAssignedToWhereAssignedToIn($newAsignee, $assignedTo);
    }

    /**
     * @param $taskData
     * @return string
     */
    public function addTask($taskData)
    {
        return $this->tasks_gateway->addTask($taskData);
    }

    /**
     * @param $taskId
     * @param $date
     * @return mixed
     */
    public function setCompletionDateForTaskById($taskId, $date)
    {
        return $this->tasks_gateway->setCompletionDateForTaskById($taskId, $date);
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
        return $this->tasks_gateway->publishTasks($tasksId);
    }

    /**
     * @param $tasksId
     * @return mixed
     */
    public function unPublishTasks($tasksId)
    {
        $tasksId = filter_var($tasksId, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->unPublishTasks($tasksId);
    }

    /**
     * @param $oldOwner
     * @param $newOwner
     */
    public function reassignTasks($oldOwner, $newOwner)
    {
        $this->tasks_gateway->reassignTasks($oldOwner, $newOwner);
    }

    /**
     * @param $taskIds
     * @return mixed
     */
    public function deleteTasks($taskIds)
    {
        return $this->tasks_gateway->deleteTasks($taskIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteTasksByProjectId($projectIds)
    {
        return $this->tasks_gateway->deleteTasksByProject($projectIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteSubtasksByProjectId($projectIds)
    {
        return $this->tasks_gateway->deleteSubtasksByProjectId($projectIds);
    }

    /**
     * @param $subTaskIds
     * @return mixed
     */
    public function deleteSubTasks($subTaskIds)
    {
        return $this->tasks_gateway->deleteSubTasks($subTaskIds);
    }

    /**
     * @param $subtaskIds
     * @return mixed
     */
    public function deleteSubTasksById($subtaskIds)
    {
        return $this->tasks_gateway->deleteSubTasksById($subtaskIds);
    }
}
