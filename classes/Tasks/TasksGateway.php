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
        $placeholders = str_repeat ('?, ', count($taskIds)-1) . '?';
        $whereStatement = " WHERE tas.id IN($placeholders)";
        $this->db->query($this->initrequest["tasks"] . $whereStatement);
        return $this->db->execute($taskIds);
    }

    /**
     * @param $assignedToIds
     * @return mixed
     */
    public function getTasksAssignedTo($assignedToIds)
    {
        $assignedToIds = explode(',', $assignedToIds);
        $placeholders = str_repeat ('?, ', count($assignedToIds)-1) . '?';
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
     * @param $sorting
     * @return mixed
     */
    public function getTasksByProjectId($projectId, $sorting = null)
    {
        $whereStatement = " WHERE tas.project = :project_id";
        $this->db->query($this->initrequest["tasks"] . $whereStatement . $this->orderBy($sorting));
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
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
        $this->db->bind(':phase_id', $phaseId);
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
        $placeholders = str_repeat ('?, ', count($subtaskId)-1) . '?';
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

        return $this->db->single();
    }

    /**
     * @param $parentTaskIds
     * @return mixed
     */
    public function getSubtasksByParentTaskIdIn($parentTaskIds)
    {
        $placeholders = str_repeat ('?, ', count($parentTaskIds)-1) . '?';

        $sql = $this->initrequest["subtasks"] . " WHERE task IN ($placeholders)";

        $this->db->query($sql);
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
     * @param $newAssignee
     * @param $assignedTo
     * @return mixed
     */
    public function setTasksAssignedToWhereAssignedToIn($newAssignee, $assignedTo)
    {
        // Generate placeholders
        $placeholders = str_repeat ('?, ', count($assignedTo)-1) . '?';

        $sql = "UPDATE {$this->tableCollab["tasks"]} SET assigned_to = ? WHERE assigned_to IN ($placeholders)";

        // Prepend the newAssignee value
        if (is_array($assignedTo)) {
            array_unshift($assignedTo, $newAssignee);
        } else {
            $assignedTo = explode(',', $newAssignee . ',' . $assignedTo);
        }
        $this->db->query($sql);
        $this->db->execute($assignedTo);
        return $this->db->fetchAll();
    }

    /**
     * @param $taskIds
     * @return mixed
     */
    public function publishTasks($taskIds)
    {
        if ( strpos($taskIds, ',') ) {
            $taskIds = explode(',', $taskIds);
            $placeholders = str_repeat ('?, ', count($taskIds)-1) . '?';
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
        if ( strpos($taskIds, ',') ) {
            $taskIds = explode(',', $taskIds);
            $placeholders = str_repeat ('?, ', count($taskIds)-1) . '?';
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
        $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
        $placeholders2 = str_repeat ('?, ', count($ids)-1) . '?';
        $sql = "UPDATE files SET published=0 WHERE id IN ($placeholders) OR vc_parent IN ($placeholders2)";

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
        $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
        $placeholders2 = str_repeat ('?, ', count($ids)-1) . '?';
        $sql = "UPDATE files SET published=0 WHERE id IN ({$placeholders}) OR vc_parent IN ({$placeholders2})";

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
        $placeholders = str_repeat ('?, ', count($taskIds)-1) . '?';
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
        $placeholders = str_repeat ('?, ', count($projectId)-1) . '?';
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
        $placeholders = str_repeat ('?, ', count($subTaskIds)-1) . '?';
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
        $placeholders = str_repeat ('?, ', count($subtaskIds)-1) . '?';
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
        $placeholders = str_repeat ('?, ', count($projectId)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['subtasks']} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["tas.id","tas.project","tas.priority","tas.status","tas.owner","tas.assigned_to","tas.name","tas.description","tas.start_date","tas.due_date","tas.estimated_time","tas.actual_time","tas.comments","tas.completion","tas.created","tas.modified","tas.assigned","tas.published","tas.parent_phase","tas.complete_date","tas.invoicing","tas.worked_hours","mem.id","mem.name","mem.login","mem.email_work","mem2.id","mem2.name","mem2.login","mem2.email_work","mem.organization","pro.name","org.id", "subtas.id","subtas.task","subtas.priority","subtas.status","subtas.owner","subtas.assigned_to","subtas.name","subtas.description","subtas.start_date","subtas.due_date","subtas.estimated_time","subtas.actual_time","subtas.comments","subtas.completion","subtas.created","subtas.modified","subtas.assigned","subtas.published","subtas.complete_date"];
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
