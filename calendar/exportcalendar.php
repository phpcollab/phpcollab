<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../calendar/exportcalendar.php
** Authors: Ceam / Fullo
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: exportcalendar.php
**
** DESC: screen: export calendar data to vCALENDAR format
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**	add the iCal format
**
** =============================================================================
*/

$checkSession = "false";
include_once '../includes/library.php';

$calendars = new \phpCollab\Calendars\Calendars();

$detailCalendar = $calendars->openCalendarByOwnerAndId($idSession, $id);

$comptDetailCalendar = count($detailCalendar->cal_id);

if (count($detailCalendar) != "0") {
    $filename = $detailCalendar['cal_subject'] . ".ics";
    header("Content-Type: text/x-iCalendar");
    header("Content-Disposition: attachment; filename=$filename");

    $DescDump = str_replace("\r\n", "\\n", $detailCalendar['cal_description']);

    $vCalStart = str_replace("-", "", $detailCalendar['cal_date_start']);
    $vCalEnd = str_replace("-", "", $detailCalendar['cal_date_end']);
}
echo "BEGIN:VCALENDAR
PRODID:PhpCollab $version
VERSION:2.0
METHOD:PUBLISH
BEGIN:VEVENT
ORGANIZER:MAILTO:" . $detailCalendar['cal_mem_email_work'] . "
DTSTART;VALUE=DATE:$vCalStart
DTEND;VALUE=DATE:$vCalEnd 
TRANSP:OPAQUE
SEQUENCE:0
UID:040000008200E00074C5B7101A82E00800000000A03EAED7766FC2010000000000000000100
 0000056B56C3860D17B448DC0B0DB90B2BEB6
DTSTAMP:20021009T073253Z
DESCRIPTION:" . $DescDump . "
SUMMARY:" . $detailCalendar['cal_subject'] . "
PRIORITY:5
CLASS:PUBLIC\n";
if ($detailCalendar['cal_reminder'] == "1") {
    echo "BEGIN:VALARM
TRIGGER:PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM\n";
}
echo "END:VEVENT
END:VCALENDAR";
