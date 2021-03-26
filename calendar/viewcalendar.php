<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../calendar/viewcalendar.php
** Authors: Ceam / Fullo
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewcalendar.php
**
** DESC: screen: view main calendar page
**
** HISTORY:
**  2003-10-23  -   added new document info
**  15/09/2004  -   added broadcast support and subtask view
**  10/11/2004  -   fixed http://www.php-collab.org/community/viewtopic.php?t=1697
**  04/12/2004  -   fixed [ 1077236 ] Calendar bug in Client's Project site
**  21/04/2005  -   added css to calendar events
**  25/04/2006  -   replaced JavaScript Calendar functions with new DHTML calendar files.
** -----------------------------------------------------------------------------
** TO-DO:
**  add the iCal format import
**  add vCalendar format import
**  add more view (weekly, daily)
**  check for better calendar engine (PHPiCal ?)
** =============================================================================
*/


use phpCollab\Util;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$calendars = $container->getCalendarLoader();
$tasks = $container->getTasksLoader();
$detailCalendar = null;

$id = $request->query->get('id');
$type = $request->query->get('type');
$dateCalend = $request->query->get('dateCalend');

if (empty($type)) {
    $type = "monthPreview";
}

if ($gmtTimezone != (int)"false") {
    $zone = 3600 * $session->get('timezone');
    $year = gmdate("Y", time() + $zone);
    $month = gmdate("n", time() + $zone);
    $day = gmdate("j", time() + $zone);
} else {
    $year = date("Y");
    $month = date("n");
    $day = date("j");
}

if (strlen($month) == 1) {
    $month = "0$month";
}
if (strlen($day) == 1) {
    $day = "0$day";
}

$dateToday = "$year-$month-$day";

if ($dateCalend != "") {
    $year = substr("$dateCalend", 0, 4);
    $month = substr("$dateCalend", 5, 2);
    $day = substr("$dateCalend", 8, 2);
}

if ($dateCalend == "") {
    if ($gmtTimezone != (int)"false") {
        $zone = 3600 * $session->get('timezone');
        $year = gmdate("Y", time() + $zone);
        $month = gmdate("n", time() + $zone);
        $day = gmdate("d", time() + $zone);
    } else {
        $year = date("Y");
        $month = date("n");
        $day = date("d");
    }

    if (strlen($day) == 1) {
        $day = "0$day";
    }

    if (strlen($month) == 1) {
        $month = "0$month";
    }

    $dateCalend = "$year-$month-$day";
}

$yearDay = date("Y");
$monthDay = date("n");
$dayDay = date("d");

$dayName = date("w", mktime(0, 0, 0, $month, $day, $year));
$monthName = date("n", mktime(0, 0, 0, $month, $day, $year));

$dayName = ($dayName != "0") ? $dayName : 7;

$dayName = $dayNameArray[$dayName];
$monthName = $monthNameArray[$monthName];

$daysmonth = date("t", mktime(0, 0, 0, $month, $day, $year));
$firstday = date("w", mktime(0, 0, 0, $month, 1, $year));
$padmonth = date("m", mktime(0, 0, 0, $month, $day, $year));
$padday = date("d", mktime(0, 0, 0, $month, $day, $year));

if ($firstday == 0) {
    $firstday = 7;
}

if ($type == "calendEdit") {

    if ($request->isMethod('post')) {
        try {

            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                $subject = $request->request->get("subject");
                $description = $request->request->get("description");
                $location = $request->request->get("location");
                $shortname = $request->request->get("shortname");
                $dateStart = $request->request->get("dateStart");
                $dateEnd = $request->request->get("dateEnd");
                $timeStart = $request->request->get("time_start");
                $timeEnd = $request->request->get("time_end");
                $reminder = $request->request->get("reminder");
                $broadcast = $request->request->get("broadcast");
                $recurring = $request->request->get("recurring");

                if ($request->query->get("action") == "update") {
                    if ($recurring == "") {
                        $recurring = "0";
                    } else {
                        $dateStart_A = substr("$dateStart", 0, 4);
                        $dateStart_M = substr("$dateStart", 5, 2);
                        $dateStart_J = substr("$dateStart", 8, 2);
                        $dayRecurr = Util::dayOfWeek(mktime(12, 12, 12, $dateStart_M, $dateStart_J, $dateStart_A));
                    }
                    $subject = phpCollab\Util::convertData($subject);
                    $description = phpCollab\Util::convertData($description);
                    $dayRecurr = ($dayRecurr != 0) ? $dayRecurr : 0;

                    $calendars->editCalendarEvent(
                        $id, $subject, $description, $location, $shortname, $dateStart, $dateEnd, $timeStart, $timeEnd,
                        $reminder, $broadcast, $recurring, $dayRecurr
                    );

                    phpCollab\Util::headerFunction("../calendar/viewcalendar.php?id=$id&dateCalend=$dateCalend&type=calendDetail&msg=update");
                }

                if ($request->query->get("action") == "add") {

                    if ($shortname == "") {
                        $error = $strings["blank_fields"];
                    } else {
                        if ($recurring == "") {
                            $recurring = "0";
                        } else {
                            $dateStart_A = substr($dateStart, 0, 4);
                            $dateStart_M = substr($dateStart, 5, 2);
                            $dateStart_J = substr($dateStart, 8, 2);
                            $dayRecurr = phpCollab\Util::dayOfWeek(mktime(12, 12, 12, $dateStart_M, $dateStart_J,
                                $dateStart_A));
                        }

                        $dayRecurr = (empty($dayRecurr)) ? '0' : $dayRecurr;

                        $subject = phpCollab\Util::convertData($subject);
                        $description = phpCollab\Util::convertData($description);
                        $shortname = phpCollab\Util::convertData($shortname);

                        $num = $calendars->addCalendarEvent(
                            $session->get("id"), $subject, $description, $location, $shortname, $dateStart,
                            $dateEnd, $timeStart, $timeEnd,
                            $reminder, $broadcast, $recurring, $dayRecurr);

                        phpCollab\Util::headerFunction("../calendar/viewcalendar.php?id={$num}&dateCalend={$dateCalend}&type=calendDetail&msg=add");
                    }
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Calendar: View' => $request->request->get("id"),
                '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }
    }

    if ($id != "") {
        $detailCalendar = $calendars->openCalendarByOwnerAndId($session->get("id"), $id);

        $comptDetailCalendar = count($detailCalendar);

        if ($comptDetailCalendar == "0") {
            phpCollab\Util::headerFunction("../calendar/viewcalendar.php");
        }
    }
}

if ($type == "calendDetail") {
    $detailCalendar = $calendars->openCalendarDetail($session->get("id"), $id);
    $comptDetailCalendar = count($detailCalendar);

    if ($comptDetailCalendar == "0") {
        phpCollab\Util::headerFunction("../calendar/viewcalendar.php");
    }
}

if ($type == "calendEdit") {
    $bodyCommand = 'onLoad="document.calendForm.shortname.focus()"';
}

/** Do the title calcs here.. we __HAVE__ to do it before the include **/
switch ($type) {
    case 'monthPreview':
        $setTitle .= " : View Calendar ($monthName $year)";
        break;
    case 'dayList':
        $setTitle .= " : View Calendar ($dateCalend)";
        break;
    case 'calendEdit':
        if ($id == "") {
            $setTitle .= " : Add Calendar Entry ($dateCalend)";
        }
        if ($id != "") {
            $setTitle .= " : Edit Calendar Entry ($dateCalend - " . $detailCalendar['cal_shortname'] . ")";
        }
        break;
    case 'calendDetail':
        $setTitle .= " : Calendar Entry (" . $detailCalendar['cal_shortname'] . ")";
        break;
}
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include APP_ROOT . '/views/layout/header.php';

if ($type == "calendEdit") {
    if ($id != "") {
        $subject = $detailCalendar['cal_subject'];
        $description = $detailCalendar['cal_description'];
        $location = $detailCalendar['cal_location'];
        $shortname = $detailCalendar['cal_shortname'];
        $date_start = $detailCalendar['cal_date_start'];
        $date_end = $detailCalendar['cal_date_end'];
        $time_start = $detailCalendar['cal_time_start'];
        $time_end = $detailCalendar['cal_time_end'];
        $reminder = $detailCalendar['cal_reminder'];
        $broadcast = $detailCalendar['cal_broadcast'];
        $recurring = $detailCalendar['cal_recurring'];

        if ($reminder == "0") {
            $checked1_b = "checked"; //false
        } else {
            $checked2_b = "checked"; //true
        }

        if ($broadcast == "0") {
            $checked3_b = "checked"; //false
        } else {
            $checked4_b = "checked"; //true
        }
        if ($recurring == "1") {
            $checked2_a = "checked"; //true
        }
    } else {
        $checked2_b = "checked"; //true
        $checked3_b = "checked";
        $checked2_a = "";
    }

    $blockPage = new phpCollab\Block();
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview",
        $strings["calendar"], 'in'));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview&dateCalend=$dateCalend",
        "$monthName $year", 'in'));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=dayList&dateCalend=$dateCalend",
        "$dayName $day $monthName $year", 'in'));

    if ($id != "") {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=calendDetail&dateCalend=$dateCalend&id=$id",
            $detailCalendar['cal_shortname'], 'in'));
        $blockPage->itemBreadcrumbs($strings["edit"]);
    } else {
        $blockPage->itemBreadcrumbs($strings["add"]);
    }
    $blockPage->closeBreadcrumbs();

    if ($msg != "") {
        include '../includes/messages.php';
        $blockPage->messageBox($msgLabel);
    }

    $block1 = new phpCollab\Block();

    $block1->form = "calend";

    if ($id != "") {
        $block1->openForm("../calendar/viewcalendar.php?&id=$id&dateCalend=$dateCalend&type=$type&action=update#" . $block1->form . "Anchor",
            null, $csrfHandler);
    } else {
        $block1->openForm("../calendar/viewcalendar.php?&id=$id&dateCalend=$dateCalend&type=$type&action=add#" . $block1->form . "Anchor",
            null, $csrfHandler);
    }

    if (!empty($error)) {
        $block1->headingError($strings["errors"]);
        $block1->contentError($error);
    }

    if ($id != "") {
        $block1->heading($strings["edit"] . ": " . $detailCalendar['cal_shortname']);
    } else {
        $block1->heading($strings["add"] . ":");
    }

    $block1->openContent();
    $block1->contentTitle($strings["details"]);

    $shortName = $strings["shortname"] . $block1->printHelp("calendar_shortname");
    echo <<<HTML
        <tr class="odd">
            <td class="leftvalue">* {$shortName} :</td>
            <td><input size="24" style="width: 250px;" maxlength="128" type="text" name="shortname" value="{$shortname}"></td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["subject"]} :</td>
            <td><input size="24" style="width: 250px;" maxlength="128" type="text" name="subject" value="{$subject}"></td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["description"]} :</td>
            <td><textarea style="width: 400px; height: 50px;" name="description" cols="35" rows="2">{$description}</textarea></td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["location"]} :</td>
            <td><input size="24" style="width: 250px;" maxlength="128" type="text" name="location" value="{$location}"></td>
        </tr>
HTML;

    if (empty($date_start)) {
        $date_start = $dateCalend;
    }
    if (empty($date_end)) {
        $date_end = $dateCalend;
    }

    $block1->contentRow($strings["date_start"],
        "<input type='text' name='dateStart' id='dateStart' size='20' value='$date_start'><input type='button' value=' ... ' id=\"trigDateStart\">");
    echo <<<SCRIPT
	<script type='text/javascript'>
	    Calendar.setup({
        	inputField     :    'dateStart',
        	button         :    'trigDateStart',
        	{$calendar_common_settings}
	    })
	</script>
SCRIPT;
    $block1->contentRow($strings["date_end"],
        "<input type='text' name='dateEnd' id='dateEnd' size='20' value='$date_end'><input type='button' value=' ... ' id=\"trigDateEnd\">");
    echo <<<SCRIPT
	<script type='text/javascript'>
	    Calendar.setup({
        	inputField     :    'dateEnd',
	        button         :    'trigDateEnd',
        	{$calendar_common_settings}
    	})
	</script>
SCRIPT;

    $time_start = isset($time_start) ? $time_start : '';
    $time_end = isset($time_end) ? $time_end : '';
    echo <<<HTML
          </td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["time_start"]} :</td>
            <td><input size="24" style="width: 250px;" maxlength="128" type="text" name="time_start" value="{$time_start}"></td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["time_end"]} :</td>
            <td><input size="24" style="width: 250px;" maxlength="128" type="text" name="time_end" value="{$time_end}"></td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["calendar_reminder"]} :</td>
            <td><input type="radio" name="reminder" value="0" {$checked1_b}> {$strings["no"]}&nbsp;<input type="radio" name="reminder" value="1" {$checked2_b}> {$strings["yes"]}</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["calendar_broadcast"]} :</td>
            <td><input type="radio" name="broadcast" value="0" {$checked3_b}> {$strings["no"]}&nbsp;<input type="radio" name="broadcast" value="1" {$checked4_b}> {$strings["yes"]}</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["calendar_recurring"]} :</td>
            <td><input type="checkbox" name="recurring" value="1" {$checked2_a}></td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">&nbsp;</td>
            <td><input type="SUBMIT" value="{$strings["save"]}"></td>
        </tr>
HTML;

    $block1->closeContent();
    $block1->closeForm();
}

if ($type == "calendDetail") {
    $reminder = $detailCalendar['cal_reminder'];
    $broadcast = $detailCalendar['cal_broadcast'];
    $recurring = $detailCalendar['cal_recurring'];

    if ($reminder == "0") {
        $reminder = $strings["no"];
    } else {
        $reminder = $strings["yes"];
    }

    if ($broadcast == "0") {
        $broadcast = $strings["no"];
    } else {
        $broadcast = $strings["yes"];
    }

    if ($recurring == "0") {
        $recurring = $strings["no"];
    } else {
        $recurring = $strings["yes"];
    }

    $blockPage = new phpCollab\Block();
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview",
        $strings["calendar"], 'in'));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview&dateCalend=$dateCalend",
        "$monthName $year", 'in'));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=dayList&dateCalend=$dateCalend",
        "$dayName $day $monthName $year", 'in'));
    $blockPage->itemBreadcrumbs($detailCalendar['cal_shortname']);
    $blockPage->closeBreadcrumbs();

    if ($msg != "") {
        include '../includes/messages.php';
        $blockPage->messageBox($msgLabel);
    }

    $block1 = new phpCollab\Block();

    $block1->form = "calend";
    $block1->openForm("../calendar/viewcalendar.php#" . $block1->form . "Anchor", null, $csrfHandler);

    if (!empty($error)) {
        $block1->headingError($strings["errors"]);
        $block1->contentError($error);
    }

    $block1->heading($detailCalendar['cal_shortname']);

    $block1->openPaletteIcon();

    //not sure about this...
    if ($detailCalendar['cal_owner'] == $session->get("id")) {
        $block1->paletteIcon(0, "remove", $strings["delete"]);
        $block1->paletteIcon(1, "edit", $strings["edit"]);
    }
    $block1->paletteIcon(2, "export", $strings["export"]);
    $block1->closePaletteIcon();

    $block1->openContent();
    $block1->contentTitle($strings["details"]);


    $nl2brDescription = nl2br($detailCalendar["cal_description"]);
    $shortName = $strings["shortname"] . $block1->printHelp("calendar_shortname");

    echo <<<HTML
        <tr class="odd">
            <td class="leftvalue">{$strings["subject"]} :</td>
            <td>{$detailCalendar["cal_subject"]}</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["description"]} :</td>
            <td>{$nl2brDescription}&nbsp;</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$shortName} :</td>
            <td>{$detailCalendar["cal_shortname"]}&nbsp;</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["location"]} :</td>
            <td>{$detailCalendar["cal_location"]}&nbsp;</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["date_start"]} :</td>
            <td>{$detailCalendar["cal_date_start"]}</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["date_end"]} :</td>
            <td>{$detailCalendar["cal_date_end"]}</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["time_start"]} :</td>
            <td>{$detailCalendar["cal_time_start"]}</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["time_end"]} :</td>
            <td>{$detailCalendar["cal_time_end"]}</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["calendar_reminder"]} :</td>
            <td>{$reminder}</td>
        </tr>
        <tr class="odd">
            <td class="leftvalue">{$strings["calendar_broadcast"]} :</td>
            <td>{$broadcast}</td>
        </tr>
        <tr class="odd">
        <td class="leftvalue">{$strings["calendar_recurring"]} :</td>
            <td>{$recurring}</td>
        </tr>
HTML;

    $block1->closeContent();
    $block1->closeForm();

    $block1->openPaletteScript();
    if ($detailCalendar['cal_owner'] == $session->get("id")) {
        $block1->paletteScript(0, "remove", "../calendar/deletecalendar.php?id=$id", "true,true,true",
            $strings["delete"]);
        $block1->paletteScript(1, "edit", "../calendar/viewcalendar.php?id=$id&type=calendEdit&dateCalend=$dateCalend",
            "true,true,true", $strings["edit"]);
    }

    $block1->paletteScript(2, "export", "../calendar/exportcalendar.php?id=$id", "true,true,true", $strings["export"]);
    $block1->closePaletteScript("", []);
}

$blockPage = new phpCollab\Block();

if ($type == "dayList") {
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview",
        $strings["calendar"], 'in'));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview&dateCalend=$dateCalend",
        "$monthName $year", 'in'));
    $blockPage->itemBreadcrumbs("$dayName $day $monthName $year");
    $blockPage->closeBreadcrumbs();

    $block1 = new phpCollab\Block();

    $block1->form = "calendList";
    $block1->openForm("../calendar/viewcalendar.php?type=$type&dateCalend=$dateCalend&#" . $block1->form . "Anchor",
        null, $csrfHandler);

    $block1->heading("$dayName $day $monthName $year");

    $block1->openPaletteIcon();

    $block1->paletteIcon(0, "add", $strings["add"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);
    $block1->paletteIcon(2, "info", $strings["view"]);
    $block1->paletteIcon(3, "edit", $strings["edit"]);

    $block1->closePaletteIcon();

    $block1->sorting("calendar", $sortingUser["calendar"], "cal.date_end DESC",
        $sortingFields = [0 => "cal.shortname", 1 => "cal.subject", 2 => "cal.date_start", 3 => "cal.date_end"]);

    $dayRecurr = phpCollab\Util::dayOfWeek(mktime(12, 12, 12, $month, $day, $year));

    $listCalendar = $calendars->openCalendarDay($session->get("id"), $dateCalend, $dayRecurr, $block1->sortingValue);
    $comptListCalendar = count($listCalendar);

    if ($comptListCalendar != "0") {
        $block1->openResults();
        $block1->labels($labels = [
            0 => $strings["shortname"],
            1 => $strings["subject"],
            2 => $strings["date_start"],
            3 => $strings["date_end"]
        ], "false");

        foreach ($listCalendar as $item) {
            $block1->openRow();
            $block1->checkboxRow($item['cal_id']);
            $block1->cellRow($blockPage->buildLink("../calendar/viewcalendar.php?id=" . $item['cal_id'] . "&type=calendDetail&dateCalend=$dateCalend",
                $item['cal_shortname'], 'in'));
            $block1->cellRow($item['cal_subject']);
            $block1->cellRow($item['cal_date_start']);
            $block1->cellRow($item['cal_date_end']);
            $block1->closeRow();
        }

        $block1->closeResults();
    } else {
        $block1->noresults();
    }

    $block1->closeFormResults();

    $block1->openPaletteScript();
    $block1->paletteScript(0, "add", "../calendar/viewcalendar.php?dateCalend=$dateCalend&type=calendEdit",
        "true,false,false", $strings["add"]);
    $block1->paletteScript(1, "remove", "../calendar/deletecalendar.php?", "false,true,true", $strings["delete"]);
    $block1->paletteScript(2, "info", "../calendar/viewcalendar.php?dateCalend=$dateCalend&type=calendDetail",
        "false,true,false", $strings["view"]);
    $block1->paletteScript(3, "edit", "../calendar/viewcalendar.php?dateCalend=$dateCalend&type=calendEdit",
        "false,true,false", $strings["edit"]);
    $block1->closePaletteScript($comptListCalendar, array_column($listCalendar, 'cal_id'));
}

if ($type == "monthPreview") {
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?", $strings["calendar"], 'in'));
    $blockPage->itemBreadcrumbs("$monthName $year");
    $blockPage->closeBreadcrumbs();

    if ($msg != "") {
        include '../includes/messages.php';
        $blockPage->messageBox($msgLabel);
    }

    $block2 = new phpCollab\Block();

    $block2->heading("$monthName $year");

    echo '<table class="monthPreview listing"><tr>';
    for ($daynumber = 1; $daynumber < 8; $daynumber++) {
        echo "<th class='calendDays'>&nbsp;$dayNameArray[$daynumber]</th>";
    }
    echo "</tr>";

    //  Print the calendar
    echo "<tr>";

    $listTasks = $tasks->getMyTasks($session->get("id"), 'tas.name');
    $comptListTasks = count($listTasks);

    $listSubtasks = $tasks->getSubtasksAssignedToMe($session->get("id"));
    $comptListSubtasks = count($listSubtasks);

    $comptListCalendarScan = "0";
    foreach ($listTasks as $task) {
        if (substr($task['tas_start_date'], 0, 7) == substr($dateCalend, 0, 7)) {
            $gantt = "true";
        }
    }

    //hack to check the td
    $weekremain = ($daysmonth - (7 - ($firstday - 1)));
    $daysremain = ($weekremain - (floor($weekremain / 7)) * 7);
    $colsremain = ((7 - $daysremain));

    for ($i = 1; $i < $daysmonth + $firstday; $i++) {
        $a = $i - $firstday + 1;
        $day = $i - $firstday + 1;

        if (strlen($a) == 1) {
            $a = "0$a";
        }

        if (strlen($month) == 1) {
            $month = "0$month";
        }

        $dateLink = "$year-$month-$a";
        $todayClass = "";
        $dayRecurr = phpCollab\Util::dayOfWeek(mktime(12, 12, 12, $month, $a, $year));

        $listCalendarScan = $calendars->openCalendarMonth($session->get("id"), $dateLink, $dayRecurr);

        $comptListCalendarScan = count($listCalendarScan);

        if (($i < $firstday) || ($a == "00")) {
            echo "<td class='even'>&nbsp;</td>";
        } else {
            if ($dateLink == $dateToday) {
                $classCell = "old";
            } else {
                $classCell = "odd";
            }

            echo "<td class='$classCell'><div style='text-align: right;'>" . $blockPage->buildLink("../calendar/viewcalendar.php?dateCalend=$dateLink&type=dayList",
                    $day, 'in') . "</div>";
            if ($comptListCalendarScan != "0") {
                foreach ($listCalendarScan as $calendar) {
                    if ($calendar['cal_broadcast'] == "0" && $calendar['cal_owner'] == $session->get("id")) {
                        echo "<div class='calendar-regular-event'><a href='../calendar/viewcalendar.php?id=" . $calendar['cal_id'] . "&type=calendDetail&dateCalend=$dateLink' class='calendar-regular-todo-event'>" . $calendar['cal_shortname'] . "</a></div>";
                    } elseif ($calendar['cal_broadcast'] != "0" && $calendar['cal_owner'] == $session->get("id")) {
                        echo "<div class='calendar-regular-event'><a href='../calendar/viewcalendar.php?id=" . $calendar['cal_id'] . "&type=calendDetail&dateCalend=$dateLink' class='calendar-regular-todo-event'><b>" . $calendar['cal_shortname'] . "</b></a></div>";
                    } else {
                        echo "<div class='calendar-broadcast-event'><a href='../calendar/viewcalendar.php?id=" . $calendar['cal_id'] . "&type=calendDetail&dateCalend=$dateLink' class='calendar-broadcast-todo-event'><b>" . $calendar['cal_shortname'] . "</b></a></div>";
                    }
                }
            }

            if ($comptListTasks != "0") {
                foreach ($listTasks as $task) {
                    $idPriority = $task['tas_priority'];

                    if ($task['tas_status'] == "3" || $task['tas_status'] == "2") {
                        if ($task['tas_start_date'] == $dateLink && $task['tas_start_date'] != $task['tas_due_date']) {
                            echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["task"] . "</b>: ";
                            echo "<a href='../tasks/viewtask.php?id=" . $task['tas_id'] . "' class='calendar-results-start-date'>" . $task['tas_name'] . "</a><br /><br />";
                        }

                        if ($task['tas_due_date'] == $dateLink && $task['tas_start_date'] != $task['tas_due_date']) {
                            if ($task['tas_due_date'] <= $date && $task['tas_completion'] != "10") {
                                echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["task"] . "</b>: ";
                                echo "<a href='../tasks/viewtask.php?id=" . $task['tas_id'] . "' class='calendar-results-due-date'><b>" . $task['tas_name'] . "</b></a><br /><br />";
                            } else {
                                echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["task"] . "</b>: ";
                                echo "<a href='../tasks/viewtask.php?id=" . $task['tas_id'] . "' class='calendar-results-due-date'>" . $task['tas_name'] . "</a><br /><br />";
                            }
                        }

                        if ($task['tas_start_date'] == $dateLink && $task['tas_due_date'] == $dateLink) {
                            if ($task['tas_due_date'] <= $date && $task['tas_completion'] != "10") {
                                echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["task"] . "</b>: ";
                                echo "<a href='../tasks/viewtask.php?id=" . $task['tas_id'] . "' class='calendar-results-due-date'><b>" . $task['tas_name'] . "</b></a><br /><br />";
                            } else {
                                echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["task"] . "</b>: ";
                                echo "<a href='../tasks/viewtask.php?id=" . $task['tas_id'] . "' class='calendar-results-due-date'>" . $task['tas_name'] . "</a><br /><br />";
                            }
                        }
                    } else {
                        if ($task['tas_start_date'] == $dateLink && $task['tas_start_date'] != $task['tas_due_date']) {
                            echo $blockPage->buildLink("../tasks/viewtask.php?id=" . $task['tas_id'], $task['tas_name'],
                                    'in') . " (" . $strings["start_date"] . ")<br/>";
                        }

                        if ($task['tas_due_date'] == $dateLink && $task['tas_start_date'] != $task['tas_due_date']) {
                            if ($task['tas_due_date'] <= $date && $task['tas_completion'] != "10") {
                                echo $blockPage->buildLink("../tasks/viewtask.php?id=" . $task['tas_id'],
                                        "<b>" . $task['tas_name'] . "</b>",
                                        'in') . " (" . $strings["due_date"] . ")<br/>";
                            } else {
                                echo $blockPage->buildLink("../tasks/viewtask.php?id=" . $task['tas_id'],
                                        $task['tas_name'], 'in') . " (" . $strings["due_date"] . ")<br/>";
                            }
                        }

                        if ($task['tas_start_date'] == $dateLink && $task['tas_due_date'] == $dateLink) {
                            if ($task['tas_due_date'] <= $date && $task['tas_completion'] != "10") {
                                echo $blockPage->buildLink("../tasks/viewtask.php?id=" . $task['tas_id'],
                                        "<b>" . $task['tas_name'] . "</b>", 'in') . "<br/>";
                            } else {
                                echo $blockPage->buildLink("../tasks/viewtask.php?id=" . $task['tas_id'],
                                        $task['tas_name'], 'in') . "<br/>";
                            }
                        }
                    }
                }
            }

            if ($comptListSubtasks != "0") {
                foreach ($listSubtasks as $subtask) {
                    $idPriority = $subtask['subtas_priority'];

                    if ($subtask['subtas_status'] == "3" || $subtask['subtas_status'] == "2") {
                        if ($subtask['subtas_start_date'] == $dateLink && $subtask['subtas_start_date'] != $subtask['subtas_due_date']) {
                            echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["subtask"] . "</b>: ";
                            echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "' class='calendar-results-start-date'>" . $subtask['subtas_name'] . "</a><br /><br />";
                        }

                        if ($subtask['subtas_due_date'] == $dateLink && $subtask['subtas_start_date'] != $subtask['subtas_due_date']) {
                            echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["subtask"] . "</b>: ";
                            if ($subtask['subtas_due_date'] <= $date && $subtask['subtas_completion'] != "10") {
                                echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "' class='calendar-results-due-date'><b>" . $subtask['subtas_name'] . "</b></a><br /><br />";
                            } else {
                                echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "' class='calendar-results-due-date'>" . $subtask['subtas_name'] . "</a><br /><br />";
                            }
                        }

                        if ($subtask['subtas_start_date'] == $dateLink && $subtask['subtas_due_date'] == $dateLink) {
                            echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["subtask"] . "</b>: ";

                            if ($subtask['subtas_due_date'] <= $date && $subtask['subtas_completion'] != "10") {
                                echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "' class='calendar-results-due-date'><b>" . $subtask['subtas_name'] . "</b></a><br /><br />";
                            } else {
                                echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "' class='calendar-results-due-date'>" . $subtask['subtas_name'] . "</a><br /><br />";
                            }
                        }
                    } else {
                        if ($subtask['subtas_start_date'] == $dateLink && $subtask['subtas_start_date'] != $subtask['subtas_due_date']) {
                            echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["subtask"] . "</b>: ";
                            echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "'>" . $subtask['subtas_name'] . "</a><br /><br />";
                        }

                        if ($subtask['subtas_due_date'] == $dateLink && $subtask['subtas_start_date'] != $subtask['subtas_due_date']) {
                            echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["subtask"] . "</b>: ";

                            if ($subtask['subtas_due_date'] <= $date && $subtask['subtas_completion'] != "10") {
                                echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "'><b>" . $subtask['subtas_name'] . "</b></a><br /><br />";
                            } else {
                                echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "'>" . $subtask['subtas_name'] . "</a><br /><br />";
                            }
                        }

                        if ($subtask['subtas_start_date'] == $dateLink && $subtask['subtas_due_date'] == $dateLink) {
                            echo "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> <b>" . $strings["subtask"] . "</b>: ";

                            if ($subtask['subtas_due_date'] <= $date && $subtask['subtas_completion'] != "10") {
                                echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "'><b>" . $subtask['subtas_name'] . "</b></a><br /><br />";
                            } else {
                                echo "<a href='../subtasks/viewsubtask.php?id=" . $subtask['subtas_id'] . "&task=" . $subtask['subtas_task'] . "'>" . $subtask['subtas_name'] . "</a><br /><br />";
                            }
                        }
                    }
                }
            }

            if ($comptListTasks == "0" || $comptListSubtasks == "0" || $comptListCalendarScan == "0") {
                echo "<br />";
            }

            echo "</td>";
        }

        if (($i % 7) == "0") {
            echo "</tr>\n";
        }
    }

    if ($colsremain != "7") {
        for ($j = 0; $j < $colsremain; $j++) {
            echo "<td class='even'>&nbsp;</td>\n";
        }
    }

    echo "</tr></table>";

    if ($month == 1) {
        $pyear = $year - 1;
        $pmonth = 12;
    } else {
        $pyear = $year;
        $pmonth = $month - 1;
    }

    if ($month == 12) {
        $nyear = $year + 1;
        $nmonth = 1;
    } else {
        $nyear = $year;
        $nmonth = $month + 1;
    }

    $year = date("Y");
    $month = date("n");
    $day = date("j");
    if (strlen($month) == 1) {
        $month = "0$month";
    }
    if (strlen($pmonth) == 1) {
        $pmonth = "0$pmonth";
    }
    if (strlen($nmonth) == 1) {
        $nmonth = "0$nmonth";
    }
    if (strlen($day) == 1) {
        $day = "0$day";
    }

    $datePast = "$pyear-$pmonth-01";
    $dateNext = "$nyear-$nmonth-01";

    $dateToday = "$year-$month-$day";

    echo <<<HTML
    <table>
        <tr>
            <td class="calend"> </td>
        </tr>
    </table>
      <table class="nonStriped" style="width: 100%;">
            <tr>
                <td nowrap style="text-align: right;" class="footerCell">
HTML;

    echo $blockPage->buildLink("../calendar/viewcalendar.php?dateCalend=$datePast", $strings["previous"],
            'in') . " | " . $blockPage->buildLink("../calendar/viewcalendar.php?dateCalend=$dateToday",
            $strings["today"],
            'in') . " | " . $blockPage->buildLink("../calendar/viewcalendar.php?dateCalend=$dateNext", $strings["next"],
            'in');

    $theme = THEME;
    echo <<<HTML
                </td>
            </tr>
            <tr>
                <td height="5" colspan="2"><img width="1" height="5" src="../themes/{$theme}/images/spacer.gif" alt=""></td>
            </tr>
            </table>
HTML;


    if ($activeJpgraph == "true" && $gantt == "true") {
        echo <<<HTML
			<div id="ganttChart_taskList" class="ganttChart">
				<img src="graphtasks.php?&dateCalend={$dateCalend}" alt=""><br/>
				<span class="listEvenBold">
HTML;
        echo $blockPage->buildLink("http://www.aditus.nu/jpgraph/", "JpGraph", "powered");
        echo <<<HTML
				</span>	
			</div>
HTML;
    }
}

include APP_ROOT . '/views/layout/footer.php';
