<?php

namespace phpCollab\Calendars;

use phpCollab\Database;
use Exception;

class Calendars
{
    protected $calendars_gateway;
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->calendars_gateway = new CalendarsGateway($this->db);
    }

    public function deleteCalendar($calendarId)
    {
        try {
            $calendarId = filter_var((string)$calendarId, FILTER_SANITIZE_STRING);

            $response = $this->calendars_gateway->deleteCalendar($calendarId);
            return $response;
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }

    public function openCalendarById($calendarId)
    {
        $calendarId = filter_var($calendarId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarById($calendarId);
        return $calendar;
    }

    public function openCalendarByOwnerAndId($ownerId, $calendarId)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendarId = filter_var($calendarId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarByOwnerAndId($ownerId, $calendarId);
        return $calendar;
    }

    public function openCalendarDetail($ownerId, $calendarId)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendarId = filter_var($calendarId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarByOwnerAndId($ownerId, $calendarId);
        return $calendar;
    }

    public function openCalendarMonth($ownerId, $calendarDate, $recurringDay)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarMonth($ownerId, $calendarDate, $recurringDay);
        return $calendar;
    }

    public function openCalendarDay($ownerId, $calendarDate, $recurringDay)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        $calendar = $this->calendars_gateway->getCalendarDay($ownerId, $calendarDate, $recurringDay);
        return $calendar;
    }

}