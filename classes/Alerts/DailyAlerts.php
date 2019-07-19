<?php


namespace phpCollab\Alerts;


use Exception;
use phpCollab\Database;

class DailyAlerts
{
    protected $db;
    protected $membersTable;
    protected $notificationsTable;
    protected $tasksTable;
    protected $subtasksTable;
    protected $projectsTable;
    protected $today;

    public function __construct()
    {
        $this->db = new Database();
        $this->today = date("Y-m-d", time());
    }


    /**
     * @return mixed
     */
    public function getMembersTable()
    {
        return $this->membersTable;
    }

    /**
     * @param mixed $membersTable
     */
    public function setMembersTable($membersTable)
    {
        $this->membersTable = $membersTable;
    }

    /**
     * @return mixed
     */
    public function getNotificationsTable()
    {
        return $this->notificationsTable;
    }

    /**
     * @param mixed $notificationsTable
     */
    public function setNotificationsTable($notificationsTable)
    {
        $this->notificationsTable = $notificationsTable;
    }

    /**
     * @return mixed
     */
    public function getTasksTable()
    {
        return $this->tasksTable;
    }

    /**
     * @param mixed $tasksTable
     */
    public function setTasksTable($tasksTable)
    {
        $this->tasksTable = $tasksTable;
    }

    /**
     * @return mixed
     */
    public function getSubtasksTable()
    {
        return $this->subtasksTable;
    }

    /**
     * @param mixed $subtasksTable
     */
    public function setSubtasksTable($subtasksTable)
    {
        $this->subtasksTable = $subtasksTable;
    }

    /**
     * @return mixed
     */
    public function getProjectsTable()
    {
        return $this->projectsTable;
    }

    /**
     * @param mixed $projectsTable
     */
    public function setProjectsTable($projectsTable)
    {
        $this->projectsTable = $projectsTable;
    }


    /**
     * @return mixed
     */
    public function getDailyAlertMembers()
    {
        $query = <<<SQL
SELECT {$this->membersTable}.id, {$this->membersTable}.name, {$this->membersTable}.email_work
FROM {$this->membersTable}, {$this->notificationsTable} 
WHERE {$this->membersTable}.id = {$this->notificationsTable}.member
AND {$this->notificationsTable}.dailyAlert = 0
SQL;

        $this->db->query($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    /**
     * @param $assignedTo
     * @return mixed
     */
    public function getTasks($assignedTo)
    {
        $query = <<<SQL
SELECT {$this->tasksTable}.id,
     {$this->tasksTable}.name,
     {$this->tasksTable}.priority, 
     {$this->tasksTable}.status, 
     {$this->tasksTable}.completion,
     {$this->tasksTable}.start_date, 
     {$this->tasksTable}.due_date,
     {$this->tasksTable}.description,
     {$this->projectsTable}.id as project_id, 
     {$this->projectsTable}.name as project_name
FROM  {$this->tasksTable}, {$this->projectsTable}, {$this->membersTable}
WHERE {$this->tasksTable}.assigned_to = :assigned_to
AND {$this->tasksTable}.status != 1 
AND {$this->tasksTable}.due_date = '{$this->today}'
AND {$this->tasksTable}.project = {$this->projectsTable}.id
AND {$this->tasksTable}.assigned_to = {$this->membersTable}.id
ORDER BY {$this->tasksTable}.priority
SQL;
        $this->db->query($query);
        $this->db->bind(":assigned_to", $assignedTo);
        return $this->db->resultset();

    }

    /**
     * @param $assignedTo
     * @return mixed
     */
    public function getSubTasks($assignedTo)
    {
        $query = <<<SQL
SELECT {$this->subtasksTable}.id,
     {$this->subtasksTable}.name,
     {$this->subtasksTable}.priority, 
     {$this->subtasksTable}.status, 
     {$this->subtasksTable}.completion,
     {$this->subtasksTable}.start_date, 
     {$this->subtasksTable}.due_date,
     {$this->subtasksTable}.description,
     {$this->tasksTable}.id as parent_id, 
     {$this->tasksTable}.name as parent_name
FROM  {$this->subtasksTable}, {$this->tasksTable}, {$this->membersTable}
WHERE {$this->subtasksTable}.assigned_to = :assigned_to
AND {$this->subtasksTable}.status != 1
AND {$this->subtasksTable}.due_date = '$this->today'
AND {$this->subtasksTable}.task = {$this->tasksTable}.id
AND {$this->subtasksTable}.assigned_to = {$this->membersTable}.id
ORDER BY {$this->subtasksTable}.priority
SQL;
        $this->db->query($query);
        $this->db->bind(":assigned_to", $assignedTo);
        return $this->db->resultset();
    }

    /**
     * @param $lang
     * @return string
     */
    public function sendEmail($lang)
    {
        try {
            $email = new DailyAlertEmail($lang);

            $membersToNotify = $this->getDailyAlertMembers();

            foreach ($membersToNotify as $member) {
                $tasks = $this->getTasks($member["id"]);

                $subtasks = $this->getSubTasks($member["id"]);

                if ($member) {
                    $email->sendEmail($member, $tasks, $subtasks);
                }
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
