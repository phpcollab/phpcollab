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
/**
     * CalendarsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];

    }

    /**
     * @param $calendarId
     * @return mixed
     */
    public function deleteCalendar($calendarId)
    {
        $query = "DELETE FROM {$this->db->getTableName("calendar")} WHERE id IN(:calendar_id)";
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
        $ids = explode(',', $calendarId);
        $placeholders = str_repeat('?, ', count($ids) - 1) . '?';
        $whereStatement = " WHERE cal.id IN($placeholders) ";
        $this->db->query($this->initrequest["calendar"] . $whereStatement);
        $this->db->execute($ids);
        return $this->db->fetchAll();
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
        $query = $this->initrequest["calendar"] . "WHERE cal.owner = :cal_owner OR cal.broadcast = '1' ";
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
     * @param null $sorting
     * @return mixed
     */
    public function getCalendarDay($ownerId, $calendarDate, $recurringDay, $sorting = null)
    {
        $query = $this->initrequest["calendar"] . " WHERE (cal.owner = :calendar_owner AND ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day AND cal.recurring = '0') OR ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day) AND cal.recurring = '1' AND cal.recur_day = :recurring_day))) OR (cal.broadcast = '1' AND ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day AND cal.recurring = '0') OR ((cal.date_start <= :calendar_day AND cal.date_end >= :calendar_day) AND cal.recurring = '1' AND cal.recur_day = :recurring_day)))";
        $this->db->query($query . $this->orderBy($sorting));
        $this->db->bind(':calendar_owner', $ownerId);
        $this->db->bind(':calendar_day', $calendarDate);
        $this->db->bind(':recurring_day', $recurringDay);
        return $this->db->resultset();
    }

    public function addEvent(
        $owner,
        $subject,
        $description,
        $location,
        $shortname,
        $date_start,
        $date_end,
        $time_start,
        $time_end,
        $reminder,
        $broadcast,
        $recurring,
        $recur_day
    ) {
        $sql = <<<SQL
INSERT INTO {$this->db->getTableName("calendar")} (
owner,subject,description,location,shortname,date_start,date_end,time_start,time_end,reminder,broadcast,recurring,recur_day
) VALUES(
:owner,:subject,:description,:location,:shortname,:date_start,:date_end,:time_start,:time_end,:reminder,:broadcast,:recurring,:recur_day
)
SQL;
        $this->db->query($sql);
        $this->db->bind(':owner', $owner);
        $this->db->bind(':subject', $subject);
        $this->db->bind(':description', $description);
        $this->db->bind(':location', $location);
        $this->db->bind(':shortname', $shortname);
        $this->db->bind(':date_start', $date_start);
        $this->db->bind(':date_end', $date_end);
        $this->db->bind(':time_start', $time_start);
        $this->db->bind(':time_end', $time_end);
        $this->db->bind(':reminder', $reminder);
        $this->db->bind(':broadcast', $broadcast);
        $this->db->bind(':recurring', $recurring);
        $this->db->bind(':recur_day', $recur_day);
        $this->db->execute();
        return $this->db->lastInsertId();

    }

    public function updateEvent(
        $calendarId,
        $subject,
        $description,
        $location,
        $shortname,
        $date_start,
        $date_end,
        $time_start,
        $time_end,
        $reminder,
        $broadcast,
        $recurring,
        $recur_day
    ) {
        $sql = <<<SQL
UPDATE {$this->db->getTableName("calendar")} 
SET 
subject = :subject,
description = :description,
location = :location,
shortname = :shortname,
date_start = :date_start,
date_end = :date_end,
time_start = :time_start,
time_end = :time_end,
reminder = :reminder,
recurring = :recurring,
recur_day = :recur_day,
broadcast = :broadcast 
WHERE id = :calendar_id
SQL;
        $this->db->query($sql);
        $this->db->bind(':calendar_id', $calendarId);
        $this->db->bind(':subject', $subject);
        $this->db->bind(':description', $description);
        $this->db->bind(':location', $location);
        $this->db->bind(':shortname', $shortname);
        $this->db->bind(':date_start', $date_start);
        $this->db->bind(':date_end', $date_end);
        $this->db->bind(':time_start', $time_start);
        $this->db->bind(':time_end', $time_end);
        $this->db->bind(':reminder', $reminder);
        $this->db->bind(':broadcast', $broadcast);
        $this->db->bind(':recurring', $recurring);
        $this->db->bind(':recur_day', $recur_day);
        return $this->db->execute();
    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = [
                "cal.owner",
                "cal.subject",
                "cal.description",
                "cal.shortname",
                "cal.date_start",
                "cal.date_end",
                "cal.time_start",
                "cal.time_end",
                "cal.reminder",
                "cal.recurring",
                "cal.recur_day",
                "cal.broadcast",
                "cal.location",
                "mem.email_work",
                "mem.name"
            ];
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
