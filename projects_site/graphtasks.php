<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../projects_site/graphtasks.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: graphtasks.php
**
** DESC: Screen: render the calendar or project gantt graph for project_site
**
** HISTORY:
**	21/04/2005	-	added the broadcast visualization
**	20/04/2005	-	added the calendar view
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

$checkSession = "true";
include '../includes/library.php';

$tasks = new \phpCollab\Tasks\Tasks();
$calendars = new \phpCollab\Calendars\Calendars();

include '../includes/jpgraph/jpgraph.php';
include '../includes/jpgraph/jpgraph_gantt.php';

$strings = $GLOBALS["strings"];
$idSession = $_SESSION["idSession"];

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

// case of calendar graph
if ($_GET['dateCalend'] != '') {
    $graph->title->Set($strings["calendar"]);
    $graph->subtitle->Set($dateCalend);

    $dateCalend = substr($dateCalend, 0, 7);

    //add the published task to the graph
    $listTasks = $tasks->getTasksWhereStartDateAndEndDateLikeNotPublishedAndAssignedToUserId($dateCalend, $idSession);

    foreach ($listTasks as $task) {
        $task["tas_name"] = str_replace('&quot;', '"', $task["tas_name"]);
        $task["tas_name"] = str_replace("&#39;", "'", $task["tas_name"]);
        $progress = round($task["tas_completion"] / 10, 2);
        $printProgress = $task["tas_completion"] * 10;
        $activity = new GanttBar($i, $task["tas_name"], $task["tas_start_date"], $task["tas_due_date"]);
        $activity->SetPattern(BAND_LDIAG, "yellow");
        $activity->caption->Set($task["tas_mem_login"] . " (" . $printProgress . "%)");
        $activity->SetFillColor("gray");

        if ($task["tas_priority"] == "4" || $task["tas_priority"] == "5") {
            $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
        } else {
            $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
        }

        $activity->progress->Set($progress);
        $graph->Add($activity);
    }

    $detailCalendar = $calendars->openCalendarByOwnerOrIsBroadcast($idSession);

    foreach ($detailCalendar as $calendar) {
        $calendar["cal_subject"] = str_replace('&quot;', '"', $calendar["cal_subject"] . '(' . $calendar["cal_location"] . ')');
        $calendar["cal_subject"] = str_replace("&#39;", "'", $calendar["cal_subject"]);

        $activity = new GanttBar($i + $j, $calendar["cal_subject"], $calendar["cal_date_start"], $calendar["cal_date_end"]);
        $activity->SetPattern(BAND_LDIAG, "yellow");
        $activity->caption->Set($calendar["cal_mem_name"]);
        $activity->SetFillColor("gray");
        $activity->progress->SetPattern(BAND_SOLID, "#0000BB");

        $activity->progress->Set($progress);
        $graph->Add($activity);
    }
} elseif ($_GET['project'] != '') {
    // case of project graph
    $graph->title->Set($strings["project"] . " " . $projectDetail->pro_name[0]);
    $graph->subtitle->Set("(" . $strings["created"] . ": " . $projectDetail->pro_created[0] . ")");

    $tmpquery = "WHERE pro.id = '" . $project . "'";
    $projectDetail = new phpCollab\Request();
    $projectDetail->openProjects($tmpquery);

    $projectDetail->pro_created[0] = phpCollab\Util::createDate($projectDetail->pro_created[0], $timezoneSession);
    $projectDetail->pro_name[0] = str_replace('&quot;', '"', $projectDetail->pro_name[0]);
    $projectDetail->pro_name[0] = str_replace("&#39;", "'", $projectDetail->pro_name[0]);

    $tmpquery = "WHERE tas.project = '" . $project . "' AND tas.start_date != '--' AND tas.due_date != '--' AND tas.published != '1' ORDER BY tas.due_date";
    $listTasks = new phpCollab\Request();
    $listTasks->openTasks($tmpquery);
    $comptListTasks = count($listTasks->tas_id);

    for ($i = 0; $i < $comptListTasks; $i++) {
        $listTasks->tas_name[$i] = str_replace('&quot;', '"', $listTasks->tas_name[$i]);
        $listTasks->tas_name[$i] = str_replace("&#39;", "'", $listTasks->tas_name[$i]);
        $progress = round($listTasks->tas_completion[$i] / 10, 2);
        $printProgress = $listTasks->tas_completion[$i] * 10;
        $activity = new GanttBar($i, $listTasks->tas_name[$i], $listTasks->tas_start_date[$i], $listTasks->tas_due_date[$i]);
        $activity->SetPattern(BAND_LDIAG, "yellow");
        $activity->caption->Set($listTasks->tas_mem_login[$i] . " (" . $printProgress . "%)");
        $activity->SetFillColor("gray");

        if ($listTasks->tas_priority[$i] == "4" || $listTasks->tas_priority[$i] == "5") {
            $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
        } else {
            $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
        }

        $activity->progress->Set($progress);
        $graph->Add($activity);
    }
}

$graph->Stroke();
