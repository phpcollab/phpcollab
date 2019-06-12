<?php

namespace phpCollab\Tasks;

use phpCollab\Database;

/**
 * Class TasksGateway
 * @package phpCollab\Tasks
 */
class TasksGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * TasksGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $assignedToId
     * @param null $sorting
     * @return mixed
     */
    public function getMyTasks($assignedToId, $sorting = null)
    {
        $whereStatement = " WHERE tas.assigned_to = :assigned_to";

        $this->db->query($this->initrequest["tasks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':assigned_to', $assignedToId);

        return $this->db->resultset();
    }

    /**
     * @param $taskId
     * @param $assigned_to
     * @return mixed
     */
    public function setAssignedTo($taskId, $assigned_to)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET assigned_to = :assigned_to WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':assigned_to', $assigned_to);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $assignedDate
     * @return mixed
     */
    public function setAssignedDate($taskId, $assignedDate)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET assigned = :assigned_date WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':assigned_date', $assignedDate);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $status
     * @return mixed
     */
    public function setStatus($taskId, $status)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET status = :status WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':status', $status);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();

    }

    /**
     * @param $taskId
     * @param $completion
     * @return mixed
     */
    public function setCompletion($taskId, $completion)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET completion = :completion WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':completion', $completion);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $priority
     * @return mixed
     */
    public function setPriority($taskId, $priority)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET priority = :priority WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':priority', $priority);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $taskName
     * @return mixed
     */
    public function setName($taskId, $taskName)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET name = :task_name WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':task_name', $taskName);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $startDate
     * @return mixed
     */
    public function setStartDate($taskId, $startDate)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET start_date = :start_date WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $dueDate
     * @return mixed
     */
    public function setDueDate($taskId, $dueDate)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET due_date = :due_date WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':due_date', $dueDate);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $comment
     * @return mixed
     */
    public function setComment($taskId, $comment)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET comment = :comment WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':comment', $comment);
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public function setModifiedDate($taskId)
    {
        $tmpquery = "UPDATE {$this->tableCollab["tasks"]} SET modified = :modified_date WHERE id = :task_id";
        $this->db->query($tmpquery);
        $this->db->bind(':modified_date', date('Y-m-d h:i'));
        $this->db->bind(':task_id', $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $phase
     * @return mixed
     */
    public function setParentPhase($taskId, $phase)
    {
        $sql = "UPDATE {$this->tableCollab["tasks"]} SET parent_phase = :phase_id WHERE id = :task_id";
        $this->db->query($sql);
        $this->db->bind(":task_id", $taskId);
        $this->db->bind(":phase_id", $phase);
        return $this->db->execute();
    }


    /**
     * @param $userId
     * @param $subtasks
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getAllMyTasks($userId, $subtasks = null, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        if (!empty($subtasks)) {
            $subtaskIds = explode(',', $subtasks);
            $placeholders = str_repeat('?, ', count($subtaskIds) - 1) . '?';

            $tmpquery = " WHERE (tas.assigned_to = ? AND tas.status IN(0,2,3) AND pro.status IN(0,2,3)) OR tas.id IN({$placeholders})";
        } else {
            $tmpquery = " WHERE tas.assigned_to = ? AND tas.status IN(0,2,3) AND pro.status IN(0,2,3)";
        }

        $tmpquery = $tmpquery . $this->orderBy($sorting) . $this->limit($startRow, $rowsLimit);

        if (!empty($subtasks) && !empty($subtaskIds)) {
            if (is_array($subtaskIds)) {
                array_unshift($subtaskIds, $userId);
            } else {
                $subtaskIds = explode(',', $userId . ',' . $subtasks);
            }
        }

        $this->db->query($this->initrequest["tasks"] . $tmpquery);

        if (!empty($subtasks) && !empty($subtaskIds)) {
            $this->db->execute($subtaskIds);
        }

        if (empty($subtasks)) {
            $this->db->execute([$userId]);
        }
        return $this->db->resultset();
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getClientUserTasksIn($userId)
    {
        $userIds = explode(',', $userId);
        $placeholders = str_repeat('?, ', count($userIds) - 1) . '?';
        $sql = "SELECT tas.id FROM {$this->tableCollab["tasks"]} tas WHERE tas.assigned_to IN({$placeholders})";

        $this->db->query($sql);
        $this->db->execute($userIds);
        return $this->db->resultset();
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
     * @param $created
     * @param $assigned
     * @param $published
     * @param $completion
     * @return mixed
     */
    public function addSubTask($parentTask, $name, $description, $owner, $assigned_to, $status, $priority, $start_date,
                               $due_date, $complete_date, $estimated_time, $actual_time, $comments, $created, $assigned,
                               $published, $completion)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["subtasks"]} (
task, name, description, owner, assigned_to, status, priority, start_date, due_date, complete_date,
estimated_time, actual_time, comments, created, assigned, published, completion
) VALUES (
:parent_task, :name, :description, :owner, :assigned_to, :status, :priority, :start_date, :due_date, :complete_date, 
:estimated_time, :actual_time, :comments, :created, :assigned, :published, :completion
)
SQL;

        $this->db->query($sql);
        $this->db->bind(':task', $parentTask);
        $this->db->bind(':name', $name);
        $this->db->bind(':description', $description);
        $this->db->bind(':owner', $owner);
        $this->db->bind(':assigned_to', $assigned_to);
        $this->db->bind(':status', $status);
        $this->db->bind(':priority', $priority);
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':due_date', $due_date);
        $this->db->bind(':complete_date', $complete_date);
        $this->db->bind(':estimated_time', $estimated_time);
        $this->db->bind(':actual_time', $actual_time);
        $this->db->bind(':comments', $comments);
        $this->db->bind(':created', $created);
        $this->db->bind(':assigned', $assigned);
        $this->db->bind(':published', $published);
        $this->db->bind(':completion', $completion);

        return $this->db->execute();

    }

    /**
     * @param $userId
     * @param null $sorting
     * @return mixed
     */
    public function getSubtasksAssignedToMe($userId, $sorting = null)
    {
        $whereStatement = ' WHERE subtas.assigned_to = :user_id';

        $this->db->query($this->initrequest["subtasks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':user_id', $userId);

        return $this->db->resultset();
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public function getTaskById($taskId)
    {
        $whereStatement = " WHERE tas.id = :task_id";

        $this->db->query($this->initrequest["tasks"] . $whereStatement);

        $this->db->bind(':task_id', $taskId);

        return $this->db->single();
    }

    /**
     * @param $taskIds
     * @return mixed
     */
    public function getTasksById($taskIds)
    {
        $taskIds = explode(',', $taskIds);
        $placeholders = str_repeat('?, ', count($taskIds) - 1) . '?';
        $whereStatement = " WHERE tas.id IN($placeholders)";
        $this->db->query($this->initrequest["tasks"] . $whereStatement);
        $this->db->execute($taskIds);
        return $this->db->resultset();
    }

    /**
     * @param $assignedToIds
     * @return mixed
     */
    public function getTasksAssignedTo($assignedToIds)
    {
        $assignedToIds = explode(',', $assignedToIds);
        $placeholders = str_repeat('?, ', count($assignedToIds) - 1) . '?';
        $whereStatement = " WHERE tas.assigned_to IN($placeholders)";
        $this->db->query($this->initrequest["tasks"] . $whereStatement);
        $this->db->execute($assignedToIds);
        return $this->db->fetchAll();
    }

    /**
     * @param $assignedTo
     * @return mixed
     */
    public function getTasksAssignedToMeThatAreNotCompletedOrSuspended($assignedTo)
    {
        $sql = "SELECT tas.id FROM {$this->tableCollab["tasks"]} tas LEFT OUTER JOIN {$this->tableCollab["projects"]} pro ON pro.id = tas.project WHERE tas.assigned_to = :assigned_to AND tas.status IN(0,2,3) AND pro.status IN(0,2,3)";
        $this->db->query($sql);
        $this->db->bind(':assigned_to', $assignedTo);
        return $this->db->resultset();
    }

    /**
     * @param $projectName
     * @return mixed
     */
    public function getTasksByProjectName($projectName)
    {
        $whereStatement = " WHERE tas.project = :project_name";

        $this->db->query($this->initrequest["tasks"] . $whereStatement);

        $this->db->bind(':project_name', $projectName);

        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param null $startRow
     * @param null $rowsLimit
     * @param $sorting
     * @return mixed
     */
    public function getTasksByProjectId($projectId, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        $whereStatement = " WHERE tas.project = :project_id";

        $query = $this->initrequest["tasks"] . $whereStatement . $this->orderBy($sorting) . $this->limit($startRow, $rowsLimit);

        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param $ownerId
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getTasksByProjectIdAndOwnerOrPublished($projectId, $ownerId, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        $whereStatement = " WHERE (tas.project = :project_id) AND (tas.owner = :task_owner OR tas.published = '0') ORDER BY tas.name";

        $query = $this->initrequest["tasks"] . $whereStatement . $this->orderBy($sorting) . $this->limit($startRow, $rowsLimit);

        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':task_owner', $ownerId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param $ownerId
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getSubTasksByProjectIdAndOwnerOrPublished($projectId, $ownerId, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        $whereStatement = " WHERE subtas.task = tas.id AND tas.project = :project_id AND (tas.owner = :task_owner OR tas.published = '0') ORDER BY subtas.name";

        $query = $this->initrequest["subtasks"] . $whereStatement . $this->orderBy($sorting) . $this->limit($startRow, $rowsLimit);

        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':task_owner', $ownerId);
        return $this->db->resultset();
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getClientUserTasks($userId)
    {
        $tmpquery = "SELECT tas.id FROM {$this->tableCollab["tasks"]} tas LEFT OUTER JOIN {$this->tableCollab["projects"]} pro ON pro.id = tas.project WHERE tas.assigned_to = :user_id AND tas.status IN(0,2,3) AND pro.status IN(0,2,3)";
        $this->db->query($tmpquery);
        $this->db->bind(":user_id", $userId);
        return $this->db->resultset();
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
        $whereStatement = " WHERE tas.project = :project_id AND tas.assigned_to != 0 AND tas.published = 0 AND mem.profil = 3";

        $query = $this->initrequest["tasks"] . $whereStatement . $this->orderBy($sorting) . $this->limit($startRow, $rowsLimit);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $phaseId
     * @return mixed
     */
    public function getOpenPhaseTasks($phaseId)
    {
        $query = " WHERE tas.parent_phase = :phase_id AND tas.status = '3'";
        $this->db->query($this->initrequest["tasks"] . $query);
        $this->db->bind(":phase_id", $phaseId);
        return $this->db->resultset();
    }

    /**
     * @param $taskId
     * @param $status
     * @return mixed
     */
    public function setTaskStatus($taskId, $status)
    {
        $query = "UPDATE {$this->tableCollab["tasks"]} SET status = :status WHERE id = :task_id";
        $this->db->query($query);
        $this->db->bind(":task_id", $taskId);
        $this->db->bind(":status", $status);
        return $this->db->execute();
    }

    /**
     * @param $projectId
     * @param $phaseId
     * @param null $sorting
     * @return mixed
     */
    public function getTasksByProjectIdAndParentPhase($projectId, $phaseId, $sorting = null)
    {
        $whereStatement = " WHERE tas.project = :project_id AND tas.parent_phase = :parent_phase";
        $this->db->query($this->initrequest["tasks"] . $whereStatement . $this->orderBy($sorting));
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':parent_phase', $phaseId);
        return $this->db->resultset();
    }

    /**
     * @param $date
     * @param $assignedTo
     * @return mixed
     */
    public function getTasksWhereStartDateAndEndDateLikeNotPublishedAndAssignedToUserId($date, $assignedTo)
    {
        $whereStatement = " WHERE (tas.start_date LIKE :date OR tas.due_date LIKE :date) AND tas.published = 0 AND tas.assigned_to = :assigned_to";
        $this->db->query($this->initrequest["tasks"] . $whereStatement . $this->orderBy('tas.due_date'));
        $this->db->bind(':date', $date . '%');
        $this->db->bind(':assigned_to', $assignedTo);
        return $this->db->resultset();

    }

    /**
     * @param $subtaskId
     * @return mixed
     */
    public function getSubTaskById($subtaskId)
    {
        $whereStatement = " WHERE subtas.id = :sub_task_id";

        $this->db->query($this->initrequest["subtasks"] . $whereStatement);

        $this->db->bind(':sub_task_id', $subtaskId);

        return $this->db->single();
    }

    /**
     * @param $subtaskId
     * @return mixed
     */
    public function getSubTaskByIdIn($subtaskId)
    {
        $subtaskId = explode(',', $subtaskId);
        $placeholders = str_repeat('?, ', count($subtaskId) - 1) . '?';
        $whereStatement = " WHERE subtas.id IN($placeholders)";
        $this->db->query($this->initrequest["subtasks"] . $whereStatement . $this->orderBy('subtas.name'));
        $this->db->execute($subtaskId);
        return $this->db->fetchAll();

    }


    /**
     * @param $parentTaskId
     * @param null $sorting
     * @return mixed
     */
    public function getSubtasksByParentTaskId($parentTaskId, $sorting = null)
    {
        $whereStatement = " WHERE subtas.task = :parent_task_id";
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        $this->db->query($this->initrequest["subtasks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':parent_task_id', $parentTaskId);

        return $this->db->resultset();
    }

    /**
     * @param $parentTaskId
     * @param null $sorting
     * @return mixed
     */
    public function getPublishedSubtasksByParentTaskId($parentTaskId, $sorting = null)
    {
        $whereStatement = " WHERE subtas.task = :parent_task_id AND subtas.published = 0";
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        $this->db->query($this->initrequest["subtasks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':parent_task_id', $parentTaskId);

        return $this->db->resultset();
    }

    /**
     * @param String $parentTaskIds
     * @param null $sorting
     * @return mixed
     */
    public function getSubtasksByParentTaskIdIn($parentTaskIds, $sorting = null)
    {
        $parentTaskIds = explode(',', $parentTaskIds);
        $placeholders = str_repeat('?, ', count($parentTaskIds) - 1) . '?';

        $sql = $this->initrequest["subtasks"] . " WHERE task IN ($placeholders)";

        $this->db->query($sql . $this->orderBy($sorting));
        $this->db->execute($parentTaskIds);
        return $this->db->fetchAll();
    }

    /**
     * @param $parentTaskId
     * @return mixed
     */
    public function getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmptyAndNotPublished($parentTaskId)
    {
        $whereStatement = " WHERE subtas.task = :parent_task_id AND subtas.start_date != '--' AND subtas.due_date != '--' AND tas.published != 1";
        $this->db->query($this->initrequest["subtasks"] . $whereStatement . $this->orderBy('subtas.due_date'));
        $this->db->bind(':parent_task_id', $parentTaskId);
        return $this->db->resultset();

    }

    /**
     * @param $parentTaskId
     * @return mixed
     */
    public function getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmpty($parentTaskId)
    {
        $whereStatement = " WHERE subtas.task = :parent_task_id AND subtas.start_date != '--' AND subtas.due_date != '--'";
        $this->db->query($this->initrequest["subtasks"] . $whereStatement . $this->orderBy('subtas.due_date'));
        $this->db->bind(':parent_task_id', $parentTaskId);
        return $this->db->resultset();
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getOpenAndCompletedSubTasksAssignedToMe($ownerId, $sorting = null)
    {
        $tmpquery = " WHERE subtas.assigned_to = :owner_id AND subtas.status IN(0,2,3) AND tas.status IN(0,2,3)";
        $this->db->query($this->initrequest["subtasks"] . $tmpquery . $this->orderBy($sorting));
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultset();
    }


    /**
     * @param $taskDate
     * @param $assignedTo
     * @return mixed
     */
    public function getTasksByStartDateEndDateAssignedTo($taskDate, $assignedTo)
    {
        $tmpquery = " WHERE (tas.start_date LIKE :task_date OR tas.due_date LIKE :task_date) AND tas.assigned_to = :assigned_to";
        $this->db->query($this->initrequest["tasks"] . $tmpquery . $this->orderBy('tas.due_date'));
        $this->db->bind(':task_date', $taskDate . '%');
        $this->db->bind(':assigned_to', $assignedTo);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getTasksByProjectIdWhereStartAndEndAreNotEmpty($projectId)
    {
        $tmpquery = " WHERE tas.project = :project_id AND tas.start_date != '--' AND tas.due_date != '--' AND tas.start_date != '' AND tas.due_date != ''";
        $this->db->query($this->initrequest["tasks"] . $tmpquery . $this->orderBy('tas.due_date'));
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getTasksByProjectIdWhereStartAndEndAreNotEmptyAndNotPublished($projectId)
    {
        $tmpquery = " WHERE tas.project = :project_id AND tas.start_date != '--' AND tas.due_date != '--' AND tas.published != 1";
        $this->db->query($this->initrequest["tasks"] . $tmpquery . $this->orderBy('tas.due_date'));
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param $phaseId
     * @return mixed
     */
    public function getTasksByProjectIdAndParentPhaseAndStartEndDateNotBlank($projectId, $phaseId)
    {
        $tmpquery = " WHERE tas.project = :project_id AND tas.parent_phase = :phase_id AND tas.start_date != '--' AND tas.due_date != '--'";
        $this->db->query($this->initrequest["tasks"] . $tmpquery . $this->orderBy('tas.due_date'));
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':phase_id', $phaseId);
        return $this->db->resultset();
    }

    /**
     * @param String $sql
     * @return mixed
     */
    public function getReportTasks($sql)
    {
        $query = $this->initrequest["tasks"] . ' ' . $sql;
        $this->db->query($query);
        return $this->db->resultset();

    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getTeamTasks($projectId, $sorting = null)
    {
        $tmpQuery = $this->initrequest["tasks"];
        $tmpQuery .= <<<WHERE
 WHERE tas.project = :project_id 
 AND tas.assigned_to != '0' 
 AND tas.published = '0' 
 AND mem.organization = '1'
WHERE;

        $this->db->query($tmpQuery . $this->orderBy($sorting));
        $this->db->bind(":project_id", $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $taskId
     * @param $assignedDate
     * @return mixed
     */
    public function assignTaskTo($taskId, $assignedDate)
    {
        $sql = "UPDATE {$this->tableCollab["tasks"]} SET assigned = :assigned WHERE id = :task_id";
        $this->db->query($sql);
        $this->db->bind(':task_id', $taskId);
        $this->db->bind('assigned', $assignedDate);
        return $this->db->execute();
    }

    /**
     * @param $newAssignee
     * @param $assignedTo
     * @return mixed
     */
    public function setTasksAssignedToWhereAssignedToIn($newAssignee, $assignedTo)
    {
        // Generate placeholders
        $placeholders = str_repeat('?, ', count($assignedTo) - 1) . '?';

        $sql = "UPDATE {$this->tableCollab["tasks"]} SET assigned_to = ? WHERE assigned_to IN ($placeholders)";

        // Prepend the newAssignee value
        if (is_array($assignedTo)) {
            array_unshift($assignedTo, $newAssignee);
        } else {
            $assignedTo = explode(',', $newAssignee . ',' . $assignedTo);
        }
        $this->db->query($sql);
        return $this->db->execute($assignedTo);
    }

    /**
     * @param $taskIds
     * @return mixed
     */
    public function publishTasks($taskIds)
    {
        if (strpos($taskIds, ',')) {
            $taskIds = explode(',', $taskIds);
            $placeholders = str_repeat('?, ', count($taskIds) - 1) . '?';
            $sql = "UPDATE {$this->tableCollab['tasks']} SET published = 0 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($taskIds);
        } else {
            $sql = "UPDATE {$this->tableCollab['tasks']} SET published = 0 WHERE id = :topic_ids";
            $this->db->query($sql);
            $this->db->bind(':topic_ids', $taskIds);
            return $this->db->execute();
        }
    }

    /**
     * @param $taskIds
     * @return mixed
     */
    public function unPublishTasks($taskIds)
    {
        if (strpos($taskIds, ',')) {
            $taskIds = explode(',', $taskIds);
            $placeholders = str_repeat('?, ', count($taskIds) - 1) . '?';
            $sql = "UPDATE {$this->tableCollab['tasks']} SET published = 1 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($taskIds);
        } else {
            $sql = "UPDATE {$this->tableCollab['tasks']} SET published = 1 WHERE id = :topic_ids";
            $this->db->query($sql);
            $this->db->bind(':topic_ids', $taskIds);
            return $this->db->execute();
        }
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function addToSiteFile($ids)
    {
        $ids = explode(',', $ids);
        $placeholders = str_repeat('?, ', count($ids) - 1) . '?';
        $placeholders2 = str_repeat('?, ', count($ids) - 1) . '?';
        $sql = "UPDATE {$this->tableCollab["files"]} SET published=0 WHERE id IN ($placeholders) OR vc_parent IN ($placeholders2)";

        $this->db->query($sql);

        return $this->db->execute([$placeholders, $placeholders2]);
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function removeToSiteFile($ids)
    {
        $ids = explode(',', $ids);
        $placeholders = str_repeat('?, ', count($ids) - 1) . '?';
        $placeholders2 = str_repeat('?, ', count($ids) - 1) . '?';
        $sql = "UPDATE {$this->tableCollab["files"]} SET published=0 WHERE id IN ({$placeholders}) OR vc_parent IN ({$placeholders2})";

        $this->db->query($sql);

        return $this->db->execute([$placeholders, $placeholders2]);
    }

    /**
     * @param $oldOwner
     * @param $newOwner
     * @return mixed
     */
    public function reassignTasks($oldOwner, $newOwner)
    {
        $data = explode(',', $oldOwner);
        $placeholders = str_repeat('?, ', count($data) - 1) . '?';
        $sql = "UPDATE {$this->tableCollab["tasks"]} SET assigned_to = ? WHERE assigned_to IN($placeholders)";
        // Place newOwner at the beginning of array
        if (is_array($data)) {
            array_unshift($data, $newOwner);
        } else {
            $data = explode(',', $newOwner . ',' . $oldOwner);
        }
        $this->db->query($sql);
        return $this->db->execute($data);
    }

    /**
     * @param $taskIds
     * @return mixed
     */
    public function deleteTasks($taskIds)
    {
        $taskIds = explode(',', $taskIds);
        $placeholders = str_repeat('?, ', count($taskIds) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['tasks']} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($taskIds);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function deleteTasksByProject($projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['tasks']} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param $subTaskIds
     * @return mixed
     */
    public function deleteSubTasks($subTaskIds)
    {
        $subTaskIds = explode(',', $subTaskIds);
        $placeholders = str_repeat('?, ', count($subTaskIds) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['subtasks']} WHERE task IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($subTaskIds);
    }

    /**
     * @param $subtaskIds
     * @return mixed
     */
    public function deleteSubTasksById($subtaskIds)
    {
        $subtaskIds = explode(',', $subtaskIds);
        $placeholders = str_repeat('?, ', count($subtaskIds) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['subtasks']} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($subtaskIds);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function deleteSubtasksByProjectId($projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['subtasks']} WHERE task IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
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
     * @param $assignedDate
     * @return string
     */
    public function addTask($projectId, $name, $description, $owner, $assignedTo, $status, $priority, $startDate,
                            $dueDate, $estimatedTime, $actualTime, $comments, $published, $completion, $parentPhase,
                            $invoicing, $workedHours, $assignedDate)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["tasks"]} (
project, name, description, owner, assigned_to, status, priority, start_date, due_date, 
estimated_time, actual_time, comments, created, published, completion, parent_phase, 
invoicing, worked_hours, assigned
) VALUES(
:project_id, :task_name, :description, :owner, :assigned_to, :status, :priority, :start_date, :due_date, 
:estimated_time, :actual_time, :comments, :created, :published, :completion, :parent_phase, 
:invoicing, :worked_hours, :assigned
)
SQL;
        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':task_name', $name);
        $this->db->bind(':description', $description);
        $this->db->bind(':owner', $owner);
        $this->db->bind(':assigned_to', $assignedTo);
        $this->db->bind(':status', $status);
        $this->db->bind(':priority', $priority);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':due_date', $dueDate);
        $this->db->bind(':estimated_time', $estimatedTime);
        $this->db->bind(':actual_time', $actualTime);
        $this->db->bind(':comments', $comments);
        $this->db->bind(':created', date('Y-m-d h:i'));
        $this->db->bind(':published', $published);
        $this->db->bind(':completion', $completion);
        $this->db->bind(':parent_phase', $parentPhase);
        $this->db->bind(':invoicing', $invoicing);
        $this->db->bind(':worked_hours', $workedHours);
        $this->db->bind(':assigned', $assignedDate);

        $this->db->execute();
        return $this->db->lastInsertId();
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
        $sql = <<<SQL
UPDATE {$this->tableCollab["tasks"]} SET 
name = :task_name, 
description = :description, 
assigned_to = :assigned_to, 
status = :status, 
priority = :priority, 
start_date = :start_date, 
due_date = :due_date, 
estimated_time = :estimated_time, 
actual_time = :actual_time, 
comments = :comments, 
modified = :modified, 
completion = :completion, 
parent_phase = :parent_phase, 
published = :published, 
invoicing = :invoicing,
worked_hours = :worked_hours 
WHERE id = :task_id
SQL;
        $this->db->query($sql);
        $this->db->bind(':task_name', $taskName);
        $this->db->bind(':description', $description);
        $this->db->bind(':assigned_to', $assignedTo);
        $this->db->bind(':status', $status);
        $this->db->bind(':priority', $priority);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':due_date', $dueDate);
        $this->db->bind(':estimated_time', $estimatedTime);
        $this->db->bind(':actual_time', $actualTime);
        $this->db->bind(':comments', $comments);
        $this->db->bind(':modified', $modifiedDate);
        $this->db->bind(':completion', $completion);
        $this->db->bind(':parent_phase', $parentPhase);
        $this->db->bind(':published', $published);
        $this->db->bind(':invoicing', $invoicing);
        $this->db->bind(':worked_hours', $workedHours);
        $this->db->bind(':task_id', $taskId);

        return $this->db->execute();

    }

    /**
     * @param $projectId
     * @param $taskId
     * @return mixed
     */
    public function setProjectByTaskId($projectId, $taskId)
    {
        $sql = "UPDATE {$this->tableCollab["tasks"]} SET project = :project_id WHERE id = :task_id";
        $this->db->query($sql);
        $this->db->bind(":project_id", $projectId);
        $this->db->bind(":task_id", $taskId);
        return $this->db->execute();
    }

    /**
     * @param $taskId
     * @param $date
     * @return mixed
     */
    public function setCompletionDateForTaskById($taskId, $date)
    {
        $sql = "UPDATE {$this->tableCollab["tasks"]} SET complete_date = :complete_date WHERE id = :task_id";
        $this->db->query($sql);
        $this->db->bind(':task_id', $taskId);
        $this->db->bind(':complete_date', $date);
        return $this->db->execute();
    }


    /**
     * Returns the LIMIT attribute for SQL strings
     * @param $start
     * @param $rowLimit
     * @return string
     */
    private function limit($start, $rowLimit)
    {
        if (!is_null($start) && !is_null($rowLimit)) {
            return " LIMIT {$start},{$rowLimit}";
        }
        return '';

    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = [
                "tas.id", "tas.project", "tas.priority", "tas.status", "tas.owner", "tas.assigned_to", "tas.name",
                "tas.description", "tas.start_date", "tas.due_date", "tas.estimated_time", "tas.actual_time",
                "tas.comments", "tas.completion", "tas.created", "tas.modified", "tas.assigned", "tas.published",
                "tas.parent_phase", "tas.complete_date", "tas.invoicing", "tas.worked_hours", "mem.id", "mem.name",
                "mem.login", "mem.email_work", "mem2.id", "mem2.name", "mem2.login", "mem2.email_work", "mem.organization",
                "pro.name", "org.id", "subtas.id", "subtas.task", "subtas.priority", "subtas.status", "subtas.owner",
                "subtas.assigned_to", "subtas.name", "subtas.description", "subtas.start_date", "subtas.due_date",
                "subtas.estimated_time", "subtas.actual_time", "subtas.comments", "subtas.completion", "subtas.created",
                "subtas.modified", "subtas.assigned", "subtas.published", "subtas.complete_date"];
            $pieces = explode(' ', $sorting);

            if ($pieces) {
                $key = array_search($pieces[0], $allowedOrderedBy);

                if ($key !== false) {
                    $order = $allowedOrderedBy[$key];
                    return " ORDER BY $order $pieces[1]";
                }
            }
        }

        return '';
    }
}
