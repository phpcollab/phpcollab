<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../calendar/graphtasks.php
** Authors: Ceam / Fullo
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
** DESC: screen: show ganttgraph for calendar in a blank page
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**
** =============================================================================
*/


use Amenadiel\JpGraph\Graph\GanttGraph;
use Amenadiel\JpGraph\Plot\GanttBar;
use phpCollab\Tasks\Tasks;

$checkSession = "true";
include '../includes/library.php';

$tasks = new Tasks();
$strings = $GLOBALS["strings"];

$dateCalend = substr($dateCalend, 0, 7);

$listTasks2 = $tasks->getTasksByStartDateEndDateAssignedTo($dateCalend, $session->get("idSession"));

try {
    $graph = new GanttGraph();
    $graph->SetBox();
    $graph->SetMarginColor("white");
    $graph->SetColor("white");
    $graph->title->Set($strings["project"] . " " . $projectDetail->pro_name[0]);
    $graph->subtitle->Set("(" . $strings["created"] . ": " . $projectDetail->pro_created[0] . ")");
    $graph->title->SetFont(FF_FONT1);
    $graph->SetColor("white");
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
    $graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
    $graph->scale->week->SetFont(FF_FONT0);
    $graph->scale->year->SetFont(FF_FONT1);

    $taskCount = 0;
    foreach ($listTasks2 as $task) {
        $task["tas_name"] = str_replace('&quot;', '"', $task["tas_name"]);
        $task["tas_name"] = str_replace("&#39;", "'", $task["tas_name"]);

        $dueDate = ($task["tas_due_date"] == '--') ? $task["tas_start_date"] : $task["tas_due_date"];
        $progress = round($task["tas_completion"] / 10, 2);
        $printProgress = $task["tas_completion"] * 10;
        $activity = new GanttBar($taskCount, $task["tas_name"], $task["tas_start_date"], $dueDate);
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
        $taskCount++;
    }
    $graph->Stroke();
} catch (Exception $e) {
    error_log('Error generating JpGraph', 0);
}
