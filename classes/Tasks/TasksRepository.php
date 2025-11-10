<?php

namespace phpCollab\Tasks;

use phpCollab\Database;
use phpCollab\RequestData;

/**
 * Class TasksRepository
 *
 * Repository Pattern implementation for Tasks data access.
 * Wraps TasksGateway and provides domain-friendly interface.
 *
 * @package phpCollab\Tasks
 */
class TasksRepository implements TasksRepositoryInterface
{
    private TasksGateway $gateway;

    /**
     * TasksRepository constructor.
     *
     * @param Database $database Database connection
     * @param RequestData $requestData Request data for gateway
     */
    public function __construct(Database $database, RequestData $requestData)
    {
        $this->gateway = new TasksGateway($database, $requestData);
    }

    // =========================================================================
    // Query Methods - Tasks
    // =========================================================================

    public function findById(int $taskId): ?array
    {
        $result = $this->gateway->getTaskById($taskId);
        return $result ?: null;
    }

    public function findByIds(string $taskIds): array
    {
        return $this->gateway->getTasksById($taskIds);
    }

    public function findMyTasks(int $userId, ?string $sorting = null): array
    {
        return $this->gateway->getMyTasks($userId, $sorting);
    }

    public function findAllMyTasks(int $userId, ?string $subtasks = null, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array
    {
        return $this->gateway->getAllMyTasks($userId, $subtasks, $startRow, $rowsLimit, $sorting);
    }

    public function findTasksAssignedTo(int $assignedTo): array
    {
        return $this->gateway->getTasksAssignedTo($assignedTo);
    }

    public function findActiveTasksAssignedTo(int $assignedTo): array
    {
        return $this->gateway->getTasksAssignedToMeThatAreNotCompletedOrSuspended($assignedTo);
    }

    public function findByProjectId(int $projectId, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array
    {
        return $this->gateway->getTasksByProjectId($projectId, $startRow, $rowsLimit, $sorting);
    }

    public function findByProjectName(string $projectName): array
    {
        return $this->gateway->getTasksByProjectName($projectName);
    }

    public function findByProjectAndOwnerOrPublished(int $projectId, int $ownerId, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array
    {
        return $this->gateway->getTasksByProjectIdAndOwnerOrPublished($projectId, $ownerId, $startRow, $rowsLimit, $sorting);
    }

    public function findByProjectAndPhase(int $projectId, int $phaseId, ?string $sorting = null): array
    {
        return $this->gateway->getTasksByProjectIdAndParentPhase($projectId, $phaseId, $sorting);
    }

    public function findOpenPhaseTasks(int $phaseId): array
    {
        return $this->gateway->getOpenPhaseTasks($phaseId);
    }

    public function findClientUserTasks(int $userId): array
    {
        return $this->gateway->getClientUserTasks($userId);
    }

    public function findClientUserTasksIn(int $userId): array
    {
        return $this->gateway->getClientUserTasksIn($userId);
    }

    public function findProjectSiteClientTasks(int $projectId, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array
    {
        return $this->gateway->getProjectSiteClientTasks($projectId, $startRow, $rowsLimit, $sorting);
    }

    public function findTeamTasks(int $projectId, ?string $sorting = null): array
    {
        return $this->gateway->getTeamTasks($projectId, $sorting);
    }

    public function findByDateAndAssignedTo(string $taskDate, int $assignedTo): array
    {
        return $this->gateway->getTasksByStartDateEndDateAssignedTo($taskDate, $assignedTo);
    }

    public function findByProjectWithDates(int $projectId): array
    {
        return $this->gateway->getTasksByProjectIdWhereStartAndEndAreNotEmpty($projectId);
    }

    public function findByProjectWithDatesNotPublished(int $projectId): array
    {
        return $this->gateway->getTasksByProjectIdWhereStartAndEndAreNotEmptyAndNotPublished($projectId);
    }

    public function findByProjectPhaseWithDates(int $projectId, int $phaseId): array
    {
        return $this->gateway->getTasksByProjectIdAndParentPhaseAndStartEndDateNotBlank($projectId, $phaseId);
    }

    public function search(string $query, ?string $sorting = null, ?int $limit = null, ?int $rowLimit = null): array
    {
        return $this->gateway->searchResultTasks($query, $sorting, $limit, $rowLimit);
    }

    public function findForReport(string $sql): array
    {
        return $this->gateway->getReportTasks($sql);
    }

    // =========================================================================
    // Query Methods - Subtasks
    // =========================================================================

    public function findSubtaskById(int $subtaskId): ?array
    {
        $result = $this->gateway->getSubTaskById($subtaskId);
        return $result ?: null;
    }

    public function findSubtaskByIds(int $subtaskId): array
    {
        return $this->gateway->getSubTaskByIdIn($subtaskId);
    }

    public function findSubtasksAssignedTo(int $userId): array
    {
        return $this->gateway->getSubtasksAssignedToMe($userId);
    }

    public function findActiveSubtasksAssignedTo(int $ownerId, ?string $sorting = null): array
    {
        return $this->gateway->getOpenAndCompletedSubTasksAssignedToMe($ownerId, $sorting);
    }

    public function findSubtasksByParent(int $parentTaskId, ?string $sorting = null): array
    {
        return $this->gateway->getSubtasksByParentTaskId($parentTaskId, $sorting);
    }

    public function findSubtasksByParents(string $parentTaskIds, ?string $sorting = null): array
    {
        return $this->gateway->getSubtasksByParentTaskIdIn($parentTaskIds, $sorting);
    }

    public function findPublishedSubtasksByParent(string $parentTaskId, ?string $sorting = null): array
    {
        return $this->gateway->getPublishedSubtasksByParentTaskId($parentTaskId, $sorting);
    }

    public function findSubtasksByParentWithDatesNotPublished(int $parentTaskId): array
    {
        return $this->gateway->getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmptyAndNotPublished($parentTaskId);
    }

    public function findSubtasksByParentWithDates(int $parentTaskId): array
    {
        return $this->gateway->getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmpty($parentTaskId);
    }

    public function findSubtasksByProjectAndOwnerOrPublished(int $projectId, int $ownerId, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array
    {
        return $this->gateway->getSubTasksByProjectIdAndOwnerOrPublished($projectId, $ownerId, $startRow, $rowsLimit, $sorting);
    }

    public function searchSubtasks(string $query, ?string $sorting = null, ?int $limit = null, ?int $rowLimit = null): array
    {
        return $this->gateway->searchResultSubTasks($query, $sorting, $limit, $rowLimit);
    }

    public function calculateSubtaskAverage(int $taskId)
    {
        return $this->gateway->recalculateSubtaskAverage($taskId);
    }

    // =========================================================================
    // Command Methods - Tasks
    // =========================================================================

    public function create(
        int $projectId,
        int $owner,
        string $name,
        ?string $description = null,
        int $assignedTo = 0,
        int $status = 0,
        int $priority = 0,
        ?string $startDate = null,
        ?string $dueDate = null,
        ?float $estimatedTime = null,
        ?float $actualTime = null,
        ?string $comments = null,
        int $published = 0,
        int $completion = 0,
        int $parentPhase = 0,
        int $invoicing = 0,
        float $workedHours = 0.00,
        ?string $assignedDate = null
    ): string {
        return $this->gateway->addTask(
            $projectId,
            $owner,
            $name,
            $description,
            $assignedTo,
            $status,
            $priority,
            $startDate,
            $dueDate,
            $estimatedTime,
            $actualTime,
            $comments,
            $published,
            $completion,
            $parentPhase,
            $invoicing,
            $workedHours,
            $assignedDate
        );
    }

    public function update(
        int $id,
        string $name,
        ?string $description = null,
        int $assignedTo = 0,
        int $status = 0,
        int $priority = 0,
        ?string $startDate = null,
        ?string $dueDate = null,
        ?float $estimatedTime = null,
        ?float $actualTime = null,
        ?string $comments = null,
        int $published = 0,
        int $completion = 0,
        int $parentPhase = 0,
        int $invoicing = 0,
        float $workedHours = 0.00,
        ?string $modifiedDate = null
    ): void {
        $this->gateway->updateTask(
            $id,
            $name,
            $description,
            $assignedTo,
            $status,
            $priority,
            $startDate,
            $dueDate,
            $estimatedTime,
            $actualTime,
            $comments,
            $published,
            $completion,
            $parentPhase,
            $invoicing,
            $workedHours,
            $modifiedDate
        );
    }

    public function updateStatus(int $taskId, int $status): void
    {
        $this->gateway->setStatus($taskId, $status);
    }

    public function updateName(int $taskId, string $taskName): void
    {
        $this->gateway->setName($taskId, $taskName);
    }

    public function updateStartDate(int $taskId, string $startDate): void
    {
        $this->gateway->setStartDate($taskId, $startDate);
    }

    public function updateDueDate(int $taskId, string $dueDate): void
    {
        $this->gateway->setDueDate($taskId, $dueDate);
    }

    public function updateAssignedTo(int $taskId, int $assignedTo): void
    {
        $this->gateway->setAssignedTo($taskId, $assignedTo);
    }

    public function updateAssignedDate(int $taskId, string $assignedDate): void
    {
        $this->gateway->setAssignedDate($taskId, $assignedDate);
    }

    public function updateCompletion(int $taskId, int $completion): void
    {
        $this->gateway->setCompletion($taskId, $completion);
    }

    public function updatePriority(int $taskId, int $priority): void
    {
        $this->gateway->setPriority($taskId, $priority);
    }

    public function updateComment(int $taskId, string $comment): void
    {
        $this->gateway->setComment($taskId, $comment);
    }

    public function updateModifiedDate(int $taskId): void
    {
        $this->gateway->setModifiedDate($taskId);
    }

    public function updateParentPhase(int $taskId, int $phase): void
    {
        $this->gateway->setParentPhase($taskId, $phase);
    }

    public function updateProject(int $projectId, int $taskId): void
    {
        $this->gateway->setProjectByTaskId($projectId, $taskId);
    }

    public function updateCompletionDate(int $taskId, string $date): void
    {
        $this->gateway->setCompletionDateForTaskById($taskId, $date);
    }

    public function reassign(int $oldOwner, int $newOwner): void
    {
        $this->gateway->reassignTasks($oldOwner, $newOwner);
    }

    public function reassignMultiple(int $newAssignee, int $assignedTo): void
    {
        $this->gateway->setTasksAssignedToWhereAssignedToIn($newAssignee, $assignedTo);
    }

    public function publish(int $tasksId): void
    {
        $this->gateway->publishTasks($tasksId);
    }

    public function unpublish(int $tasksId): void
    {
        $this->gateway->unPublishTasks($tasksId);
    }

    public function addToSiteFile(string $ids): void
    {
        $this->gateway->addToSiteFile($ids);
    }

    public function removeFromSiteFile(string $ids): void
    {
        $this->gateway->removeToSiteFile($ids);
    }

    public function delete(string $taskIds): void
    {
        $this->gateway->deleteTasks($taskIds);
    }

    public function deleteByProject(string $projectIds): void
    {
        $this->gateway->deleteTasksByProject($projectIds);
    }

    // =========================================================================
    // Command Methods - Subtasks
    // =========================================================================

    public function createSubtask(
        int $parentTask,
        string $name,
        string $description,
        int $owner,
        int $assignedTo,
        int $status,
        int $priority,
        ?string $startDate,
        ?string $dueDate,
        ?string $completeDate,
        ?float $estimatedTime,
        ?float $actualTime,
        ?string $comments,
        int $published,
        int $completion
    ): void {
        $this->gateway->addSubTask(
            $parentTask,
            $name,
            $description,
            $owner,
            $assignedTo,
            $status,
            $priority,
            $startDate,
            $dueDate,
            $completeDate,
            $estimatedTime,
            $actualTime,
            $comments,
            date('Y-m-d h:i'),
            date('Y-m-d h:i'),
            $published,
            $completion
        );
    }

    public function deleteSubtasksByParent(string $subTaskIds): void
    {
        $this->gateway->deleteSubTasks($subTaskIds);
    }

    public function deleteSubtasksByProject(string $projectIds): void
    {
        $this->gateway->deleteSubtasksByProjectId($projectIds);
    }

    public function deleteSubtasks(string $subtaskIds): void
    {
        $this->gateway->deleteSubTasksById($subtaskIds);
    }
}
