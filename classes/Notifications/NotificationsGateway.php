<?php


namespace phpCollab\Notifications;

use phpCollab\Database;

/**
 * Class NotificationsGateway
 * @package phpCollab\Notifications
 */
class NotificationsGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * NotificationsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getNotificationsWhereMemeberIn($memberId)
    {
        $memberId = explode(',', $memberId);
        $placeholders = str_repeat ('?, ', count($memberId)-1) . '?';
        $whereStatement = " WHERE noti.member IN($placeholders)";
        $this->db->query($this->initrequest["notifications"] . $whereStatement);
        $this->db->execute($memberId);
        return $this->db->fetchAll();

    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getNotificationsByMemberId($memberId)
    {
        $whereStatement = " WHERE noti.member = :member_id";
        $this->db->query($this->initrequest["notifications"] . $whereStatement);
        $this->db->bind(":member_id", $memberId);
        return $this->db->single();
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function addMember($memberId)
    {
        $sql = "INSERT INTO {$this->tableCollab["notifications"]} (member,taskAssignment,removeProjectTeam,addProjectTeam,newTopic,newPost,statusTaskChange,priorityTaskChange,duedateTaskChange,clientAddTask,uploadFile,dailyAlert,weeklyAlert,pastdueAlert) VALUES (:member_id,0,0,0,0,0,0,0,0,0,0,0,0,0)";
        $this->db->query($sql);
        $this->db->bind(":member_id", $memberId);
        return $this->db->execute();
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function deleteNotificationsByMemberIdIn($memberId)
    {
        // Generate placeholders
        $memberId = explode(',', $memberId);
        $placeholders = str_repeat('?, ', count($memberId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['notifications']} WHERE member IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($memberId);

    }

    /**
     * @param $memberId
     * @param $taskAssignment
     * @param $removeProjectTeam
     * @param $addProjectTeam
     * @param $newTopic
     * @param $newPost
     * @param $statusTaskChange
     * @param $priorityTaskChange
     * @param $dueDateTaskChange
     * @param $clientAddTask
     * @param $uploadFile
     * @param $dailyAlert
     * @param $weeklyAlert
     * @param $pastDueAlert
     * @return mixed
     */
    public function updateAlerts($memberId, $taskAssignment, $removeProjectTeam, $addProjectTeam, $newTopic, $newPost,
                                 $statusTaskChange, $priorityTaskChange, $dueDateTaskChange, $clientAddTask,
                                 $uploadFile, $dailyAlert, $weeklyAlert, $pastDueAlert)
    {

        $query = <<<SQL
UPDATE {$this->tableCollab["notifications"]} SET 
taskAssignment = :taskAssignment,
statusTaskChange = :statusTaskChange,
priorityTaskChange = :priorityTaskChange,
duedateTaskChange = :dueDateTaskChange,
addProjectTeam = :addProjectTeam,
removeProjectTeam = :removeProjectTeam,
newPost = :newPost,
newTopic = :newTopic,
clientAddTask = :clientAddTask,
uploadFile = :uploadFile,
dailyAlert = :dailyAlert,
weeklyAlert = :weeklyAlert,
pastdueAlert = :pastdueAlert 
WHERE member = :member
SQL;
        $this->db->query($query);
        $this->db->bind(":taskAssignment", $taskAssignment);
        $this->db->bind(":statusTaskChange", $statusTaskChange);
        $this->db->bind(":priorityTaskChange", $priorityTaskChange);
        $this->db->bind(":dueDateTaskChange", $dueDateTaskChange);
        $this->db->bind(":addProjectTeam", $addProjectTeam);
        $this->db->bind(":removeProjectTeam", $removeProjectTeam);
        $this->db->bind(":newPost", $newPost);
        $this->db->bind(":newTopic", $newTopic);
        $this->db->bind(":clientAddTask", $clientAddTask);
        $this->db->bind(":uploadFile", $uploadFile);
        $this->db->bind(":dailyAlert", $dailyAlert);
        $this->db->bind(":weeklyAlert", $weeklyAlert);
        $this->db->bind(":pastdueAlert", $pastDueAlert);
        $this->db->bind(":member", $memberId);
        return $this->db->execute();
    }
}
