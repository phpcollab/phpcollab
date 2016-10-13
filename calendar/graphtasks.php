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


$checkSession = "true";
include '../includes/library.php';

include '../includes/jpgraph/jpgraph.php';
include '../includes/jpgraph/jpgraph_gantt.php';

$dateCalend = substr($dateCalend, 0, 7);

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->Set($strings["calendar"]);
$graph->subtitle->Set($dateCalend);
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

$tmpquery = "WHERE (tas.start_date LIKE '" . $dateCalend . "%' OR tas.due_date LIKE '" . $dateCalend . "%') AND tas.assigned_to = '$idSession' ORDER BY tas.due_date";
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

$graph->Stroke();
?>