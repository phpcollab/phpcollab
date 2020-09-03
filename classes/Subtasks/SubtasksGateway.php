<?php

namespace phpCollab\Subtasks;

use phpCollab\Database;

/**
 * Class SubtasksGateway
 * @package phpCollab\Subtasks
 */
class SubtasksGateway
{
    protected $db;
    protected $initrequest;

    /**
     * TasksGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];

    }

    /**
     * @param $subtaskId
     * @return mixed
     */
    public function getById($subtaskId)
    {
        $whereStatement = " WHERE subtas.id = :sub_task_id";

        $this->db->query($this->initrequest["subtasks"] . $whereStatement);

        $this->db->bind(':sub_task_id', $subtaskId);

        return $this->db->single();

    }

    /**
     * @param $parentTaskId
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
     * @param $completion
     * @param $published
     * @param $created
     * @return string
     */
    public function addSubtask(
        $parentTaskId,
        $name,
        $description,
        $owner,
        $assignedTo,
        $status,
        $priority,
        $startDate,
        $dueDate,
        $estimatedTime,
        $actualTime,
        $comments,
        $completion,
        $published,
        $created
    ) {
        $sql = <<< SQL
INSERT INTO {$this->db->getTableName("subtasks")} (
task, name, description, owner, assigned_to, status, priority, start_date, due_date, estimated_time, actual_time, 
comments, published, completion, created
) VALUES (
:parent_task_id, :name, :description, :owner, :assigned_to, :status, :priority, :start_date, :due_date, :estimated_time, 
:actual_time, :comments, :published, :completion, :created
)
SQL;
        $this->db->query($sql);
        $this->db->bind(":parent_task_id", $parentTaskId);
        $this->db->bind(":name", $name);
        $this->db->bind(":description", $description);
        $this->db->bind(":owner", $owner);
        $this->db->bind(":assigned_to", $assignedTo);
        $this->db->bind(":status", $status);
        $this->db->bind(":priority", $priority);
        $this->db->bind(":start_date", $startDate);
        $this->db->bind(":due_date", $dueDate);
        $this->db->bind(":estimated_time", $estimatedTime);
        $this->db->bind(":actual_time", $actualTime);
        $this->db->bind(":comments", $comments);
        $this->db->bind(":completion", $completion);
        $this->db->bind(":published", $published);
        $this->db->bind(":created", $created);
        $this->db->execute();
        return $this->db->lastInsertId();

    }

    /**
     * @param $subtaskId
     * @param $name
     * @param $description
     * @param $assignedTo
     * @param $status
     * @param $priority
     * @param $startDate
     * @param $dueDate
     * @param $estimatedTime
     * @param $actualTime
     * @param $comments
     * @param $modified
     * @param $completion
     * @param $published
     * @return mixed
     */
    public function updateSubtask(
        $subtaskId,
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
        $modified,
        $completion,
        $published
    ) {

        $sql = <<< SQL
UPDATE {$this->db->getTableName("subtasks")}
SET
name = :name,
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
published = :published
WHERE id = :subtask_id
SQL;

        $this->db->query($sql);
        $this->db->bind(':subtask_id', $subtaskId);
        $this->db->bind(':name', $name);
        $this->db->bind(':description', $description);
        $this->db->bind(':assigned_to', $assignedTo);
        $this->db->bind(':status', $status);
        $this->db->bind(':priority', $priority);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':due_date', $dueDate);
        $this->db->bind(':estimated_time', $estimatedTime);
        $this->db->bind(':actual_time', $actualTime);
        $this->db->bind(':comments', $comments);
        $this->db->bind(':modified', $modified);
        $this->db->bind(':completion', $completion);
        $this->db->bind(':published', $published);
        return $this->db->execute();
    }

    /**
     * @param $subtaskId
     * @return mixed
     */
    public function publishSubtask($subtaskId)
    {
        $sql = "UPDATE {$this->db->getTableName("subtasks")} SET published = 0 WHERE id = :subtask_id";
        $this->db->query($sql);
        $this->db->bind(':subtask_id', $subtaskId);
        return $this->db->execute();
    }

    /**
     * @param $subtaskId
     * @return mixed
     */
    public function unpublishSubtask($subtaskId)
    {
        $sql = "UPDATE {$this->db->getTableName("subtasks")} SET published = 1 WHERE id = :subtask_id";
        $this->db->query($sql);
        $this->db->bind(':subtask_id', $subtaskId);
        return $this->db->execute();
    }


    /**
     * @param $subtaskId
     * @param $date
     * @return mixed
     */
    public function setCompletionDate($subtaskId, $date)
    {
        $sql = "UPDATE {$this->db->getTableName("subtasks")} SET complete_date = :complete_date WHERE id = :subtask_id";
        $this->db->query($sql);
        $this->db->bind(':subtask_id', $subtaskId);
        $this->db->bind(':complete_date', $date);
        return $this->db->execute();
    }

    /**
     * @param $subtaskId
     * @param $date
     * @return mixed
     */
    public function setAssignedDate($subtaskId, $date)
    {
        $sql = "UPDATE {$this->db->getTableName("subtasks")} SET assigned = :assigned_date WHERE id = :subtask_id";
        $this->db->query($sql);
        $this->db->bind(':subtask_id', $subtaskId);
        $this->db->bind(':assigned_date', $date);
        return $this->db->execute();
    }
}
