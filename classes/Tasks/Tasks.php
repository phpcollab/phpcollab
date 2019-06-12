<?php


namespace phpCollab\Tasks;

use Exception;
use phpCollab\Database;
use phpCollab\Notification;
use phpCollab\Notifications\Notifications;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;

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
    private $projects;
    private $teams;
    private $notifications;
    private $priority;
    private $status;


    /**
     * Tasks constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
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
     * @param $phaseId
     * @return mixed
     */
    public function getOpenPhaseTasks($phaseId)
    {
        return $this->tasks_gateway->getOpenPhaseTasks($phaseId);
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
     * @param $taskId
     * @param $status
     * @return mixed
     */
    public function setTaskStatus($taskId, $status)
    {
        return $this->tasks_gateway->setTaskStatus($taskId, $status);
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
     * @param $taskOwner
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getTasksByProjectIdAndOwnerOrPublished($projectId, $taskOwner, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        $task = $this->tasks_gateway->getTasksByProjectIdAndOwnerOrPublished($projectId, $taskOwner, $startRow, $rowsLimit, $sorting);
        return $task;
    }

    /**
     * @param $projectId
     * @param $taskOwner
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getSubTasksByProjectIdAndOwnerOrPublished($projectId, $taskOwner, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        $task = $this->tasks_gateway->getSubTasksByProjectIdAndOwnerOrPublished($projectId, $taskOwner, $startRow, $rowsLimit, $sorting);
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
     * @param $projectId
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getProjectSiteClientTasks($projectId, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        return $this->tasks_gateway->getProjectSiteClientTasks($projectId, $startRow, $rowsLimit, $sorting);
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
     * @param $projectId
     * @return int
     */
    public function getCountCompletedTasks($projectId)
    {
        $tasks = $this->getTasksByProjectId($projectId);
        $tasks = array_filter($tasks, function ($item) {
            return $item["tas_status"] == 0 || $item["tas_status"] == 1;
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
     * @param $parentTaskIds
     * @param null $sorting
     * @return mixed
     */
    public function getSubtasksByParentTaskIdIn($parentTaskIds, $sorting = null)
    {
        $task = $this->tasks_gateway->getSubtasksByParentTaskIdIn($parentTaskIds, $sorting);
        return $task;
    }

    /**
     * @param $parentTaskId
     * @param null $sorting
     * @return mixed
     */
    public function getPublishedSubtasksByParentTaskId($parentTaskId, $sorting = null)
    {
        $parentTaskId = filter_var($parentTaskId, FILTER_VALIDATE_INT);
        return $this->tasks_gateway->getSubtasksByParentTaskId($parentTaskId, $sorting);
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
     * @param $projectId
     * @param $name
     * @param $description
     * @param $owner
     * @param $assignedTo
     * @param $status
     * @param $priority
     * @param $startDate
     * @param $dueDate
     * @param $estimatedTime
     * @param $actualTime
     * @param $comments
     * @param $published
     * @param $completion
     * @param $parentPhase
     * @param $invoicing
     * @param $workedHours
     * @return array
     * @throws Exception
     */
    public function addTask($projectId, $name, $description, $owner, $assignedTo, $status, $priority, $startDate,
                            $dueDate, $estimatedTime, $actualTime, $comments, $published, $completion, $parentPhase = 0,
                            $invoicing = 0, $workedHours = 0.00)
    {
        if ($projectId && $name ) {
            // Check to see if assigned_to set, if so then pass over the date.
            $assignedDate = !empty($assignedTo) ? date('Y-m-d h:i') : null;

            $published = is_null($published) ? 0 : $published;
            $invoicing = is_null($invoicing) ? 0 : $invoicing;
            $completion = is_null($completion) ? 0 : $completion;

            $newTaskId = $this->tasks_gateway->addTask($projectId, $name, $description, $owner, $assignedTo, $status, $priority, $startDate,
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
     * @param $parentTask
     * @param $name
     * @param $description
     * @param $owner
     * @param $assigned_to
     * @param $status
     * @param $priority
     * @param $start_date
     * @param $due_date
     * @param $complete_date
     * @param $estimated_time
     * @param $actual_time
     * @param $comments
     * @param $published
     * @param $completion
     * @return mixed
     */
    public function addSubTask($parentTask, $name, $description, $owner, $assigned_to, $status, $priority, $start_date,
                               $due_date, $complete_date, $estimated_time, $actual_time, $comments, $published,
                               $completion)
    {

        return $this->tasks_gateway->addSubTask($parentTask, $name, $description, $owner, $assigned_to, $status,
            $priority, $start_date, $due_date, $complete_date, $estimated_time, $actual_time, $comments,
            date('Y-m-d h:i'),
            date('Y-m-d h:i'),
            $published, $completion);
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
     * @param $taskName
     * @return mixed
     */
    public function setName($taskId, $taskName)
    {
        return $this->tasks_gateway->setName($taskId, $taskName);
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
     * @param $taskId
     * @param $phase
     * @return mixed
     */
    public function setParentPhase($taskId, $phase)
    {
        return $this->tasks_gateway->setParentPhase($taskId, $phase);
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

    /**
     * @param $taskId
     * @param $userId
     * @return bool
     */
    public function isOwner($taskId, $userId)
    {
        $taskDetail = $this->tasks_gateway->getTaskById($taskId);

        if ($taskDetail) {
            return $taskDetail["tas_owner"] == $userId;
        }

        return false;
    }

    /**
     * @param $taskDetails
     * @param $projectDetails
     * @param $userDetails
     * @param $subject
     * @param $bodyOpening
     * @throws Exception
     */
    public function sendTaskNotification($taskDetails, $projectDetails, $userDetails, $subject, $bodyOpening)
    {
        if ($taskDetails && $projectDetails && $userDetails && $subject && $bodyOpening) {
            $mail = new Notification(true);
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
                    $body .= "{$this->root}/general/login.php?url=tasks/viewtask.php%3Fid={$taskDetails["tas_id"]}";
                } elseif ($taskDetails["tas_mem_organization"] != "1" && $projectDetails["pro_published"] == "0" && $taskDetails["tas_published"] == "0") {
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
     * @param $taskDetails
     * @throws Exception
     */
    public function sendClientAddTaskNotification($taskDetails)
    {
        $this->projects = new Projects();
        $this->teams = new Teams();
        $this->notifications = new Notifications();

        /*
         *  Get the project details, specifically we need:
         *  pro_org_id, pro_org_name, pro_published, pro_name, pro_id
         */
        $projectDetails = $this->projects->getProjectById($taskDetails["tas_project"]);

        /*
         * Get a list of team members, excluding the current member
         */
        $teamMembers = $this->teams->getTeamByProjectId($taskDetails["tas_project"]);

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
        $listNotifications = $this->notifications->getNotificationsWhereMemberIn(implode(', ', $posters));

        /*
         * Sanity check to make sure we have all the required data before proceeding.
         */
        if ($taskDetails && $projectDetails && $listNotifications) {
            /*
             * Start creating the mail notification
             */
            $mail = new Notification(true);

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
                if ($listNotifications) {
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
{$mail->partMessage}

{$this->strings["task"]} : {$taskDetails["tas_name"]}
{$this->strings["start_date"]} : {$taskDetails["tas_start_date"]}
{$this->strings["due_date"]} : {$taskDetails["tas_due_date"]}
{$this->strings["completion"]} : {$complValue}
{$this->strings["priority"]} : {$this->priority[$idPriority]}
{$this->strings["status"]} : {$this->status[$idStatus]}
{$this->strings["description"]} : {$taskDetails["tas_description"]}

{$this->strings["project"]} : {$projectDetails["pro_name"]} ({$projectDetails["pro_id"]})
{$this->strings["organization"]} : {$projectDetails["pro_org_name"]}

{$this->strings["noti_moreinfo"]}
MESSAGE_BODY;
                                
                                if ($listNotification["organization"] == "1") {
                                    $body .= "{$this->root}/general/login.php?url=topics/viewtopic.php%3Fid=" . $taskDetails["tas_id"];
                                } elseif ($listNotification["organization"] != "1") {
                                    $body .= "{$this->root}/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetails["pro_id"];
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
                }
            } catch (Exception $e) {
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending email.');
        }
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getTeamTasks($projectId, $sorting = null)
    {
        return $this->tasks_gateway->getTeamTasks($projectId, $sorting);
    }
}
