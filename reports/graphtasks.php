<?php
/*
** Application name: phpCollab
** Last Edit page: 23/03/2004
** Path by root: ../reports/graphtasks.php
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
** DESC: 
**
** HISTORY:
** 	23/03/2004	-	added new document info
**  23/03/2004  -	new export to PDF by Angel
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/
$checkSession = "true";
include '../includes/library.php';

include ("../includes/jpgraph/jpgraph.php");
include ("../includes/jpgraph/jpgraph_gantt.php");

$tmpquery = "WHERE id = '".$report."'";
$reportDetail = new phpCollab\Request();
$reportDetail->openReports($tmpquery);
$S_ORGSEL = $reportDetail->rep_clients[0];
$S_PRJSEL = $reportDetail->rep_projects[0];
$S_ATSEL = $reportDetail->rep_members[0];
$S_STATSEL = $reportDetail->rep_status[0];
$S_PRIOSEL = $reportDetail->rep_priorities[0];
$S_SDATE = $reportDetail->rep_date_due_start[0];
$S_EDATE = $reportDetail->rep_date_due_end[0];

if ($S_SDATE == "" && $S_EDATE == "") {
	$S_DUEDATE = "ALL";
}

//echo "$S_PRJSEL + $S_ORGSEL + $S_ATSEL + $S_STATSEL + $S_PRIOSEL + $S_SDATE + $S_EDATE";

if ($S_ORGSEL != "ALL" || $S_PRJSEL != "ALL" || $S_ATSEL != "ALL" || $S_STATSEL != "ALL" || $S_PRIOSEL != "ALL" || $S_DUEDATE != "ALL") 
{
	$queryStart = "WHERE (";

	if ($S_PRJSEL != "ALL" && $S_PRJSEL != "") 
	{
		$query = "tas.project IN($S_PRJSEL)";
	}
	
	if ($S_ORGSEL != "ALL" && $S_ORGSEL != "") 
	{
		if ($query != "") {
			$query .= " AND org.id IN($S_ORGSEL)";
		} else {
			$query .= "org.id IN($S_ORGSEL)";
		}
	}
	
	if ($S_ATSEL != "ALL" && $S_ATSEL != "") 
	{
		if ($query != "") {
			$query .= " AND tas.assigned_to IN($S_ATSEL)";
		} else {
			$query .= "tas.assigned_to IN($S_ATSEL)";
		}
	}
	
	if ($S_STATSEL != "ALL" && $S_STATSEL != "") 
	{
		if ($query != "") {
			$query .= " AND tas.status IN($S_STATSEL)";
		} else {
			$query .= "tas.status IN($S_STATSEL)";
		}
	}
	
	if ($S_PRIOSEL != "ALL" && $S_PRIOSEL != "") 
	{
		if ($query != "") {
			$query .= " AND tas.priority IN($S_PRIOSEL)";
		} else {
			$query .= "tas.priority IN($S_PRIOSEL)";
		}
	}
	
	if ($S_DUEDATE != "ALL" && $S_SDATE != "--") 
	{
		if ($query != "") {
			$query .= " AND tas.due_date >= '$S_SDATE'";
		} else {
			$query .= "tas.due_date >= '$S_SDATE'";
		}
	}
	
	if ($S_DUEDATE != "ALL" && $S_EDATE != "--") 
	{
		if ($query != "") {
			$query .= " AND tas.due_date <= '$S_EDATE'";
		} else {
			$query .= "tas.due_date <= '$S_EDATE'";
	
		}
	}
	
	$query .= ")";
}

$reportDetail->rep_created[0] = phpCollab\Util::createDate($reportDetail->rep_created[0],$timezoneSession);

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->Set($strings["report"]." ".$reportDetail->rep_name[0]);
$graph->subtitle->Set("(".$strings["created"].": ".$reportDetail->rep_created[0].")");
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

$tmpquery = "$queryStart $query ORDER BY tas.name";
$listTasks = new phpCollab\Request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);
$posGantt = 0;

for ($i=0;$i<$comptListTasks;$i++) 
{
	
	$listTasks->tas_name[$i] = str_replace('&quot;','"',$listTasks->tas_name[$i]);
	$listTasks->tas_name[$i] = str_replace("&#39;","'",$listTasks->tas_name[$i]);
	$listTasks->tas_pro_name[$i] = str_replace('&quot;','"',$listTasks->tas_pro_name[$i]);
	$listTasks->tas_pro_name[$i] = str_replace("&#39;","'",$listTasks->tas_pro_name[$i]);
	
	$progress = round($listTasks->tas_completion[$i]/10,2);
	$printProgress = $listTasks->tas_completion[$i]*10;
	$activity = new GanttBar($posGantt,$listTasks->tas_pro_name[$i]." / ".$listTasks->tas_name[$i],$listTasks->tas_start_date[$i],$listTasks->tas_due_date[$i]);
	
	//$activity = new GanttBar($i,$strings["project"].": ".$listTasks->tas_pro_name[$i]." / ".$strings["task"].": ".$listTasks->tas_name[$i],$listTasks->tas_start_date[$i],$listTasks->tas_due_date[$i]);
	$activity->SetPattern(BAND_LDIAG,"yellow");
	$activity->caption->Set($listTasks->tas_mem_login[$i]." (".$printProgress."%)");
	$activity->SetFillColor("gray");

	if ($listTasks->tas_priority[$i] == "4" || $listTasks->tas_priority[$i] == "5") 
	{
		$activity->progress->SetPattern(BAND_SOLID,"#BB0000");
	} else {
		$activity->progress->SetPattern(BAND_SOLID,"#0000BB");
	}

	$activity->progress->Set($progress);
	$graph->Add($activity);
	
	// begin if subtask
	$tmpquery = "WHERE task = ".$listTasks->tas_id[$i];
	$listSubTasks = new phpCollab\Request();
	$listSubTasks->openSubtasks($tmpquery);
	$comptListSubTasks = count($listSubTasks->subtas_id);
	
	if ( $comptListSubTasks >= 1 )
	{
		// list subtasks
		for ($j=0;$j<$comptListSubTasks;$j++) 
		{
			$listSubTasks->subtas_name[$j] = str_replace('&quot;','"',$listSubTasks->subtas_name[$j]);
			$listSubTasks->subtas_name[$j] = str_replace("&#39;","'",$listSubTasks->subtas_name[$j]);
			$progress = round($listSubTasks->subtas_completion[$j]/10,2);
			$printProgress = $listSubTasks->subtas_completion[$j]*10;
			$posGantt += 1;
			// $activity = new GanttBar($posGantt,$listTasks->tas_pro_name[$i]." / ".$listSubTasks->subtas_name[$j],$listSubTasks->subtas_start_date[$j],$listSubTasks->subtas_due_date[$j]);
			// change name of project for name of parent task
			$activity = new GanttBar($posGantt,$listSubTasks->subtas_tas_name[$j]." / ".$listSubTasks->subtas_name[$j],$listSubTasks->subtas_start_date[$j],$listSubTasks->subtas_due_date[$j]);
			//$activity = new GanttBar($j,$strings["project"].": ".$listSubTasks->subtas_pro_name[$j]." / ".$strings["task"].": ".$listSubTasks->subtas_name[$j],$listSubTasks->subtas_start_date[$j],$listSubTasks->subtas_due_date[$j]);
			$activity->SetPattern(BAND_LDIAG,"yellow");
			$activity->caption->Set($listSubTasks->subtas_mem_login[$j]." (".$printProgress."%)");
			$activity->SetFillColor("gray");
			
			if ($listSubTasks->subtas_priority[$j] == "4" || $listSubTasks->subtas_priority[$j] == "5") 
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
	}// end if subtask
	
	// end subtask
	$posGantt += 1;
}

$graph->Stroke();
?>