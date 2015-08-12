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
include_once('../includes/library.php');

$tmpquery = "WHERE cal.owner = '$idSession' AND cal.id = '$id'";
$detailCalendar = new Request();
$detailCalendar->openCalendar($tmpquery);
$comptDetailCalendar = count($detailCalendar->cal_id);

//echo "$idSession - $id - $comptDetailCalendar<br/>";

if ($comptDetailCalendar != "0") {
$filename = $detailCalendar->cal_subject[0].".ics";
	header("Content-Type: text/x-iCalendar");
	header("Content-Disposition: attachment; filename=$filename");

//echo $filename;

$DescDump = str_replace("\r\n", "\\n", $detailCalendar->cal_description[0]);

$vCalStart = str_replace("-","",$detailCalendar->cal_date_start[0]);
$vCalEnd = str_replace("-","",$detailCalendar->cal_date_end[0]);
}
echo "BEGIN:VCALENDAR
PRODID:PhpCollab $version
VERSION:2.0
METHOD:PUBLISH
BEGIN:VEVENT
ORGANIZER:MAILTO:".$detailCalendar->cal_mem_email_work[0]."
DTSTART;VALUE=DATE:$vCalStart
DTEND;VALUE=DATE:$vCalEnd 
TRANSP:OPAQUE
SEQUENCE:0
UID:040000008200E00074C5B7101A82E00800000000A03EAED7766FC2010000000000000000100
 0000056B56C3860D17B448DC0B0DB90B2BEB6
DTSTAMP:20021009T073253Z
DESCRIPTION:".$DescDump."
SUMMARY:".$detailCalendar->cal_subject[0]."
PRIORITY:5
CLASS:PUBLIC\n";
if ($detailCalendar->cal_reminder[0] == "1") {
echo "BEGIN:VALARM
TRIGGER:PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM\n";
}
echo "END:VEVENT
END:VCALENDAR";
?>