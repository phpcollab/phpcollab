<?php

$checkSession = "true";
include '../includes/library.php';

$tasks = new \phpCollab\Tasks\Tasks();
$calendars = new \phpCollab\Calendars\Calendars();
$projects = new \phpCollab\Projects\Projects();

include '../includes/jpgraph/jpgraph.php';
include '../includes/jpgraph/jpgraph_gantt.php';

$strings = $GLOBALS["strings"];
$idSession = $_SESSION["idSession"];
$timezoneSession = $_SESSION["timezoneSession"];

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

    $progress = 0;
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

    $j = 0;
    $progress = 0;
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
        $j++;
    }
} elseif ($_GET['project'] != '') {
    $projectDetail = $projects->getProjectById($_GET["project"]);

    // case of project graph
    $graph->title->Set($strings["project"] . " " . $projectDetail["pro_name"]);
    $graph->subtitle->Set("(" . $strings["created"] . ": " . $projectDetail["pro_created"] . ")");

    $projectDetail["pro_created"] = phpCollab\Util::createDate($projectDetail["pro_created"], $timezoneSession);
    $projectDetail["pro_name"] = str_replace('&quot;', '"', $projectDetail["pro_name"]);
    $projectDetail["pro_name"] = str_replace("&#39;", "'", $projectDetail["pro_name"]);

    $listTasks = $tasks->getTasksByProjectIdWhereStartAndEndAreNotEmptyAndNotPublished($_GET["project"]);

    $progress = 0;
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
}

$graph->Stroke();
