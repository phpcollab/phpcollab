<?php

namespace phpCollab\Calendars;

use phpCollab\Database;
use Exception;

/**
 * Class Calendars
 * @package phpCollab\Calendars
 */
class Calendars
{
    protected $calendars_gateway;
    protected $db;

    /**
     * Calendars constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->calendars_gateway = new CalendarsGateway($this->db);
    }

    /**
     * @param $calendarId
     * @return mixed
     */
    public function deleteCalendar($calendarId)
    {
        try {
            $calendarId = filter_var((string)$calendarId, FILTER_SANITIZE_STRING);

            $response = $this->calendars_gateway->deleteCalendar($calendarId);
            return $response;
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            return true;
        }
    }

    /**
     * @param $calendarId
     * @return mixed
     */
    public function openCalendarById($calendarId)
    {
        $calendarId = filter_var($calendarId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarById($calendarId);
        return $calendar;
    }

    /**
     * @param $ownerId
     * @param $calendarId
     * @return mixed
     */
    public function openCalendarByOwnerAndId($ownerId, $calendarId)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendarId = filter_var($calendarId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarByOwnerAndId($ownerId, $calendarId);
        return $calendar;
    }

    /**
     * @param $ownerId
     * @return mixed
     */
    public function openCalendarByOwnerOrIsBroadcast($ownerId)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->openCalendarByOwnerOrIsBroadcast($ownerId);
        return $calendar;
    }

    /**
     * @param $ownerId
     * @param $calendarId
     * @return mixed
     */
    public function openCalendarDetail($ownerId, $calendarId)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendarId = filter_var($calendarId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarByOwnerAndId($ownerId, $calendarId);
        return $calendar;
    }

    /**
     * @param $ownerId
     * @param $calendarDate
     * @param $recurringDay
     * @return mixed
     */
    public function openCalendarMonth($ownerId, $calendarDate, $recurringDay)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarMonth($ownerId, $calendarDate, $recurringDay);
        return $calendar;
    }

    /**
     * @param $ownerId
     * @param $calendarDate
     * @param $recurringDay
     * @return mixed
     */
    public function openCalendarDay($ownerId, $calendarDate, $recurringDay)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarDay($ownerId, $calendarDate, $recurringDay);
        return $calendar;
    }

    public function addCalendarEvent(
        $owner, $subject, $description, $location, $shortname, $date_start, $date_end, $time_start, $time_end,
        $reminder, $broadcast, $recurring, $recur_day
    )
    {
        return $this->calendars_gateway->addEvent($owner, $subject, $description, $location, $shortname,
            $date_start, $date_end, $time_start, $time_end, $reminder, $broadcast, $recurring, $recur_day);
    }

    public function editCalendarEvent(
        $owner, $subject, $description, $location, $shortname, $date_start, $date_end, $time_start, $time_end,
        $reminder, $broadcast, $recurring, $recur_day
    )
    {
        return $this->calendars_gateway->updateEvent($owner, $subject, $description, $location, $shortname,
            $date_start, $date_end, $time_start, $time_end, $reminder, $broadcast, $recurring, $recur_day);
    }



}
