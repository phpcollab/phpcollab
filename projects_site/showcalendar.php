<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../projects_site/showcalendar.php
** Authors: Fullo / UrbanFalcon
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: showcalendar.php
**
** DESC: screen: view main calendar page
**
** HISTORY:
**	19/04/2005	-	added from http://www.php-collab.org/community/viewtopic.php?p=6915
**	21/04/2005	-	added css to events
**	03/08/2005	-	fix for [ 1241494 ] Broadcasted calendar entrys on project site
**  06/09/2005	-	fix for show other users tasks in calendar
** =============================================================================
**
** TODO
** - a project admin should see all the task on his project
** - a project manager should see all the task on his project
** - can a project team member see the other members tasks? *why not?*
*/

require_once '../includes/library.php';

$tasks = $container->getTasksLoader();
$calendars = $container->getCalendarLoader();

$bouton[12] = "over";
$titlePage = $strings["calendar"];
include 'include_header.php';

if ($type == "") {
    $type = "monthPreview";
}


$year = date("Y");
$month = date("n");
$day = date("j");

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
    $year = date("Y");
    $month = date("n");
    $day = date("d");

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
$dayName = $dayNameArray[$dayName];
$monthName = $monthNameArray[$monthName];

$daysmonth = date("t", mktime(0, 0, 0, $month, $day, $year));
$firstday = date("w", mktime(0, 0, 0, $month, 1, $year));
$padmonth = date("m", mktime(0, 0, 0, $month, $day, $year));
$padday = date("d", mktime(0, 0, 0, $month, $day, $year));

if ($firstday == 0) {
    $firstday = 7;
}

if ($type == "calendDetail") {
    if (empty($dateEnreg) && $id != "") {
        $dateEnreg = $id;
    }

    $detailCalendar = $calendars->getCalendarDetail($session->get("id"), $dateEnreg);

    if (empty($detailCalendar)) {
        header("Location:../projects_site/showcalendar.php");
    }

    $reminder = $detailCalendar["cal_reminder"];
    $broadcast = $detailCalendar["cal_broadcast"];
    $recurring = $detailCalendar["cal_recurring"];

    if ($reminder == 0) {
        $reminder = $strings["no"];
    } else {
        $reminder = $strings["yes"];
    }

    if ($broadcast == 0) {
        $broadcast = $strings["no"];
    } else {
        $broadcast = $strings["yes"];
    }

    if ($recurring == 0) {
        $recurring = $strings["no"];
    } else {
        $recurring = $strings["yes"];
    }

    $block1 = new phpCollab\Block();

    if (isset($error) && !empty($error)) {
        $block1->headingError($strings["errors"]);
        $block1->contentError($error);
    }

    $block1->heading($strings["calendar"] . " " . $strings["details"]);

    echo <<<OPEN_TABLE
    <table style="width: 90%" class="listing striped calendar-detail">
        <tr>
            <th class="active" colspan="2">&nbsp;</th>
OPEN_TABLE;

    foreach ($detailCalendar as $item) {
        if (!($i % 2)) {
            $class = "odd";
        } else {
            $class = "even";
        }

        echo <<<TR
        <tr>
            <td><strong>{$strings["shortname"]}{$block1->printHelp("calendar_shortname")}</strong> :</td>
            <td>{$detailCalendar->cal_shortname[0]}&nbsp;</td>
        </tr>
TR;

        if ($detailCalendar->cal_subject[0] != "") {
            echo <<<TR
            <tr>
                <td><strong>{$strings["subject"]}</strong> :</td>
                <td>{$detailCalendar->cal_subject[0]}</td>
            </tr>
TR;
        } else {
            echo <<<TR
            <tr>
                <td><strong>{$strings["subject"]}</strong> :</td>
                <td>{$strings["none"]}</td>
            </tr>
TR;
        }

        if ($detailCalendar->cal_description[0] != "") {
            echo <<<TR
            <tr>
                <td><strong>{$strings["description"]}</strong> :</td>
                <td>{nl2br($detailCalendar->cal_description[0])}&nbsp;</td>
            </tr>
TR;
        } else {
            echo <<<TR
            <tr>
                <td><strong>{$strings["description"]}</strong> :</td>
                <td>{$strings["none"]}</td>
            </tr>
TR;
        }

        if ($detailCalendar->cal_location[0] == "") {
            echo <<<TR
            <tr>
                <td><strong>{$strings["location"]}</strong> :</td>
                <td>{$strings["none"]}</td>
            </tr>
TR;
        } else {
            echo <<<TR
            <tr>
                <td><strong>{$strings["location"]}</strong> :</td>
                <td>{$detailCalendar->cal_location[0]}</td>
            </tr>
TR;
        }

        echo <<<TR
        <tr>
            <td><strong>{$strings["date_start"]}</strong> :</td>
            <td>{$detailCalendar->cal_date_start[0]}</td>
        </tr>
		<tr>
		    <td><strong>{$strings["date_end"]}</strong> :</td>
            <td>{$detailCalendar->cal_date_end[0]}</td>
        </tr>
		<tr>
		    <td><strong>{$strings["time_start"]}</strong> :</td>
		    <td>
TR;

        if (empty($detailCalendar["cal_time_start"])) {
            echo $strings["none"];
        } else {
            echo $detailCalendar["cal_time_start"];
        }

        echo "</td></tr>";
        echo <<<TR
        <tr>
            <td><strong>{$strings["time_end"]}</strong> :</td>
            <td>
TR;

        if (empty($detailCalendar["cal_time_end"])) {
            echo $strings["none"];
        } else {
            echo $detailCalendar["cal_time_end"];
        }
    }

    echo "</table><hr>";
}

if ($type == "monthPreview") {
    $block2 = new phpCollab\Block();
    $block2->heading("$monthName $year");

    echo "<table style='width: 100%' class='listing nonStriped'><tr>";

    for ($daynumber = 1; $daynumber < 8; $daynumber++) {
        echo "<td style='width: 14%; vertical-align: middle; text-align: center' class='calendDays'>$dayNameArray[$daynumber]</td>";
    }

    echo "</tr></table>";

    //	Print the calendar
    echo "<table class='calendar-blockout-line'><tr>";

    //LIMIT CALENDAR TO CURRENT PROJECT BUT SHOW ALL ASSIGNEES
    $listTasks = $tasks->getTasksByProjectIdAndOwnerOrPublished($session->get("project"), $session->get("id"));

    $listSubtasks = $tasks->getSubTasksByProjectIdAndOwnerOrPublished($session->get("project"), $session->get("id"));

    $comptListCalendarScan = "0";

    foreach ($listTasks as $listTask) {
        if (substr($listTask["tas_start_date"], 0, 7) == substr($dateCalend, 0, 7)) {
            $gantt = "true";
        }
    }

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
        $dayRecurr = $calendars->dayOfWeek(mktime(0, 0, 0, $month, $a, $year));

        $listCalendarScan = $calendars->openCalendarDay($session->get("id"), $dateLink, $dayRecurr);

        if (($i < $firstday) || ($a == "00")) {
            echo "<td  style='width: 14%' class='even'>&nbsp;</td>";
        } else {
            if ($dateLink == $dateToday) {
                $classCell = "today";
            } else {
                $classCell = "odd";
            }

            echo <<<TD
<td class="{$classCell}">
    <div class="calendarDate">$day</div>
TD;

            if (!empty($listCalendarScan)) {
                foreach ($listCalendarScan as $item) {
                    if ($item["cal_broadcast"] == "1") {
                        echo <<<DIV
<div class="calendar-broadcast-event"><a href="showcalendar.php?dateEnreg={$item["cal_id"]}&type=calendDetail&dateCalend={$dateLink}" class="calendar-broadcast-todo-event"><b>{$item["cal_shortname"]}</b></a></div>
DIV;
                    }
                }
            }

            if ($listTasks) {
                foreach ($listTasks as $listTask) {
                    if ($listTask["tas_status"] == "3" || $listTask["tas_status"] == "2") {
                        if ($listTask["tas_start_date"] == $dateLink && $listTask["tas_start_date"] != $listTask["tas_due_date"]) {
                            echo <<<ENTRY
                            <strong>{$strings["task"]}</strong>: 
                            <a href='../projects_site/teamtaskdetail.php?id={$listTask["tas_id"]}' class='calendar-results-start-date'>{$listTask["tas_name"]}</a><br />({$listTask["tas_mem_name"]})<br /><br />
ENTRY;

                        }

                        if ($listTask["tas_due_date"] == $dateLink && $listTask["tas_start_date"] != $listTask["tas_due_date"]) {
                            echo "<b>" . $strings["task"] . "</b>: ";
                            if ($listTask["tas_due_date"] <= $date && $listTask["tas_completion"] != "10") {
                                echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "' class='calendar-results-due-date'><b>" . $listTask["tas_name"] . "</b></a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                            } else {
                                echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "' class='calendar-results-due-date'>" . $listTask["tas_name"] . "</a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                            }
                        }

                        if ($listTask["tas_start_date"] == $dateLink && $listTask["tas_due_date"] == $dateLink) {
                            echo "<b>" . $strings["task"] . "</b>: ";

                            if ($listTask["tas_due_date"] <= $date && $listTask["tas_completion"] != "10") {
                                echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "' class='calendar-results-due-date'><b>" . $listTask["tas_name"] . "</b></a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                            } else {
                                echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "' class='calendar-results-due-date'>" . $listTask["tas_name"] . "</a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                            }
                        }
                    } else {
                        if ($listTask["tas_start_date"] == $dateLink && $listTask["tas_start_date"] != $listTask["tas_due_date"]) {
                            echo "<b>" . $strings["task"] . "</b>: ";
                            echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "'>" . $listTask["tas_name"] . "</a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                        }

                        if ($listTask["tas_due_date"] == $dateLink && $listTask["tas_start_date"] != $listTask["tas_due_date"]) {
                            echo "<b>" . $strings["task"] . "</b>: ";
                            if ($listTask["tas_due_date"] <= $date && $listTask["tas_completion"] != "10") {
                                echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "'><b>" . $listTask["tas_name"] . "</b></a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                            } else {
                                echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "'>" . $listTask["tas_name"] . "</a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                            }
                        }

                        if ($listTask["tas_start_date"] == $dateLink && $listTask["tas_due_date"] == $dateLink) {
                            echo "<b>" . $strings["task"] . "</b>: ";
                            if ($listTask["tas_due_date"] <= $date && $listTask["tas_completion"] != "10") {
                                echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "'><b>" . $listTask["tas_name"] . "</b></a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                            } else {
                                echo "<a href='../projects_site/teamtaskdetail.php?id=" . $listTask["tas_id"] . "'>" . $listTask["tas_name"] . "</a><br />(" . $listTask["tas_mem_name"] . ")<br /><br />";
                            }
                        }
                    }
                }
            }

            if ($listSubtasks) {
                foreach ($listSubtasks as $listSubtask) {
                    if ($listSubtask["subtas_status"] == "3" || $listSubtask["subtas_status"] == "2") {
                        if ($listSubtask["subtas_start_date"] == $dateLink && $listSubtask["subtas_start_date"] != $listSubtask["subtas_due_date"]) {
                            echo "<b>" . $strings["subtask"] . "</b>: ";
                            echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "' class='calendar-results-start-date'>" . $listSubtask["subtas_name"] . "</a><br />(" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                        }

                        if ($listSubtask["subtas_due_date"] == $dateLink && $listSubtask["subtas_start_date"] != $listSubtask["subtas_due_date"]) {
                            echo "<b>" . $strings["subtask"] . "</b>: ";
                            if ($listSubtask["subtas_due_date"] <= $date && $listSubtask["subtas_completion"] != "10") {
                                echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "' class='calendar-results-due-date'><b>" . $listSubtask["subtas_name"] . "</b></a><br />(" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                            } else {
                                echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "' class='calendar-results-due-date'>" . $listSubtask["subtas_name"] . "</a><br />(" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                            }
                        }

                        if ($listSubtask["subtas_start_date"] == $dateLink && $listSubtask["subtas_due_date"] == $dateLink) {
                            echo "<b>" . $strings["subtask"] . "</b>: ";
                            if ($listSubtask["subtas_due_date"] <= $date && $listSubtask["subtas_completion"] != "10") {
                                echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "' class='calendar-results-due-date'><b>" . $listSubtask["subtas_name"] . "</b></a>(<br />" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                            } else {
                                echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "' class='calendar-results-due-date'>" . $listSubtask["subtas_name"] . "</a><br />(" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                            }
                        }
                    } else {
                        if ($listSubtask["subtas_start_date"] == $dateLink && $listSubtask["subtas_start_date"] != $listSubtask["subtas_due_date"]) {
                            echo "<b>" . $strings["subtask"] . "</b>: ";
                            echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "'>" . $listSubtask["subtas_name"] . "</a><br />(" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                        }

                        if ($listSubtask["subtas_due_date"] == $dateLink && $listSubtask["subtas_start_date"] != $listSubtask["subtas_due_date"]) {
                            echo "<b>" . $strings["subtask"] . "</b>: ";
                            if ($listSubtask["subtas_due_date"] <= $date && $listSubtask["subtas_completion"] != "10") {
                                echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "'><b>" . $listSubtask["subtas_name"] . "</b></a>(<br />" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                            } else {
                                echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "'>" . $listSubtask["subtas_name"] . "</a><br />(" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                            }
                        }

                        if ($listSubtask["subtas_start_date"] == $dateLink && $listSubtask["subtas_due_date"] == $dateLink) {
                            echo "<b>" . $strings["subtask"] . "</b>: ";

                            if ($listSubtask["subtas_due_date"] <= $date && $listSubtask["subtas_completion"] != "10") {
                                echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "'><b>" . $listSubtask["subtas_name"] . "</b></a><br />(" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                            } else {
                                echo "<a href='../projects_site/teamsubtaskdetail.php?task=" . $listSubtask["subtas_task"] . "&id=" . $listSubtask["subtas_id"] . "'>" . $listSubtask["subtas_name"] . "</a><br />(" . $listSubtask["subtas_mem_name"] . ")<br /><br />";
                            }
                        }
                    }
                }
            }

            if (count($listTasks) == "0" || count($listSubtasks) == "0" || count($listCalendarScan) == "0") {
                echo "<br />";
            }

            echo "&nbsp;</td>";
        }

        if (($i % 7) == "0") {
            echo "</tr>";
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

    echo <<<PREV_NEXT_LINKS

		<table class="prev-next-table">
		    <tr>
			    <th><a href="showcalendar.php?dateCalend={$datePast}">{$strings["previous"]}</a> | <a href="showcalendar.php?dateCalend={$dateToday}">{$strings["today"]}</a> | <a href="showcalendar.php?dateCalend={$dateNext}">{$strings["next"]}</a></th>
		    </tr>
		</table>
		<br />
PREV_NEXT_LINKS;

    if ($activeJpgraph == "true" && $gantt == "true") {
        $poweredByLink = $block2->buildLink("http://www.aditus.nu/jpgraph/", "JpGraph", "powered");
        echo <<<JpGraph
			<div id="ganttChart_taskList" class="ganttChart">
				<img src="graphtasks.php?dateCalend={$dateCalend}" alt=""><br/>
				<span class="listEvenBold"">{$poweredByLink}</span>	
			</div>
JpGraph;
    }
}

include("include_footer.php");
