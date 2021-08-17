<?php


namespace phpCollab\Tasks;

use Exception;
use phpCollab\Container;
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
    private $strings;
    private $root;
    private $priority;
    private $status;

    /**
     * @var Container
     */
    private $container;


    /**
     * Tasks constructor.
     * @param Database $database
     * @param Container $container
     */
    public function __construct(Database $database, Container $container)
    {
        $this->db = $database;
        $this->container = $container;
        $this->tasks_gateway = new TasksGateway($this->db);
        $this->strings = $GLOBALS["strings"];
        $this->root = $GLOBALS["root"];
        $this->priority = $GLOBALS["priority"];
        $this->status = $GLOBALS["status"];
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
    public function setTasksCount(int $tasksCount)
    {
        $this->tasksCount = $tasksCount;
    }

    /**
     * @param int $userId
     * @param string|null $sorting
     * @return mixed
     */
    public function getMyTasks(int $userId, string $sorting = null)
    {
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        $userId = filter_var((string)$userId, FILTER_SANITIZE_STRING);

        return $this->tasks_gateway->getMyTasks($userId, $sorting);
    }

    /**
     * @param int $userId
     * @param string|null $subtasks
     * @param int|null $startRow
     * @param int|null $rowsLimit
     * @param string|null $sorting
     * @return mixed
     */
    public function getAllMyTasks(int $userId, string $subtasks = null, int $startRow = null, int $rowsLimit = null, string $sorting = null)
    {
        return $this->tasks_gateway->getAllMyTasks($userId, $subtasks, $startRow, $rowsLimit, $sorting);
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getClientUserTasksCount(int $userId): int
    {
        $data = $this->tasks_gateway->getClientUserTasksIn($userId);
        return count($data);
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getSubtasksAssignedToMe(int $userId)
    {
        $userId = filter_var((string)$userId, FILTER_SANITIZE_STRING);

        return $this->tasks_gateway->getSubtasksAssignedToMe($userId);
    }

    /**
     * @param int $taskId
     * @return mixed
     */
    public function getTaskById(int $taskId)
    {
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        return $this->tasks_gateway->getTaskById($taskId);
    }

    /**
     * @param int $phaseId
     * @return mixed
     */
    public function getOpenPhaseTasks(int $phaseId)
    {
        return $this->tasks_gateway->getOpenPhaseTasks($phaseId);
    }

    /**
     * @param int $assignedTo
     * @return mixed
     */
    public function getTasksAssignedTo(int $assignedTo)
    {
        $assignedTo = filter_var($assignedTo, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksAssignedTo($assignedTo);
    }

    /**
     * @param int $assignedTo
     * @return mixed
     */
    public function getTasksAssignedToMeThatAreNotCompletedOrSuspended(int $assignedTo)
    {
        $assignedTo = filter_var($assignedTo, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksAssignedToMeThatAreNotCompletedOrSuspended($assignedTo);
    }

    /**
     * @param string $taskIds
     * @return mixed
     */
    public function getTasksById(string $taskIds)
    {
        return $this->tasks_gateway->getTasksById($taskIds);
    }

    /**
     * @param int $taskId
     * @param int $status
     * @return mixed
     */
    public function setTaskStatus(int $taskId, int $status)
    {
        return $this->tasks_gateway->setTaskStatus($taskId, $status);
    }

    /**
     * @param string $projectName
     * @return mixed
     */
    public function getTasksByProjectName(string $projectName)
    {
        $projectName = filter_var($projectName, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksByProjectName($projectName);
    }

    /**
     * @param int $projectId
     * @param int|null $startRow
     * @param int|null $rowsLimit
     * @param string|null $sorting
     * @return mixed
     */
    public function getTasksByProjectId(int $projectId, int $startRow = null, int $rowsLimit = null, string $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksByProjectId($projectId, $startRow, $rowsLimit, $sorting);
    }

    /**
     * @param int $projectId
     * @param int $taskOwner
     * @param int|null $startRow
     * @param int|null $rowsLimit
     * @param string|null $sorting
     * @return mixed
     */
    public function getTasksByProjectIdAndOwnerOrPublished(
        int $projectId,
        int $taskOwner,
        int $startRow = null,
        int $rowsLimit = null,
        string $sorting = null
    ) {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksByProjectIdAndOwnerOrPublished($projectId, $taskOwner, $startRow,
            $rowsLimit, $sorting);
    }

    /**
     * @param int $projectId
     * @param int $taskOwner
     * @param int|null $startRow
     * @param int|null $rowsLimit
     * @param string|null $sorting
     * @return mixed
     */
    public function getSubTasksByProjectIdAndOwnerOrPublished(
        int $projectId,
        int $taskOwner,
        int $startRow = null,
        int $rowsLimit = null,
        string $sorting = null
    ) {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getSubTasksByProjectIdAndOwnerOrPublished($projectId, $taskOwner, $startRow,
            $rowsLimit, $sorting);
    }

    /**
     * @param int $projectId
     * @return int
     */
    public function getCountAllTasksForProject(int $projectId): int
    {
        return count($this->getTasksByProjectId($projectId));
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getClientUserTasks(int $userId): int
    {
        $tasks = $this->tasks_gateway->getClientUserTasks($userId);
        return count($tasks);
    }

    /**
     * @param int $projectId
     * @param int|null $startRow
     * @param int|null $rowsLimit
     * @param string|null $sorting
     * @return mixed
     */
    public function getProjectSiteClientTasks(int $projectId, int $startRow = null, int $rowsLimit = null, string $sorting = null)
    {
        return $this->tasks_gateway->getProjectSiteClientTasks($projectId, $startRow, $rowsLimit, $sorting);
    }

    /**
     * @param int $projectId ID of the project
     * @param int $phaseId ID of parent phase
     * @param string|null $sorting column to sort on and direction
     * @return mixed
     */
    public function getTasksByProjectIdAndParentPhase(int $projectId, int $phaseId, string $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $phaseId = filter_var($phaseId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->getTasksByProjectIdAndParentPhase($projectId, $phaseId, $sorting);
    }

    /**
     * @param int $phaseId
     * @param int $projectId
     * @return int
     */
    public function getCountOpenTasksByPhaseAndProject(int $phaseId, int $projectId): int
    {
        return count($this->getTasksByProjectIdAndParentPhase($projectId, $phaseId));
    }

    /**
     * @param int $phaseId
     * @param int $projectId
     * @return int
     */
    public function getCountUncompletedTasks(int $phaseId, int $projectId): int
    {
        $tasks = $this->getTasksByProjectIdAndParentPhase($projectId, $phaseId);
        $tasks = array_filter($tasks, function ($item) {
            return $item["tas_status"] == 2 || $item["tas_status"] == 3 || $item["tas_status"] == 4;
        });
        return count($tasks);
    }

    /**
     * @param int $projectId
     * @return int
     */
    public function getCountCompletedTasks(int $projectId): int
    {
        $tasks = $this->getTasksByProjectId($projectId);
        $tasks = array_filter($tasks, function ($item) {
            return $item["tas_status"] == 0 || $item["tas_status"] == 1;
        });
        return count($tasks);
    }

    /**
     * @param string $date
     * @param int $assignedTo
     * @return mixed
     */
    public function getTasksWhereStartDateAndEndDateLikeNotPublishedAndAssignedToUserId(string $date, int $assignedTo)
    {
        return $this->getTasksWhereStartDateAndEndDateLikeNotPublishedAndAssignedToUserId($date, $assignedTo);
    }

    /**
     * @param int $ownerId
     * @param string|null $sorting
     * @return mixed
     */
    public function getOpenAndCompletedSubTasksAssignedToMe(int $ownerId, string $sorting = null)
    {
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        $ownerId = filter_var((string)$ownerId, FILTER_SANITIZE_STRING);

        return $this->tasks_gateway->getOpenAndCompletedSubTasksAssignedToMe($ownerId, $sorting);
    }

    /**
     * @param int $taskId
     * @return mixed
     */
    public function getSubTaskById(int $taskId)
    {
        $taskId = filter_var($taskId, FILTER_VALIDATE_INT);
        return $this->tasks_gateway->getSubTaskById($taskId);
    }

    /**
     * @param int $subTaskId
     * @return mixed
     */
    public function getSubTaskByIdIn(int $subTaskId)
    {
        return $this->tasks_gateway->getSubTaskByIdIn($subTaskId);
    }

    /**
     * @param int $parentTaskId
     * @param string|null $sorting
     * @return mixed
     */
    public function getSubtasksByParentTaskId(int $parentTaskId, string $sorting = null)
    {
        $parentTaskId = filter_var($parentTaskId, FILTER_VALIDATE_INT);
        return $this->tasks_gateway->getSubtasksByParentTaskId($parentTaskId, $sorting);
    }

    /**
     * @param string $parentTaskIds
     * @param string|null $sorting
     * @return mixed
     */
    public function getSubtasksByParentTaskIdIn(string $parentTaskIds, string $sorting = null)
    {
        return $this->tasks_gateway->getSubtasksByParentTaskIdIn($parentTaskIds, $sorting);
    }

    /**
     * @param string $parentTaskId
     * @param string|null $sorting
     * @return mixed
     */
    public function getPublishedSubtasksByParentTaskId(string $parentTaskId, string $sorting = null)
    {
        $parentTaskId = filter_var($parentTaskId, FILTER_VALIDATE_INT);
        return $this->tasks_gateway->getSubtasksByParentTaskId($parentTaskId, $sorting);
    }

    /**
     * @param int $parentTaskId
     * @return mixed
     */
    public function getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmptyAndNotPublished(int $parentTaskId)
    {
        return $this->tasks_gateway->getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmptyAndNotPublished($parentTaskId);
    }

    /**
     * @param int $parentTaskId
     * @return mixed
     */
    public function getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmpty(int $parentTaskId)
    {
        return $this->tasks_gateway->getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmpty($parentTaskId);
    }

    /**
     * @param string $taskDate
     * @param int $assignedTo
     * @return mixed
     */
    public function getTasksByStartDateEndDateAssignedTo(string $taskDate, int $assignedTo)
    {
        return $this->tasks_gateway->getTasksByStartDateEndDateAssignedTo($taskDate, $assignedTo);
    }

    /**
     * @param int $projectId
     * @return mixed
     */
    public function getTasksByProjectIdWhereStartAndEndAreNotEmpty(int $projectId)
    {
        return $this->tasks_gateway->getTasksByProjectIdWhereStartAndEndAreNotEmpty($projectId);
    }

    /**
     * @param int $projectId
     * @return mixed
     */
    public function getTasksByProjectIdWhereStartAndEndAreNotEmptyAndNotPublished(int $projectId)
    {
        return $this->tasks_gateway->getTasksByProjectIdWhereStartAndEndAreNotEmptyAndNotPublished($projectId);
    }

    /**
     * @param int $projectId
     * @param int $phaseId
     * @return mixed
     */
    public function getTasksByProjectIdAndParentPhaseAndStartEndDateNotBlank(int $projectId, int $phaseId)
    {
        return $this->tasks_gateway->getTasksByProjectIdAndParentPhaseAndStartEndDateNotBlank($projectId, $phaseId);
    }

    /**
     * @param String $sql
     * @return mixed
     */
    public function getReportTasks(string $sql)
    {
        return $this->tasks_gateway->getReportTasks($sql);
    }

    /**
     * @param string $tmpQuery
     * @param string|null $sorting
     * @param int|null $limit
     * @param int|null $rowLimit
     * @return mixed
     */
    public function getSearchTasks(string $tmpQuery, string $sorting = null, int $limit = null, int $rowLimit = null)
    {
        return $this->tasks_gateway->searchResultTasks($tmpQuery, $sorting, $limit, $rowLimit);
    }

    /**
     * @param string $sql
     * @param string|null $sorting
     * @param int|null $limit
     * @param int|null $rowLimit
     * @return mixed
     */
    public function getSearchSubTasks(string $sql, string $sorting = null, int $limit = null, int $rowLimit = null)
    {
        return $this->tasks_gateway->searchResultSubTasks($sql, $sorting, $limit, $rowLimit);
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
     * @param int $id
     * @param string $name
     * @param string|null $description
     * @param int $assignedTo
     * @param int $status
     * @param int $priority
     * @param string|null $startDate
     * @param string|null $dueDate
     * @param float|null $estimatedTime
     * @param float|null $actualTime
     * @param string|null $comments
     * @param int $published
     * @param int $completion
     * @param int $parentPhase
     * @param int $invoicing
     * @param float $workedHours
     * @return mixed
     */
    public function updateTask(
        int $id,
        string $name,
        string $description = null,
        int $assignedTo = 0,
        int $status = 0,
        int $priority = 0,
        string $startDate = null,
        string $dueDate = null,
        float $estimatedTime = null,
        float $actualTime = null,
        string $comments = null,
        int $published = 0,
        int $completion = 0,
        int $parentPhase = 0,
        int $invoicing = 0,
        float $workedHours = 0.00
    ) {
        return $this->tasks_gateway->updateTask($id, $name, $description, $assignedTo, $status, $priority, $startDate,
            $dueDate, $estimatedTime, $actualTime, $comments, $completion, $parentPhase, $published, $invoicing,
            $workedHours, date('Y-m-d h:i') );
    }

    /**
     * @param int $newAssignee
     * @param int $assignedTo
     * @return mixed
     */
    public function setTasksAssignedToWhereAssignedToIn(int $newAssignee, int $assignedTo)
    {
        return $this->tasks_gateway->setTasksAssignedToWhereAssignedToIn($newAssignee, $assignedTo);
    }

    /**
     * @param int $projectId
     * @param int $owner
     * @param string $name
     * @param string|null $description
     * @param int $assignedTo
     * @param int $status
     * @param int $priority
     * @param string|null $startDate
     * @param string|null $dueDate
     * @param float|null $estimatedTime
     * @param float|null $actualTime
     * @param string|null $comments
     * @param int $published
     * @param int $completion
     * @param int $parentPhase
     * @param int $invoicing
     * @param float|null $workedHours
     * @return array
     * @throws Exception
     */
    public function addTask(
        int $projectId,
        int $owner,
        string $name,
        string $description = null,
        int $assignedTo = 0,
        int $status = 0,
        int $priority = 0,
        string $startDate = null,
        string $dueDate = null,
        float $estimatedTime = null,
        float $actualTime = null,
        string $comments = null,
        int $published = 0,
        int $completion = 0,
        int $parentPhase = 0,
        int $invoicing = 0,
        float $workedHours = 0.00
    ): array {
        if ($projectId && $name) {
            // Check to see if assigned_to set, if so then pass over the date.
            $assignedDate = !empty($assignedTo) ? date('Y-m-d h:i') : null;

            $published = $published ?? 0;
            $invoicing = $invoicing ?? 0;
            $completion = $completion ?? 0;
            $workedHours = $workedHours ?? 0.0;

            $newTaskId = $this->tasks_gateway->addTask($projectId, $owner, $name, $description, $assignedTo, $status,
                $priority, $startDate,
                $dueDate, $estimatedTime, $actualTime, $comments, $published, $completion, $parentPhase,
                $invoicing, $workedHours, $assignedDate);

            if ($newTaskId) {
                return $this->tasks_gateway->getTaskById($newTaskId);
            } else {
                throw new Exception('Error adding task');
            }
        } else {
            throw new Exception('Project ID or Task name missing appear to be empty');
        }

    }

    /**
     * @param int $parentTask
     * @param string $name
     * @param string $description
     * @param int $owner
     * @param int $assigned_to
     * @param int $status
     * @param int $priority
     * @param string $start_date
     * @param string $due_date
     * @param string $complete_date
     * @param float $estimated_time
     * @param float $actual_time
     * @param string $comments
     * @param int $published
     * @param int $completion
     * @return mixed
     */
    public function addSubTask(
        int $parentTask,
        string $name,
        string $description,
        int $owner,
        int $assigned_to,
        int $status,
        int $priority,
        string $start_date,
        string $due_date,
        string $complete_date,
        float $estimated_time,
        float $actual_time,
        string $comments,
        int $published,
        int $completion
    ) {

        return $this->tasks_gateway->addSubTask($parentTask, $name, $description, $owner, $assigned_to, $status,
            $priority, $start_date, $due_date, $complete_date, $estimated_time, $actual_time, $comments,
            date('Y-m-d h:i'),
            date('Y-m-d h:i'),
            $published, $completion);
    }

    /**
     * @param int $projectId
     * @param int $taskId
     * @return mixed
     */
    public function setProjectByTaskId(int $projectId, int $taskId)
    {
        return $this->tasks_gateway->setProjectByTaskId($projectId, $taskId);
    }

    /**
     * @param int $taskId
     * @param string $date
     * @return mixed
     */
    public function setCompletionDateForTaskById(int $taskId, string $date)
    {
        return $this->tasks_gateway->setCompletionDateForTaskById($taskId, $date);
    }


    /**
     * @param int $taskId
     * @param string $taskName
     * @return mixed
     */
    public function setName(int $taskId, string $taskName)
    {
        return $this->tasks_gateway->setName($taskId, $taskName);
    }

    /**
     * @param int $taskId
     * @param string $startDate
     * @return mixed
     */
    public function setStartDate(int $taskId, string $startDate)
    {
        return $this->tasks_gateway->setStartDate($taskId, $startDate);
    }

    /**
     * @param int $taskId
     * @param string $dueDate
     * @return mixed
     */
    public function setDueDate(int $taskId, string $dueDate)
    {
        return $this->tasks_gateway->setDueDate($taskId, $dueDate);
    }

    /**
     * @param int $taskId
     * @param int $assignedTo
     * @return mixed
     */
    public function setAssignedTo(int $taskId, int $assignedTo)
    {
        return $this->tasks_gateway->setAssignedTo($taskId, $assignedTo);
    }

    /**
     * @param int $taskId
     * @param string $assignedDate
     * @return mixed
     */
    public function setAssignedDate(int $taskId, string $assignedDate)
    {
        return $this->tasks_gateway->setAssignedDate($taskId, $assignedDate);
    }

    /**
     * @param int $taskId
     * @param int $status
     * @return mixed
     */
    public function setStatus(int $taskId, int $status)
    {
        return $this->tasks_gateway->setStatus($taskId, $status);
    }

    /**
     * @param int $taskId
     * @param int $completion
     * @return mixed
     */
    public function setCompletion(int $taskId, int $completion)
    {
        return $this->tasks_gateway->setCompletion($taskId, $completion);
    }

    /**
     * @param int $taskId
     * @param int $priority
     * @return mixed
     */
    public function setPriority(int $taskId, int $priority)
    {
        return $this->tasks_gateway->setPriority($taskId, $priority);

    }

    /**
     * @param int $taskId
     * @param string $comment
     * @return mixed
     */
    public function setComment(int $taskId, string $comment)
    {
        return $this->tasks_gateway->setComment($taskId, $comment);
    }

    /**
     * @param int $taskId
     * @return mixed
     */
    public function setModifiedDate(int $taskId)
    {
        return $this->tasks_gateway->setModifiedDate($taskId);
    }

    /**
     * @param int $taskId
     * @param int $phase
     * @return mixed
     */
    public function setParentPhase(int $taskId, int $phase)
    {
        return $this->tasks_gateway->setParentPhase($taskId, $phase);
    }

    /**
     * @param string $ids
     * @return mixed
     */
    public function addToSiteFile(string $ids)
    {
        return $this->tasks_gateway->addToSiteFile($ids);
    }

    /**
     * @param string $ids
     * @return mixed
     */
    public function removeToSiteFile(string $ids)
    {
        return $this->tasks_gateway->removeToSiteFile($ids);
    }

    /**
     * @param int $tasksId
     * @return mixed
     */
    public function publishTasks(int $tasksId)
    {
        $tasksId = filter_var($tasksId, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->publishTasks($tasksId);
    }

    /**
     * @param int $tasksId
     * @return mixed
     */
    public function unPublishTasks(int $tasksId)
    {
        $tasksId = filter_var($tasksId, FILTER_SANITIZE_STRING);
        return $this->tasks_gateway->unPublishTasks($tasksId);
    }

    /**
     * @param int $oldOwner
     * @param int $newOwner
     */
    public function reassignTasks(int $oldOwner, int $newOwner)
    {
        $this->tasks_gateway->reassignTasks($oldOwner, $newOwner);
    }

    /**
     * @param string $taskIds
     * @return mixed
     */
    public function deleteTasks(string $taskIds)
    {
        return $this->tasks_gateway->deleteTasks($taskIds);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deleteTasksByProjectId(string $projectIds)
    {
        return $this->tasks_gateway->deleteTasksByProject($projectIds);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deleteSubtasksByProjectId(string $projectIds)
    {
        return $this->tasks_gateway->deleteSubtasksByProjectId($projectIds);
    }

    /**
     * @param string $subTaskIds
     * @return mixed
     */
    public function deleteSubTasks(string $subTaskIds)
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

    /**
     * @param int $taskId
     * @param int $userId
     * @return bool
     */
    public function isOwner(int $taskId, int $userId): bool
    {
        $taskDetail = $this->tasks_gateway->getTaskById($taskId);

        if ($taskDetail) {
            return $taskDetail["tas_owner"] == $userId;
        }

        return false;
    }

    /**
     * @param int $taskId
     * @return mixed
     */
    public function recalculateSubtaskAverages(int $taskId)
    {
        $avg = $this->tasks_gateway->recalculateSubtaskAverage($taskId);
        $avg = $avg["average"];
        settype($avg, "integer");
        return $avg;
    }

    /**
     * @param array $taskDetails
     * @param array $projectDetails
     * @param array $userDetails
     * @param string $subject
     * @param string $bodyOpening
     * @throws Exception
     */
    public function sendTaskNotification(array $taskDetails, array $projectDetails, array $userDetails, string $subject, string $bodyOpening)
    {
        if ($taskDetails && $projectDetails && $userDetails && $subject && $bodyOpening) {
            $mail = $this->container->getNotification();
            try {

                $mail->setFrom($projectDetails["pro_mem_email_work"], $projectDetails["pro_mem_name"]);

                $mail->partSubject = $subject;
                $mail->partMessage = $bodyOpening;

                if ($projectDetails["pro_org_id"] == "1") {
                    $projectDetails["pro_org_name"] = $this->strings["none"];
                }

                $complValue = ($taskDetails["tas_completion"] > 0) ? $taskDetails["tas_completion"] . "0 %" : $taskDetails["tas_completion"] . " %";
                $idStatus = $taskDetails["tas_status"];
                $idPriority = $taskDetails["tas_priority"];

                $body = $mail->partMessage . "\n\n";
                $body .= $this->strings["task"] . " : " . $taskDetails["tas_name"] . "\n";
                $body .= $this->strings["start_date"] . " : " . $taskDetails["tas_start_date"] . "\n";
                $body .= $this->strings["due_date"] . " : " . $taskDetails["tas_due_date"] . "\n";
                $body .= $this->strings["completion"] . " : " . $complValue . "\n";
                $body .= $this->strings["priority"] . " : " . $GLOBALS["priority"][$idPriority] . "\n";
                $body .= $this->strings["status"] . " : " . $GLOBALS["status"][$idStatus] . "\n";
                $body .= $this->strings["description"] . " : " . $taskDetails["tas_description"] . "\n\n";
                $body .= $this->strings["project"] . " : " . $projectDetails["pro_name"] . " (" . $projectDetails["pro_id"] . ")\n";
                $body .= $this->strings["organization"] . " : " . $projectDetails["pro_org_name"] . "\n\n";
                $body .= $this->strings["noti_moreinfo"] . "\n";

                if ($taskDetails["tas_mem_organization"] == "1") {
                    $body .= "$this->root/general/login.php?url=tasks/viewtask.php%3Fid={$taskDetails["tas_id"]}";
                } elseif ($projectDetails["pro_published"] == "0" && $taskDetails["tas_published"] == "0") {
                    $body .= "$this->root/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetails["pro_id"];
                }

                $body .= "\n\n" . $mail->footer;

                $subject = $mail->partSubject . " " . $taskDetails["tas_name"];

                $mail->Subject = $subject;

                if ($taskDetails["tas_priority"] == "4" || $taskDetails["tas_priority"] == "5") {
                    $mail->Priority = "1";
                } else {
                    $mail->Priority = "3";
                }

                $mail->addAddress($userDetails["mem_email_work"], $userDetails["mem_name"]);

                $mail->Body = $body;
                $mail->send();
                $mail->clearAddresses();
            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending mail');
        }
    }

    /**
     * @param array $taskDetails
     * @throws Exception
     */
    public function sendClientAddTaskNotification(array $taskDetails)
    {
        $projects = $this->container->getProjectsLoader();
        $teams = $this->container->getTeams();
        $notifications = $this->container->getNotificationsManager();

        /*
         *  Get the project details, specifically we need:
         *  pro_org_id, pro_org_name, pro_published, pro_name, pro_id
         */
        $projectDetails = $projects->getProjectById($taskDetails["tas_project"]);

        /*
         * Get a list of team members, excluding the current member
         */
        $teamMembers = $teams->getTeamByProjectId($taskDetails["tas_project"]);

        /*
         * We loop through the list of $teamMembers so we can pass it through to get their notification preferences
         */
        $posters = [];
        foreach ($teamMembers as $teamMember) {
            array_push($posters, $teamMember["tea_member"]);
        }

        /*
         * Retrieve a list of notifications for the list of $teamMembers retrieved above
         */
        $listNotifications = $notifications->getNotificationsWhereMemberIn(implode(', ', $posters));

        /*
         * Sanity check to make sure we have all the required data before proceeding.
         */
        if ($taskDetails && $projectDetails && $listNotifications) {
            /*
             * Start creating the mail notification
             */
            $mail = $this->container->getNotification();

            try {
                $mail->setFrom($taskDetails["tas_mem2_email_work"], $taskDetails["tas_mem2_name"]);

                $mail->partSubject = $this->strings["noti_clientaddtask1"];
                $mail->partMessage = $this->strings["noti_clientaddtask2"];

                $complValue = ($taskDetails["tas_completion"] > 0) ? $taskDetails["tas_completion"] . "0 %" : $taskDetails["tas_completion"] . " %";

                $idStatus = $taskDetails["tas_status"];
                $idPriority = $taskDetails["tas_priority"];

                $subject = $mail->partSubject . " " . $taskDetails["tas_name"];

                if ($projectDetails["pro_org_id"] == "1") {
                    $projectDetails["pro_org_name"] = $this->strings["none"];
                }

                /*
                 * Loop through $listNotifications
                 */
                foreach ($listNotifications as $listNotification) {
                    if (
                        ($listNotification["organization"] != "1"
                            && $taskDetails["top_published"] == "0"
                            && $projectDetails["pro_published"] == "0")
                        || $listNotification["organization"] == "1"
                    ) {

                        /*
                         * Make sure the user has an email address, and is flagged
                         * to receive new topic notifications
                         */

                        if (
                            !empty($listNotification["email_work"])
                            && $listNotification["clientAddTask"] == "0"
                        ) {
                            /*
                             * Build up the body of the message
                             */
                            $body = <<<MESSAGE_BODY
$mail->partMessage

{$this->strings["task"]} : {$taskDetails["tas_name"]}
{$this->strings["start_date"]} : {$taskDetails["tas_start_date"]}
{$this->strings["due_date"]} : {$taskDetails["tas_due_date"]}
{$this->strings["completion"]} : $complValue
{$this->strings["priority"]} : {$this->priority[$idPriority]}
{$this->strings["status"]} : {$this->status[$idStatus]}
{$this->strings["description"]} : {$taskDetails["tas_description"]}

{$this->strings["project"]} : {$projectDetails["pro_name"]} ({$projectDetails["pro_id"]})
{$this->strings["organization"]} : {$projectDetails["pro_org_name"]}

{$this->strings["noti_moreinfo"]}
MESSAGE_BODY;

                            if ($listNotification["organization"] == "1") {
                                $body .= "$this->root/general/login.php?url=topics/viewtopic.php%3Fid=" . $taskDetails["tas_id"];
                            }
                            if ($listNotification["organization"] != "1") {
                                $body .= "$this->root/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetails["pro_id"];
                            }

                            $body .= "\n\n" . $mail->footer;

                            $mail->Subject = $subject;
                            $mail->Priority = "3";

                            // To: Address
                            $mail->addAddress($listNotification["email_work"], $listNotification["name"]);
                            $mail->Body = $body;
                            $mail->send();
                            $mail->clearAddresses();
                        }
                    }
                }
            } catch (Exception $e) {
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending email.');
        }
    }

    /**
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getTeamTasks(int $projectId, string $sorting = null)
    {
        return $this->tasks_gateway->getTeamTasks($projectId, $sorting);
    }
}
