<?php
/*
** Application name: phpCollab
** Last Edit page: 23/03/2004
** Path by root: ../reports/exportreport.php
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
** FILE: exportreport.php
**
** DESC: Screen: team member list
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

// PDF setup
include ('../includes/class.ezpdf.php');
$pdf =& new Cezpdf();
$pdf->selectFont('../includes/fonts/Helvetica.afm');
$pdf -> ezSetMargins(50,70,50,50);

// begin PHPCollab code
$checkSession = "true";
include '../includes/library.php';

// get company info
$tmpquery = "WHERE org.id = '1'";
$clientDetail = new phpCollab\Request();
$clientDetail->openOrganizations($tmpquery);

$cn = $clientDetail->org_name[0];
$add = $clientDetail->org_address1[0];
$wp = $clientDetail->org_phone[0];
$url = $clientDetail->org_url[0];
$email = $clientDetail->org_email[0];
$c = $clientDetail->org_comments[0];

// get task info
if ($id == "" && $tri != "true") 
{
	$compt1 = count($S_PRJSEL);
	$S_pro = "";
	
	for($i=0; $i<$compt1; $i++) 
	{
		if($S_PRJSEL[$i] == "ALL") 
		{
			$S_pro = "ALL";
			break;
		}
		if ($i != $compt1-1) 
		{
			$S_pro .= $S_PRJSEL[$i].",";
		} else {
			$S_pro .= $S_PRJSEL[$i];
		}
	}

	$compt2 = count($S_ATSEL);
	$S_mem = "";
	
	for($i=0; $i<$compt2; $i++) 
	{
		if($S_ATSEL[$i] == "ALL") 
		{
			$S_mem = "ALL";
			break;
		}
		
		if ($i != $compt2-1) 
		{
			$S_mem .= $S_ATSEL[$i].",";
		} else {
			$S_mem .= $S_ATSEL[$i];
		}
	}

	$compt3 = count($S_STATSEL);
	$S_sta = "";
	
	for($i=0; $i<$compt3; $i++) 
	{
		if($S_STATSEL[$i] == "ALL") 
		{
			$S_sta = "ALL";
			break;
		}
		
		if ($i != $compt3-1) 
		{
			$S_sta .= $S_STATSEL[$i].",";
		} else {
			$S_sta .= $S_STATSEL[$i];
		}
	}

	$compt4 = count($S_PRIOSEL);
	$S_pri = "";
	
	for($i=0; $i<$compt4; $i++) 
	{
		if($S_PRIOSEL[$i] == "ALL") {
			$S_pri = "ALL";
			break;
		}
		
		if ($i != $compt4-1) 
		{
			$S_pri .= $S_PRIOSEL[$i].",";
		} else {
			$S_pri .= $S_PRIOSEL[$i];
		}
	}

	$compt5 = count($S_ORGSEL);
	$S_org = "";
	
	for($i=0; $i<$compt5; $i++) 
	{
		if($S_ORGSEL[$i] == "ALL") 
		{
			$S_org = "ALL";
			break;
		}

		if ($i != $compt5-1) 
		{
			$S_org .= $S_ORGSEL[$i].",";
		} else {
			$S_org .= $S_ORGSEL[$i];
		}
	}

	//echo "$S_pro - $S_org - $S_mem - $S_sta - $S_pri";

	$S_ORGSEL = $S_org;
	$S_PRJSEL = $S_pro;
	$S_ATSEL = $S_mem;

	$S_STATSEL = $S_sta;
	$S_PRIOSEL = $S_pri;
}

if ($id != "") {
	
	$tmpquery = "WHERE id = '$id'";
	$reportDetail = new phpCollab\Request();
	$reportDetail->openReports($tmpquery);
	$reportName = $reportDetail->rep_name[0];
	$S_ORGSEL = $reportDetail->rep_clients[0];
	$S_PRJSEL = $reportDetail->rep_projects[0];
	$S_ATSEL = $reportDetail->rep_members[0];
	$S_STATSEL = $reportDetail->rep_status[0];
	$S_PRIOSEL = $reportDetail->rep_priorities[0];
	$S_SDATE = $reportDetail->rep_date_due_start[0];
	$S_EDATE = $reportDetail->rep_date_due_end[0];
	$S_SDATE2 = $reportDetail->rep_date_complete_start[0];
	$S_EDATE2 = $reportDetail->rep_date_complete_end[0];
	
	if ($S_SDATE == "" && $S_EDATE == "") {
		$S_DUEDATE = "ALL";
	}

	if ($S_SDATE2 == "" && $S_EDATE2 == "") {
		$S_COMPLETEDATE = "ALL";
	}
}

//echo "$S_PRJSEL + $S_ORGSEL + $S_ATSEL + $S_STATSEL + $S_PRIOSEL + $S_SDATE + $S_EDATE + $S_SDATE2 + $S_EDATE2";

if ($S_PRJSEL != "ALL" || $S_ORGSEL != "ALL" || $S_ATSEL != "ALL" || $S_STATSEL != "ALL" || $S_PRIOSEL != "ALL" || $S_DUEDATE != "ALL" || $S_COMPLETEDATE != "ALL") 
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

	if ($S_DUEDATE != "ALL" && $S_SDATE != "") 
	{
		if ($query != "") {
			$query .= " AND tas.due_date >= '$S_SDATE'";
		} else {
			$query .= "tas.due_date >= '$S_SDATE'";
		}
	}

	if ($S_DUEDATE != "ALL" && $S_EDATE != "") 
	{
		if ($query != "") {
			$query .= " AND tas.due_date <= '$S_EDATE'";
		} else {
			$query .= "tas.due_date <= '$S_EDATE'";
		}
	}

	if ($S_COMPLETEDATE != "ALL" && $S_SDATE2 != "") 
	{
		if ($query != "") {
			$query .= " AND tas.complete_date >= '$S_SDATE2'";
		} else {
			$query .= "tas.complete_date >= '$S_SDATE2'";
		}
	}

	if ($S_COMPLETEDATE != "ALL" && $S_EDATE2 != "") 
	{
		if ($query != "") {
			$query .= " AND tas.complete_date <= '$S_EDATE2'";
		} else {
			$query .= "tas.complete_date <= '$S_EDATE2'";
		}
	}

	$query .= ")";
}


$blockPage = new phpCollab\Block();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}


$block1 = new phpCollab\Block();

$block1->sorting("report_tasks",$sortingUser->sor_report_tasks[0],"tas.complete_date DESC",$sortingFields = array(0=>"tas.name",1=>"tas.project",2=>"tas.actual_time",3=>"tas.completion",4=>"tas.status",5=>"tas.start_date",6=>"tas.due_date",7=>"tas.complete_date",8=>"mem.login",9=>"tas.description",10=>"tas.comments"));

if ($projectsFilter == "true") 
{
	$tmpquery = "LEFT OUTER JOIN ".$tableCollab["teams"]." teams ON teams.project = pro.id ";
	$tmpquery .= "WHERE pro.status IN(0,2,3) AND teams.member = '$idSession' ORDER BY pro.id";

	$listProjectsTasks = new phpCollab\Request();
	$listProjectsTasks->openProjects($tmpquery);
	$comptListProjectsTasks = count($listProjectsTasks->pro_id);

	if ($comptListProjectsTasks != "0") 
	{
		for ($i=0;$i<$comptListProjectsTasks;$i++)
		{
			$filterTasks .= $listProjectsTasks->pro_id[$i];
			
			if ($comptListProjectsTasks-1 != $i) 
			{
				$filterTasks .= ",";
			}
		}
		
		if ($query != "") 
		{
			$tmpquery = "$queryStart $query AND pro.id IN($filterTasks) ORDER BY {$block1->sortingValue}";
		} else {
			$tmpquery = "WHERE pro.id IN($filterTasks) ORDER BY ".$block1->sortingValue." ";
		}

	} else {
		$validTasks = "false";
	}
} else {
	$tmpquery = "$queryStart $query ORDER BY ".$block1->sortingValue." ";
}


$listTasks = new phpCollab\Request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

$sum=0.0;

// begin PDF code 	

// print the page number
$pdf->ezStartPageNumbers(526,34,6,'right','',1);

// company name at the top of the first page
$pdf->ezText("<b>".$cn."</b>",18,array('justification'=>'center'));

// report name at the top of the first page
$pdf->ezText($strings["report"].": ".$reportName."\n",16,array('justification'=>'center'));

// put a line top and bottom on all the pages and company info on the bottom
$all = $pdf->openObject();
$pdf->saveState();
$pdf->setStrokeColor(0,0,0,1);
$pdf->line(20,40,578,40);
$pdf->line(20,822,578,822);
$pdf->addText(50,34,6,$cn." - ".$url);
$pdf->AddText(510, 34, 6, "Page ");
$pdf->restoreState();
$pdf->closeObject();
$pdf->addObject($all,'all');

// iterate through tasks
for ($i=0;$i<$comptListTasks;$i++) 
{
	$idStatus = $listTasks->tas_status[$i];
	$idPriority = $listTasks->tas_priority[$i];
	$actualTime = str_replace(",",".",$listTasks->tas_actual_time[$i]);
	$sum+=$actualTime;
	
	if ($listTasks->tas_assigned_to[$i] == "0") 
	{
		$idAssigned = $strings["unassigned"];
	} 
	else 
	{
		$idAssigned = $listTasks->tas_mem_login[$i];
	}
	
	// stuff values into an array
	$data = array (
					array ('item'=>$strings["project"],'value'=>$listTasks->tas_pro_name[$i])
					,array ('item'=>$strings["worked_hours"],'value'=>$actualTime)
					,array ('item'=>$strings["Pct_Complete"],'value'=>($listTasks->tas_completion[$i]*10).'%')
					,array ('item'=>$strings["status"],'value'=>$status[$idStatus])
					,array ('item'=>$strings["start_date"],'value'=>$listTasks->tas_start_date[$i])
					,array ('item'=>$strings["due_date"],'value'=>$listTasks->tas_due_date[$i])
					,array ('item'=>$strings["complete_date"],'value'=>$listTasks->tas_complete_date[$i])
					,array ('item'=>$strings["assigned_to"],'value'=>$idAssigned)
					,array ('item'=>$strings["description"],'value'=>$listTasks->tas_description[$i])
					,array ('item'=>$strings["comments"],'value'=>$listTasks->tas_comments[$i])
			);
	
	// set table data and draw table
	$cols = array('item'=>'Item','value'=>'Value');
	$pdf->ezText($strings["task"].": ".$listTasks->tas_name[$i]."\n",12);
	$pdf->saveState(); 
	$pdf->ezTable($data,$cols,'',array('xPos'=>50,'xOrientation'=>'right','width'=>510, 'fontSize'=>10, 'showHeadings'=>0, 'protectRows'=>2,'cols'=>array('item'=>array('width'=>90))));
	$pdf->restoreState();
	$pdf->ezText("\n");
	
	// if subtask
	$tmpquery = "WHERE task = ".$listTasks->tas_id[$i];
	$listSubTasks = new phpCollab\Request();
	$listSubTasks->openSubtasks($tmpquery);
	$comptListSubTasks = count($listSubTasks->subtas_id);
	
	if ( $comptListSubTasks >= 1 )
	{
		// list subtasks
		for ($j=0;$j<$comptListSubTasks;$j++) 
		{
			$idStatus = $listSubTasks->subtas_status[$j];
			$idPriority = $listSubTasks->subtas_priority[$j];
			$actualTime = str_replace(",",".",$listSubTasks->subtas_actual_time[$j]);
			$sum+=$actualTime;
			if ($listSubTasks->subtas_assigned_to[$j] == "0") 
			{
				$idAssigned = $strings["unassigned"];
			}
			else
			{
				$idAssigned = $listSubTasks->subtas_mem_login[$j];
			}
			// stuff values into an array
			$data = array (
					array ('item'=>$strings["project"],'value'=>$listTasks->tas_pro_name[$i])
					,array ('item'=>$strings["worked_hours"],'value'=>$actualTime)
					,array ('item'=>$strings["Pct_Complete"],'value'=>($listSubTasks->subtas_completion[$j]*10).'%')
					,array ('item'=>$strings["status"],'value'=>$status[$idStatus])
					,array ('item'=>$strings["start_date"],'value'=>$listSubTasks->subtas_start_date[$j])
					,array ('item'=>$strings["due_date"],'value'=>$listSubTasks->subtas_due_date[$j])
					,array ('item'=>$strings["complete_date"],'value'=>$listSubTasks->subtas_complete_date[$j])
					,array ('item'=>$strings["assigned_to"],'value'=>$idAssigned)
					,array ('item'=>$strings["description"],'value'=>$listSubTasks->subtas_description[$j])
					,array ('item'=>$strings["comments"],'value'=>$listSubTasks->subtas_comments[$j])
					);
		// set table data and draw table
		$cols = array('item'=>'Item','value'=>'Value');
		$pdf->ezText($strings["task"].": ".$listSubTasks->subtas_name[$j]."\n",12);
		$pdf->saveState(); 
		$pdf->ezTable($data,$cols,'',array('xPos'=>50,'xOrientation'=>'right','width'=>510, 'fontSize'=>10, 'showHeadings'=>0, 'protectRows'=>2,'cols'=>array('item'=>array('width'=>90))));
		$pdf->restoreState();
		$pdf->ezText("\n");
		} // end for complistsubtask
	} // end if subtask

} // close task loop

	// add a grey bar and output the hours worked
	$tmp = $strings["Total_Hours_Worked"].": ".$sum;
	$pdf->transaction('start');
	$ok=0;
	while (!$ok){
			$thisPageNum = $pdf->ezPageCount;
			$pdf->saveState();
			$pdf->setColor(0.9,0.9,0.9);
			$pdf->filledRectangle($pdf->ez['leftMargin'],$pdf->y-$pdf->getFontHeight(12)+$pdf->getFontDecender(12),$pdf->ez['pageWidth']-$pdf->ez['leftMargin']-$pdf->ez['rightMargin'],$pdf->getFontHeight(12));
			$pdf->restoreState();
			$pdf->ezText($tmp,12,array('justification'=>'left'));
			
			if ($pdf->ezPageCount==$thisPageNum){
					$pdf->transaction('commit');
					$ok=1;
			} else {
					// then we have moved onto a new page, bad bad, as the background rectangle will be on the old one
					$pdf->transaction('rewind');
					$pdf->ezNewPage();
			}
	}
// begin include gantt graph in pdf
$pdf->ezText("\n\n");
$graphPDF = ganttPDF($reportName,$listTasks);
$pdf->ezImage( $graphPDF,-5,510,"","left" );
unlink("../files/".$graphPDF);
// end include gantt graph in pdf

// output the PDF
$pdf->ezStream();

function ganttPDF($reportName,$listTasks)
{
    include '../includes/jpgraph/jpgraph.php';
    include '../includes/jpgraph/jpgraph_gantt.php';

    $graph = new GanttGraph();
    $graph->SetBox();
    $graph->SetMarginColor("white");
    $graph->SetColor("white");
    $graph->title->Set($strings["project"]." ".$reportName);
//    $graph->subtitle->Set("(".$strings["created"].": "..")");
    $graph->title->SetFont(FF_FONT1);
    $graph->SetColor("white");
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
    $graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
    $graph->scale->week->SetFont(FF_FONT0);
    $graph->scale->year->SetFont(FF_FONT1);

    $comptListTasks = count($listTasks->tas_id);
	$posGantt = 0;
    
	for ($i=0;$i<$comptListTasks;$i++) 
	{
		$listTasks->tas_name[$i] = str_replace('&quot;','"',$listTasks->tas_name[$i]);
		$listTasks->tas_name[$i] = str_replace("&#39;","'",$listTasks->tas_name[$i]);
		$progress = round($listTasks->tas_completion[$i]/10,2);
		$printProgress = $listTasks->tas_completion[$i]*10;
			$activity = new GanttBar($posGantt,$listTasks->tas_pro_name[$i]." / ".$listTasks->tas_name[$i],$listTasks->tas_start_date[$i],$listTasks->tas_due_date[$i]);
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
				
				if ($listSubTasks->subtas_priority[$j] == "4" || $listSubTasks->subtas_priority[$j] == 	"5") 
				{
					$activity->progress->SetPattern(BAND_SOLID,"#BB0000");
				} 
				else 
				{
					$activity->progress->SetPattern(BAND_SOLID,"#0000BB");
				}
				
				$activity->progress->Set($progress);
				$graph->Add($activity);
			} // end for compl�istsubtask
		} // end if subtask
		$posGantt += 1;
	} // end for complisttask

    $tmpGantt = "../files/".md5(uniqid(rand()));
    $graph ->Stroke($tmpGantt); 
    return $tmpGantt;

} // end ganttPDF

?>

