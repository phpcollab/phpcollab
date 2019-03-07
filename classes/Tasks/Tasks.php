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
    protected $tasksCount;

    /**
     * Tasks constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->tasks_gateway = new TasksGateway($this->db);
    }

    /**
     * @return mixed
     */
    public function getTasksCount()
    {
        return $this->tasksCount;
    }

    /**
     * @param mixed $tasksCount
     */
    public function setTasksCount($tasksCount)
    {
        $this->tasksCount = $tasksCount;
    }

    /**
     * @param $userId
     * @param null $sorting
     * @return mixed
     */
    public function getMyTasks($userId, $sorting = null)
    {
        if (isset($sorting)) {
            $sorting = filter_var((string)$sorting, FILTER_SANITIZE_STRING);
        }
        $userId = filter_var((string)$userId, FILTER_SANITIZE_STRING);

        $data = $this->tasks_gateway->getMyTasks($userId, $sorting);
        return $data;
    }

    /**
     * @param $userId
     * @param null $subtasks
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getAllMyTasks($userId, $subtasks = null, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        return $this->tasks_gateway->getAllMyTasks($userId, $subtasks, $startRow, $rowsLimit, $sorting);
    }

    /**
     * @param $userId
     * @return int
     */
    public function getClientUserTasksCount($userId)
    {
        $data = $this->tasks_gateway->getClientUserTasksIn($userId);
        return count($data);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getSubtasksAssignedToMe($userId)
    {
        $userId = filter_var((string)$userId, FILTER_SANITIZE_STRING);

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
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getTasksByProjectId($projectId, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        $task = $this->tasks_gateway->getTasksByProjectId($projectId, $startRow, $rowsLimit, $sorting);
        return $task;
    }

    /**
     * @param $projectId
     * @return int
     */
    public function getCountAllTasksForProject($projectId)
    {
        return count($this->getTasksByProjectId($projectId));
    }

    /**
     * @param $userId
     * @return int
     */
    public function getClientUserTasks($userId)
    {
        $tasks = $this->tasks_gateway->getClientUserTasks($userId);
        return count($tasks);
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
     * @param $phaseId
     * @param $projectId
     * @return int
     */
    public function getCountOpenTasksByPhaseAndProject($phaseId, $projectId)
    {
        return count($this->getTasksByProjectIdAndParentPhase($projectId, $phaseId));
    }

    /**
     * @param $phaseId
     * @param $projectId
     * @return int
     */
    public function getCountUncompletedTasks($phaseId, $projectId)
    {
        $tasks = $this->getTasksByProjectIdAndParentPhase($projectId, $phaseId);
        $tasks = array_filter($tasks, function ($item) {
            return $item["tas_status"] == 2 || $item["tas_status"] == 3 || $item["tas_status"] == 4;
        });
        return count($tasks);
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
            $sorting = filter_var((string)$sorting, FILTER_SANITIZE_STRING);
        }
        $ownerId = filter_var((string)$ownerId, FILTER_SANITIZE_STRING);

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
     * @param $taskId
     * @param $assignedDate
     * @return mixed
     */
    public function updateAssignedDate($taskId, $assignedDate)
    {
        $taskId = filter_var((int)$taskId, FILTER_SANITIZE_NUMBER_INT);

        return $this->tasks_gateway->assignTaskTo($taskId, $assignedDate);
    }

    /**
     * @param $taskName
     * @param $description
     * @param $assignedTo
     * @param $status
     * @param $priority
     * @param $startDate
     * @param $dueDate
     * @param $estimatedTime
     * @param $actualTime
     * @param $comments
     * @param $modifiedDate
     * @param $completion
     * @param $parentPhase
     * @param $published
     * @param $invoicing
     * @param $workedHours
     * @param $taskId
     * @return mixed
     */
    public function updateTask($taskName, $description, $assignedTo, $status, $priority, $startDate, $dueDate,
                               $estimatedTime, $actualTime, $comments, $modifiedDate, $completion, $parentPhase, $published, $invoicing,
                               $workedHours, $taskId)
    {
        return $this->tasks_gateway->updateTask($taskName, $description, $assignedTo, $status, $priority, $startDate, $dueDate,
            $estimatedTime, $actualTime, $comments, $modifiedDate, $completion, $parentPhase, $published, $invoicing,
            $workedHours, $taskId);
    }

    /**
     * @param $newAsignee
     * @param $assignedTo
     * @return mixed
     */
    public function setTasksAssignedToWhereAssignedToIn($newAsignee, $assignedTo)
    {
        return $this->tasks_gateway->setTasksAssignedToWhereAssignedToIn($newAsignee, $assignedTo);
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
     * @param $taskData
     * @return mixed
     */
    public function addSubTask($taskData)
    {
        return $this->tasks_gateway->addSubTask($taskData);
    }

    /**
     * @param $projectId
     * @param $taskId
     * @return mixed
     */
    public function setProjectByTaskId($projectId, $taskId)
    {
        return $this->tasks_gateway->setProjectByTaskId($projectId, $taskId);
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
     * @param $taskId
     * @param $startDate
     * @return mixed
     */
    public function setStartDate($taskId, $startDate)
    {
        return $this->tasks_gateway->setStartDate($taskId, $startDate);
    }

    /**
     * @param $taskId
     * @param $dueDate
     * @return mixed
     */
    public function setDueDate($taskId, $dueDate)
    {
        return $this->tasks_gateway->setDueDate($taskId, $dueDate);
    }

    /**
     * @param $taskId
     * @param $assignedTo
     * @return mixed
     */
    public function setAssignedTo($taskId, $assignedTo)
    {
        return $this->tasks_gateway->setAssignedTo($taskId, $assignedTo);
    }

    /**
     * @param $taskId
     * @param $assignedDate
     * @return mixed
     */
    public function setAssignedDate($taskId, $assignedDate)
    {
        return $this->tasks_gateway->setAssignedDate($taskId, $assignedDate);
    }

    /**
     * @param $taskId
     * @param $status
     * @return mixed
     */
    public function setStatus($taskId, $status)
    {
        return $this->tasks_gateway->setStatus($taskId, $status);
    }

    /**
     * @param $taskId
     * @param $completion
     * @return mixed
     */
    public function setCompletion($taskId, $completion)
    {
        return $this->tasks_gateway->setCompletion($taskId, $completion);
    }

    /**
     * @param $taskId
     * @param $priority
     * @return mixed
     */
    public function setPriority($taskId, $priority)
    {
        return $this->tasks_gateway->setPriority($taskId, $priority);

    }

    /**
     * @param $taskId
     * @param $comment
     * @return mixed
     */
    public function setComment($taskId, $comment)
    {
        return $this->tasks_gateway->setComment($taskId, $comment);
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public function setModifiedDate($taskId)
    {
        return $this->tasks_gateway->setModifiedDate($taskId);
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
