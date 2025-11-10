<?php

namespace phpCollab\Tasks;

/**
 * Interface TasksRepositoryInterface
 *
 * Repository Pattern interface for Tasks data access.
 * Provides an abstraction layer between business logic (Tasks service) and data access (TasksGateway).
 *
 * This interface defines the contract for all task and subtask data operations.
 * All methods use domain-friendly names (findById, create, update, delete).
 *
 * @package phpCollab\Tasks
 */
interface TasksRepositoryInterface
{
    // =========================================================================
    // Query Methods - Tasks
    // =========================================================================

    /**
     * Find task by ID
     *
     * @param int $taskId Task ID
     * @return array|null Task data or null if not found
     */
    public function findById(int $taskId): ?array;

    /**
     * Find tasks by multiple IDs
     *
     * @param string $taskIds Comma-separated task IDs
     * @return array Array of tasks
     */
    public function findByIds(string $taskIds): array;

    /**
     * Find my tasks assigned to user
     *
     * @param int $userId User ID
     * @param string|null $sorting Sorting clause
     * @return array Array of tasks
     */
    public function findMyTasks(int $userId, ?string $sorting = null): array;

    /**
     * Find all my tasks (active and subtasks)
     *
     * @param int $userId User ID
     * @param string|null $subtasks Comma-separated subtask IDs
     * @param int|null $startRow Start row for pagination
     * @param int|null $rowsLimit Number of rows to return
     * @param string|null $sorting Sorting clause
     * @return array Array of tasks
     */
    public function findAllMyTasks(int $userId, ?string $subtasks = null, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array;

    /**
     * Find tasks assigned to specific user(s)
     *
     * @param int $assignedTo User ID(s) - can be comma-separated
     * @return array Array of tasks
     */
    public function findTasksAssignedTo(int $assignedTo): array;

    /**
     * Find active tasks assigned to user (not completed or suspended)
     *
     * @param int $assignedTo User ID
     * @return array Array of tasks
     */
    public function findActiveTasksAssignedTo(int $assignedTo): array;

    /**
     * Find tasks by project ID
     *
     * @param int $projectId Project ID
     * @param int|null $startRow Start row for pagination
     * @param int|null $rowsLimit Number of rows to return
     * @param string|null $sorting Sorting clause
     * @return array Array of tasks
     */
    public function findByProjectId(int $projectId, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array;

    /**
     * Find tasks by project name
     *
     * @param string $projectName Project name
     * @return array Array of tasks
     */
    public function findByProjectName(string $projectName): array;

    /**
     * Find tasks by project and owner or published status
     *
     * @param int $projectId Project ID
     * @param int $ownerId Owner ID
     * @param int|null $startRow Start row for pagination
     * @param int|null $rowsLimit Number of rows to return
     * @param string|null $sorting Sorting clause
     * @return array Array of tasks
     */
    public function findByProjectAndOwnerOrPublished(int $projectId, int $ownerId, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array;

    /**
     * Find tasks by project and phase
     *
     * @param int $projectId Project ID
     * @param int $phaseId Phase ID
     * @param string|null $sorting Sorting clause
     * @return array Array of tasks
     */
    public function findByProjectAndPhase(int $projectId, int $phaseId, ?string $sorting = null): array;

    /**
     * Find open phase tasks
     *
     * @param int $phaseId Phase ID
     * @return array Array of tasks
     */
    public function findOpenPhaseTasks(int $phaseId): array;

    /**
     * Find client user tasks
     *
     * @param int $userId User ID
     * @return array Array of tasks
     */
    public function findClientUserTasks(int $userId): array;

    /**
     * Find client user tasks (IN clause version)
     *
     * @param int $userId User ID(s) - can be comma-separated
     * @return array Array of task IDs
     */
    public function findClientUserTasksIn(int $userId): array;

    /**
     * Find project site client tasks
     *
     * @param int $projectId Project ID
     * @param int|null $startRow Start row for pagination
     * @param int|null $rowsLimit Number of rows to return
     * @param string|null $sorting Sorting clause
     * @return array Array of tasks
     */
    public function findProjectSiteClientTasks(int $projectId, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array;

    /**
     * Find team tasks by project
     *
     * @param int $projectId Project ID
     * @param string|null $sorting Sorting clause
     * @return array Array of tasks
     */
    public function findTeamTasks(int $projectId, ?string $sorting = null): array;

    /**
     * Find tasks by start/end date and assigned to user
     *
     * @param string $taskDate Date to search
     * @param int $assignedTo User ID
     * @return array Array of tasks
     */
    public function findByDateAndAssignedTo(string $taskDate, int $assignedTo): array;

    /**
     * Find tasks where start and end dates are not empty
     *
     * @param int $projectId Project ID
     * @return array Array of tasks
     */
    public function findByProjectWithDates(int $projectId): array;

    /**
     * Find tasks where start and end dates are not empty and not published
     *
     * @param int $projectId Project ID
     * @return array Array of tasks
     */
    public function findByProjectWithDatesNotPublished(int $projectId): array;

    /**
     * Find tasks by project, phase, and date range
     *
     * @param int $projectId Project ID
     * @param int $phaseId Phase ID
     * @return array Array of tasks
     */
    public function findByProjectPhaseWithDates(int $projectId, int $phaseId): array;

    /**
     * Search tasks with custom query
     *
     * @param string $query Search query
     * @param string|null $sorting Sorting clause
     * @param int|null $limit Offset for pagination
     * @param int|null $rowLimit Number of rows
     * @return array Array of tasks
     */
    public function search(string $query, ?string $sorting = null, ?int $limit = null, ?int $rowLimit = null): array;

    /**
     * Find tasks for reports (custom SQL)
     *
     * @param string $sql Custom SQL WHERE clause
     * @return array Array of tasks
     */
    public function findForReport(string $sql): array;

    // =========================================================================
    // Query Methods - Subtasks
    // =========================================================================

    /**
     * Find subtask by ID
     *
     * @param int $subtaskId Subtask ID
     * @return array|null Subtask data or null if not found
     */
    public function findSubtaskById(int $subtaskId): ?array;

    /**
     * Find subtasks by IDs
     *
     * @param int $subtaskId Subtask ID(s) - can be comma-separated
     * @return array Array of subtasks
     */
    public function findSubtaskByIds(int $subtaskId): array;

    /**
     * Find subtasks assigned to user
     *
     * @param int $userId User ID
     * @return array Array of subtasks
     */
    public function findSubtasksAssignedTo(int $userId): array;

    /**
     * Find open and completed subtasks assigned to user
     *
     * @param int $ownerId Owner ID
     * @param string|null $sorting Sorting clause
     * @return array Array of subtasks
     */
    public function findActiveSubtasksAssignedTo(int $ownerId, ?string $sorting = null): array;

    /**
     * Find subtasks by parent task ID
     *
     * @param int $parentTaskId Parent task ID
     * @param string|null $sorting Sorting clause
     * @return array Array of subtasks
     */
    public function findSubtasksByParent(int $parentTaskId, ?string $sorting = null): array;

    /**
     * Find subtasks by multiple parent task IDs
     *
     * @param string $parentTaskIds Comma-separated parent task IDs
     * @param string|null $sorting Sorting clause
     * @return array Array of subtasks
     */
    public function findSubtasksByParents(string $parentTaskIds, ?string $sorting = null): array;

    /**
     * Find published subtasks by parent task ID
     *
     * @param string $parentTaskId Parent task ID
     * @param string|null $sorting Sorting clause
     * @return array Array of subtasks
     */
    public function findPublishedSubtasksByParent(string $parentTaskId, ?string $sorting = null): array;

    /**
     * Find subtasks with dates set (not published)
     *
     * @param int $parentTaskId Parent task ID
     * @return array Array of subtasks
     */
    public function findSubtasksByParentWithDatesNotPublished(int $parentTaskId): array;

    /**
     * Find subtasks with dates set
     *
     * @param int $parentTaskId Parent task ID
     * @return array Array of subtasks
     */
    public function findSubtasksByParentWithDates(int $parentTaskId): array;

    /**
     * Find subtasks by project and owner or published
     *
     * @param int $projectId Project ID
     * @param int $ownerId Owner ID
     * @param int|null $startRow Start row for pagination
     * @param int|null $rowsLimit Number of rows to return
     * @param string|null $sorting Sorting clause
     * @return array Array of subtasks
     */
    public function findSubtasksByProjectAndOwnerOrPublished(int $projectId, int $ownerId, ?int $startRow = null, ?int $rowsLimit = null, ?string $sorting = null): array;

    /**
     * Search subtasks with custom query
     *
     * @param string $query Search query
     * @param string|null $sorting Sorting clause
     * @param int|null $limit Offset for pagination
     * @param int|null $rowLimit Number of rows
     * @return array Array of subtasks
     */
    public function searchSubtasks(string $query, ?string $sorting = null, ?int $limit = null, ?int $rowLimit = null): array;

    /**
     * Calculate subtask completion average
     *
     * @param int $taskId Parent task ID
     * @return mixed Average completion percentage
     */
    public function calculateSubtaskAverage(int $taskId);

    // =========================================================================
    // Command Methods - Tasks
    // =========================================================================

    /**
     * Create a new task
     *
     * @param int $projectId Project ID
     * @param int $owner Owner ID
     * @param string $name Task name
     * @param string|null $description Task description
     * @param int $assignedTo Assigned user ID
     * @param int $status Status
     * @param int $priority Priority
     * @param string|null $startDate Start date
     * @param string|null $dueDate Due date
     * @param float|null $estimatedTime Estimated time
     * @param float|null $actualTime Actual time
     * @param string|null $comments Comments
     * @param int $published Published flag
     * @param int $completion Completion percentage
     * @param int $parentPhase Parent phase ID
     * @param int $invoicing Invoicing flag
     * @param float $workedHours Worked hours
     * @param string|null $assignedDate Assignment date
     * @return string Created task ID
     */
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
    ): string;

    /**
     * Update an existing task
     *
     * @param int $id Task ID
     * @param string $name Task name
     * @param string|null $description Task description
     * @param int $assignedTo Assigned user ID
     * @param int $status Status
     * @param int $priority Priority
     * @param string|null $startDate Start date
     * @param string|null $dueDate Due date
     * @param float|null $estimatedTime Estimated time
     * @param float|null $actualTime Actual time
     * @param string|null $comments Comments
     * @param int $published Published flag
     * @param int $completion Completion percentage
     * @param int $parentPhase Parent phase ID
     * @param int $invoicing Invoicing flag
     * @param float $workedHours Worked hours
     * @param string|null $modifiedDate Modified date
     * @return void
     */
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
    ): void;

    /**
     * Update task status
     *
     * @param int $taskId Task ID
     * @param int $status Status
     * @return void
     */
    public function updateStatus(int $taskId, int $status): void;

    /**
     * Update task name
     *
     * @param int $taskId Task ID
     * @param string $taskName Task name
     * @return void
     */
    public function updateName(int $taskId, string $taskName): void;

    /**
     * Update task start date
     *
     * @param int $taskId Task ID
     * @param string $startDate Start date
     * @return void
     */
    public function updateStartDate(int $taskId, string $startDate): void;

    /**
     * Update task due date
     *
     * @param int $taskId Task ID
     * @param string $dueDate Due date
     * @return void
     */
    public function updateDueDate(int $taskId, string $dueDate): void;

    /**
     * Update task assigned to user
     *
     * @param int $taskId Task ID
     * @param int $assignedTo User ID
     * @return void
     */
    public function updateAssignedTo(int $taskId, int $assignedTo): void;

    /**
     * Update task assigned date
     *
     * @param int $taskId Task ID
     * @param string $assignedDate Assignment date
     * @return void
     */
    public function updateAssignedDate(int $taskId, string $assignedDate): void;

    /**
     * Update task completion percentage
     *
     * @param int $taskId Task ID
     * @param int $completion Completion percentage
     * @return void
     */
    public function updateCompletion(int $taskId, int $completion): void;

    /**
     * Update task priority
     *
     * @param int $taskId Task ID
     * @param int $priority Priority
     * @return void
     */
    public function updatePriority(int $taskId, int $priority): void;

    /**
     * Update task comment
     *
     * @param int $taskId Task ID
     * @param string $comment Comment
     * @return void
     */
    public function updateComment(int $taskId, string $comment): void;

    /**
     * Update task modified date to current time
     *
     * @param int $taskId Task ID
     * @return void
     */
    public function updateModifiedDate(int $taskId): void;

    /**
     * Update task parent phase
     *
     * @param int $taskId Task ID
     * @param int $phase Phase ID
     * @return void
     */
    public function updateParentPhase(int $taskId, int $phase): void;

    /**
     * Update task project
     *
     * @param int $projectId Project ID
     * @param int $taskId Task ID
     * @return void
     */
    public function updateProject(int $projectId, int $taskId): void;

    /**
     * Update task completion date
     *
     * @param int $taskId Task ID
     * @param string $date Completion date
     * @return void
     */
    public function updateCompletionDate(int $taskId, string $date): void;

    /**
     * Reassign tasks from one owner to another
     *
     * @param int $oldOwner Old owner ID
     * @param int $newOwner New owner ID
     * @return void
     */
    public function reassign(int $oldOwner, int $newOwner): void;

    /**
     * Reassign multiple tasks to new assignee
     *
     * @param int $newAssignee New assignee ID
     * @param int $assignedTo Current assignee ID(s)
     * @return void
     */
    public function reassignMultiple(int $newAssignee, int $assignedTo): void;

    /**
     * Publish tasks
     *
     * @param int $tasksId Task ID(s)
     * @return void
     */
    public function publish(int $tasksId): void;

    /**
     * Unpublish tasks
     *
     * @param int $tasksId Task ID(s)
     * @return void
     */
    public function unpublish(int $tasksId): void;

    /**
     * Add tasks to site file
     *
     * @param string $ids Task ID(s)
     * @return void
     */
    public function addToSiteFile(string $ids): void;

    /**
     * Remove tasks from site file
     *
     * @param string $ids Task ID(s)
     * @return void
     */
    public function removeFromSiteFile(string $ids): void;

    /**
     * Delete tasks by IDs
     *
     * @param string $taskIds Comma-separated task IDs
     * @return void
     */
    public function delete(string $taskIds): void;

    /**
     * Delete tasks by project ID
     *
     * @param string $projectIds Comma-separated project IDs
     * @return void
     */
    public function deleteByProject(string $projectIds): void;

    // =========================================================================
    // Command Methods - Subtasks
    // =========================================================================

    /**
     * Create a new subtask
     *
     * @param int $parentTask Parent task ID
     * @param string $name Subtask name
     * @param string $description Subtask description
     * @param int $owner Owner ID
     * @param int $assignedTo Assigned user ID
     * @param int $status Status
     * @param int $priority Priority
     * @param string|null $startDate Start date
     * @param string|null $dueDate Due date
     * @param string|null $completeDate Complete date
     * @param float|null $estimatedTime Estimated time
     * @param float|null $actualTime Actual time
     * @param string|null $comments Comments
     * @param int $published Published flag
     * @param int $completion Completion percentage
     * @return void
     */
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
    ): void;

    /**
     * Delete subtasks by parent task IDs
     *
     * @param string $subTaskIds Comma-separated task IDs
     * @return void
     */
    public function deleteSubtasksByParent(string $subTaskIds): void;

    /**
     * Delete subtasks by project ID
     *
     * @param string $projectIds Comma-separated project IDs
     * @return void
     */
    public function deleteSubtasksByProject(string $projectIds): void;

    /**
     * Delete subtasks by subtask IDs
     *
     * @param string $subtaskIds Comma-separated subtask IDs
     * @return void
     */
    public function deleteSubtasks(string $subtaskIds): void;
}
