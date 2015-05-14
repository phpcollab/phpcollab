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
include("../includes/library.php");

include("../includes/jpgraph/jpgraph.php");
include("../includes/jpgraph/jpgraph_gantt.php");

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
if ($_GET['dateCalend'] != '')
{
	$graph->title->Set($strings["calendar"]);
	$graph->subtitle->Set($dateCalend);

	$dateCalend = substr($dateCalend,0,7);
	
	//add the published task to the graph
	$tmpquery = "WHERE (tas.start_date LIKE '".$dateCalend."%' OR tas.due_date LIKE '".$dateCalend."%') AND tas.published = '0' AND tas.assigned_to = '$idSession' ORDER BY tas.due_date";
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

	$tmpquery = "WHERE cal.owner = '$idSession'  OR cal.broadcast = '1' "; 
	$detailCalendar = new request();
	$detailCalendar->openCalendar($tmpquery);
	$comptDetailCalendar = count($detailCalendar->cal_id);
		
	for ($j=0;$j < $comptDetailCalendar;$j++) 
	{

		$detailCalendar->cal_subject[$j] = str_replace('&quot;','"',$detailCalendar->cal_subject[$j].'('.$detailCalendar->cal_location[$j].')');
		$detailCalendar->cal_subject[$j] = str_replace("&#39;","'",$detailCalendar->cal_subject[$j]);

		$activity = new GanttBar($i+$j,$detailCalendar->cal_subject[$j],$detailCalendar->cal_date_start[$j],$detailCalendar->cal_date_end[$j]);
		$activity->SetPattern(BAND_LDIAG,"yellow");
		$activity->caption->Set($detailCalendar->cal_mem_name[$j]); 
		$activity->SetFillColor("gray");
		$activity->progress->SetPattern(BAND_SOLID,"#0000BB");

		$activity->progress->Set($progress);
		$graph->Add($activity);
	}
}

elseif ($_GET['project'] != '')
{ 
	// case of project graph
	$graph->title->Set($strings["project"]." ".$projectDetail->pro_name[0]);
	$graph->subtitle->Set("(".$strings["created"].": ".$projectDetail->pro_created[0].")");
	
	$tmpquery = "WHERE pro.id = '".$project."'";
	$projectDetail = new request();
	$projectDetail->openProjects($tmpquery);

	$projectDetail->pro_created[0] = Util::createDate($projectDetail->pro_created[0],$timezoneSession);
	$projectDetail->pro_name[0] = str_replace('&quot;','"',$projectDetail->pro_name[0]);
	$projectDetail->pro_name[0] = str_replace("&#39;","'",$projectDetail->pro_name[0]);

	$tmpquery = "WHERE tas.project = '".$project."' AND tas.start_date != '--' AND tas.due_date != '--' AND tas.published != '1' ORDER BY tas.due_date";
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
}

$graph->Stroke();
?>