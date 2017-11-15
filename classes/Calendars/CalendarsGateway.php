<?php


namespace phpCollab\Calendars;

use phpCollab\Database;

/**
 * Class CalendarsGateway
 * @package phpCollab\Calendars
 */
class CalendarsGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * CalendarsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $calendarId
     * @return mixed
     */
    public function deleteCalendar($calendarId)
    {
        $query = "DELETE FROM {$this->tableCollab["calendar"]} WHERE id IN(:calendar_id)";
        $this->db->query($query);
        $this->db->bind(':calendar_id', $calendarId);
        return $this->db->execute();
    }


    /**
     * @param $calendarId
     * @return mixed
     */
    public function getCalendarById($calendarId)
    {
        $query = $this->initrequest["calendar"] . " WHERE cal.id IN(:calendar_id) ORDER BY cal.subject";

        $this->db->query($query);

        $this->db->bind(':calendar_id', $calendarId);

        return $this->db->resultset();
    }

    /**
     * @param $ownerId
     * @param $calendarId
     * @return mixed
     */
    public function getCalendarByOwnerAndId($ownerId, $calendarId)
    {
        $query = $this->initrequest["calendar"] . " WHERE cal.owner = :cal_owner AND cal.id = :calendar_id";
        $this->db->query($query);
        $this->db->bind(':cal_owner', $ownerId);
        $this->db->bind(':calendar_id', $calendarId);
        return $this->db->single();
    }

    /**
     * @param $ownerId
     * @return mixed
     */
    public function openCalendarByOwnerOrIsBroadcast($ownerId)
    {
        $query = $this->initrequest["calendar"] . "WHERE cal.owner = :cal_owner OR cal.broadcast = 1 ";
        $this->db->query($query);
        $this->db->bind(':cal_owner', $ownerId);
        return $this->db->resultset();
    }

    /**
     * @param $ownerId
     * @param $calendarId
     * @return mixed
     */
    public function getCalendarDetail($ownerId, $calendarId)
    {
        $query = $this->initrequest["calendar"] . " WHERE (cal.owner = :calendar_owner AND cal.id = :calendar_id) OR (cal.broadcast = '1' AND cal.id = :calendar_id)";

        $this->db->query($query);
        $this->db->bind(':calendar_owner', $ownerId);
        $this->db->bind(':calendar_id', $calendarId);
        return $this->db->single();
    }

    /**
     * @param $ownerId
     * @param $calendarDate
     * @param $recurringDay
     * @return mixed
     */
    public function getCalendarMonth($ownerId, $calendarDate, $recurringDay)
    {
        $query = $this->initrequest["calendar"] . " WHERE (cal.owner = :calendar_owner AND ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day AND cal.recurring = '0') OR ((cal.date_start <= :calendar_day AND cal.date_end <= :calendar_day) AND cal.recurring = '1' AND cal.recur_day = :recurring_day))) OR (cal.broadcast = '1' AND ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day AND cal.recurring = '0') OR ((cal.date_start <= :calendar_day AND cal.date_end <= :calendar_day) AND cal.recurring = '1' AND cal.recur_day = :recurring_day))) ORDER BY cal.shortname";

        $this->db->query($query);
        $this->db->bind(':calendar_owner', $ownerId);
        $this->db->bind(':calendar_day', $calendarDate);
        $this->db->bind(':recurring_day', $recurringDay);
        return $this->db->resultset();
    }

    /**
     * @param $ownerId
     * @param $calendarDate
     * @param $recurringDay
     * @return mixed
     */
    public function getCalendarDay($ownerId, $calendarDate, $recurringDay)
    {
        $query = $this->initrequest["calendar"] . " WHERE (cal.owner = :calendar_owner AND ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day AND cal.recurring = '0') OR ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day) AND cal.recurring = '1' AND cal.recur_day = :recurring_day))) OR (cal.broadcast = '1' AND ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day AND cal.recurring = '0') OR ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day) AND cal.recurring = '1' AND cal.recur_day = :recurring_day))) ORDER BY cal.shortname";
        $this->db->query($query);
        $this->db->bind(':calendar_owner', $ownerId);
        $this->db->bind(':calendar_day', $calendarDate);
        $this->db->bind(':recurring_day', $recurringDay);
        return $this->db->resultset();
    }

}
