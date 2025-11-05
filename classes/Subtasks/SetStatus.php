<?php


namespace phpCollab\Subtasks;


use InvalidArgumentException;
use phpCollab\Database;
use phpCollab\Notifications\Notifications;
use phpCollab\Notifications\SubtaskNotifications;

/**
 * Class SetStatus
 *
 * Handles setting subtask status with pure constructor injection.
 *
 * @package phpCollab\Subtasks
 */
class SetStatus extends Subtasks
{
    /**
     * SetStatus constructor.
     *
     * Uses pure constructor injection - all dependencies are explicit.
     *
     * @param Database $database Database connection
     * @param Notifications $notifications Notifications manager for storing notification records
     * @param SubtaskNotifications $subtaskNotifications Service for sending subtask-related notifications
     */
    public function __construct(
        Database $database,
        Notifications $notifications,
        SubtaskNotifications $subtaskNotifications
    ) {
        parent::__construct($database, $notifications, $subtaskNotifications);
    }

    public function set(int $subtaskId, int $status, $comments = null, $modifiedDate = null, $completedDate = null)
    {

        if (!is_int(filter_var($subtaskId, FILTER_VALIDATE_INT))) {
            throw new InvalidArgumentException('Subtask ID is missing or invalid.');
        } else {
            if (!is_int(filter_var($status, FILTER_VALIDATE_INT))) {
                throw new InvalidArgumentException('Status is missing or invalid.');
            }
        }

        $timestamp = date('Y-m-d h:i');

        $modifiedDate = (is_null($modifiedDate)) ? $timestamp : $modifiedDate;

        // If the status is "0 - client completed" or "1 - completed", then we want to set the completion date,
        // otherwise leave it as null.
        if ($status === 0 || $status === 1) {
            $completedDate = (is_null($completedDate)) ? $timestamp : $completedDate;
        }

        return $this->updateStatus($subtaskId, $status, $comments, $modifiedDate, $completedDate);

    }

    private function updateStatus($subtaskId, $status, $comment, $modifiedDate, $completedDate)
    {
        $sql = <<<SQL
UPDATE {$this->db->getTableName("subtasks")} 
SET 
comments = :comments, 
status = :status, 
modified = :modified_date, 
complete_date = :completed_date 
WHERE id = :subtask_id
SQL;
        $this->db->query($sql);
        $this->db->bind(':subtask_id', $subtaskId);
        $this->db->bind(':comments', $comment);
        $this->db->bind(':status', $status);
        $this->db->bind(':modified_date', $modifiedDate);
        $this->db->bind(':completed_date', $completedDate);
        return $this->db->execute();

    }
}
