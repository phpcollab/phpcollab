<?php
/*
** Application name: phpCollab
** Last Edit page: 18/05/2005
** Path by root: ../tasks/graphtasks.php
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
** DESC: Screen: library file to show tasks
**
** HISTORY:
**	26/08/2005	-	added comments/code cleaning
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/


$checkSession = "true";
include("../includes/library.php");

include("../includes/jpgraph/jpgraph.php");
include("../includes/jpgraph/jpgraph_gantt.php");

$tmpquery = "WHERE pro.id = '".$project."'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

$projectDetail->pro_created[0] = createDate($projectDetail->pro_created[0],$timezoneSession);
$projectDetail->pro_name[0] = str_replace('&quot;','"',$projectDetail->pro_name[0]);
$projectDetail->pro_name[0] = str_replace("&#39;","'",$projectDetail->pro_name[0]);

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->Set($strings["project"]." ".$projectDetail->pro_name[0]);
$graph->subtitle->Set("(".$strings["created"].": ".$projectDetail->pro_created[0].")");
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

$tmpquery = "WHERE tas.project = '".$project."' AND tas.start_date != '--' AND tas.due_date != '--' ORDER BY tas.due_date";
$listTasks = new request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

for ($i=0;$i<$comptListTasks;$i++) 
{
	$listTasks->tas_name[$i] = str_replace('&quot;','"',$listTasks->tas_name[$i]);
	$listTasks->tas_name[$i] = str_replace("&#39;","'",$listTasks->tas_name[$i]);
	$progress = round($listTasks->tas_completion[$i]/10,2);
	$printProgress = $listTasks->tas_completion[$i]*10;
	$activity = new GanttBar($i,$listTasks->tas_name[$i],$listTasks->tas_start_date[$i],$listTasks->tas_due_date[$i]);
	$activity->SetPattern(BAND_LDIAG,"yellow");
	$activity->caption->Set($listTasks->tas_mem_login[$i]." (".$printProgress."%)");
	$activity->SetFillColor("gray");

	if ($listTasks->tas_priority[$i] == "4" || $listTasks->tas_priority[$i] == "5") 
	{
		$activity->progress->SetPattern(BAND_SOLID,"#BB0000");
	} 
	else 
	{
		$activity->progress->SetPattern(BAND_SOLID,"#0000BB");
	}

	$activity->progress->Set($progress);
	$graph->Add($activity);
}

$graph->Stroke();
?>