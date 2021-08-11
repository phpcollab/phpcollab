<?php


namespace phpCollab\Tasks;


use InvalidArgumentException;
use phpCollab\Container;
use phpCollab\Database;

class SetTaskStatus extends Tasks
{
    public function __construct(Database $database, Container $container)
    {
        parent::__construct($database, $container);
    }

    /**
     * @param int $taskId
     * @param int $status
     * @param null $comments
     * @param null $modifiedDate
     * @param null $completedDate
     * @return mixed
     */
    public function set(int $taskId, int $status, $comments = null, $modifiedDate = null, $completedDate = null)
    {

        if (!is_int(filter_var($taskId, FILTER_VALIDATE_INT))) {
            throw new InvalidArgumentException('Task ID is missing or invalid.');
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

        return $this->updateStatus($taskId, $status, $comments, $modifiedDate, $completedDate);

    }

    /**
     * @param int $taskId
     * @param int $status
     * @param string $comment
     * @param string $modifiedDate
     * @param string $completedDate
     * @return mixed
     */
    private function updateStatus(int $taskId, int $status, string $comment, string $modifiedDate, string $completedDate)
    {
        $sql = <<<SQL
UPDATE {$this->db->getTableName("tasks")} 
SET 
comments = :comments, 
status = :status, 
modified = :modified_date, 
complete_date = :completed_date 
WHERE id = :task_id
SQL;
        $this->db->query($sql);
        $this->db->bind(':task_id', $taskId);
        $this->db->bind(':comments', $comment);
        $this->db->bind(':status', $status);
        $this->db->bind(':modified_date', $modifiedDate);
        $this->db->bind(':completed_date', $completedDate);
        return $this->db->execute();

    }
}
