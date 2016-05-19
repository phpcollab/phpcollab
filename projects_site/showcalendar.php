<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../projects_site/showcalendar.php
** Authors: Fullo / UrbanFalcon
** =============================================================================
**
**               phpCollab - Project Managment 
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

include '../includes/library.php';

$bouton[12] = "over";
$titlePage = $strings["calendar"];
include 'include_header.php';

if ($type == "") 
{
	$type = "monthPreview";
}

function _dayOfWeek($timestamp) 
{ 
	return intval(strftime("%w",$timestamp)+1);
} 

$year = date("Y");
$month = date("n");
$day = date("j");

if (strlen($month) == 1) 
{
	$month = "0$month";
}

if (strlen($day) == 1) 
{
	$day= "0$day";
}

$dateToday = "$year-$month-$day";

if ($dateCalend != "") 
{
	$year = substr("$dateCalend", 0, 4);
	$month = substr("$dateCalend", 5, 2);
	$day = substr("$dateCalend", 8, 2);
}

if ($dateCalend == "") 
{
	$year = date("Y");
	$month = date("n");
	$day = date("d");
	
	if (strlen($day) == 1) 
	{
		$day = "0$day";
	}

	if (strlen($month) == 1) 
	{
		$month = "0$month";
	}
	$dateCalend = "$year-$month-$day";
}

$yearDay = date("Y");
$monthDay = date("n");
$dayDay = date("d");

$dayName = date("w",mktime(0,0,0,$month,$day,$year));
$monthName = date("n",mktime(0,0,0,$month,$day,$year));
$dayName = $dayNameArray[$dayName];
$monthName = $monthNameArray[$monthName];

$daysmonth = date("t",mktime(0,0,0,$month,$day,$year));
$firstday = date("w",mktime(0,0,0,$month,1,$year));
$padmonth = date("m",mktime(0,0,0,$month,$day,$year));
$padday = date("d",mktime(0,0,0,$month,$day,$year));

if ($firstday == 0) 
{
	$firstday = 7;
}
	
if ($type == "calendDetail") 
{
	if ($dateEnreg == "" && $id != "")
	{
		$dateEnreg = $id;
	}

	$tmpquery = "WHERE (cal.owner = '$idSession' AND cal.id = '$dateEnreg') OR (cal.broadcast = '1' AND cal.id = '$dateEnreg')";
	$detailCalendar = new Request();
	$detailCalendar->openCalendar($tmpquery);
	$comptDetailCalendar = count($detailCalendar->cal_id);

	if ($comptDetailCalendar == "0") 
	{
		header("Location:../projects_site/showcalendar.php");
	}
}
	
if ($type == "calendDetail") 
{
	$reminder = $detailCalendar->cal_reminder[0];
	$broadcast = $detailCalendar->cal_broadcast[0];
	$recurring = $detailCalendar->cal_recurring[0];
	
	if ($reminder == 0) 
	{
		$reminder = $strings["no"];
	} 
	else 
	{
		$reminder = $strings["yes"];
	}
		
	if ($broadcast == 0) 
	{
		$broadcast = $strings["no"];
	} 
	else 
	{
		$broadcast = $strings["yes"];
	}
		
	if ($recurring == 0) 
	{
		$recurring = $strings["no"];
	} 
	else 
	{
		$recurring = $strings["yes"];
	}
	
	$block1 = new Block();
	
	if ($error != "")
	{            
		$block1->headingError($strings["errors"]);
		$block1->contentError($error);
	}
	
	$block1->heading($strings["calendar"]." ".$strings["details"]);
	
	echo "<table cellspacing='0' width='90%' border='0' cellpadding='3' cols='4' class='listing'><tr><th class='active' colspan='2'>&nbsp;</th>";
	
	for ($i=0;$i<$comptDetailCalendar;$i++) 
	{
	
		if (!($i%2)) 
		{
			$class = "odd";
			$highlightOff = $block1->oddColor;
		} 
		else 
		{
			$class = "even";
			$highlightOff = $block1->evenColor;
		}
		
		echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top' width='20%'><strong>".$strings["shortname"].$block1->printHelp("calendar_shortname")."</strong> :</td><td width='80%'>".$detailCalendar->cal_shortname[0]."&nbsp;</td></tr>";
		
		if ($detailCalendar->cal_subject[0] != "")
		{
			echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["subject"]."</strong> :</td><td>".$detailCalendar->cal_subject[0]."</td></tr>";
		} 
		else 
		{
			echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["subject"]."</strong> :</td><td>".$strings["none"]."</td></tr>";
		}
	
		if ($detailCalendar->cal_description[0] != "")
		{
			echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["description"]."</strong> :</td><td>".nl2br($detailCalendar->cal_description[0])."&nbsp;</td></tr>";
		} 
		else 
		{
			echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["description"]."</strong> :</td><td>".$strings["none"]."</td></tr></tr>";
		}		

		if ($detailCalendar->cal_location[0] == "")
		{
			echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["location"]."</strong> :</td><td>".$strings["none"]."</td></tr>";
		} 
		else 
		{
			echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["location"]."</strong> :</td><td>".$detailCalendar->cal_location[0]."</td></tr>";
		}

		echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["date_start"]."</strong> :</td><td>".$detailCalendar->cal_date_start[0]."</td></tr>
		<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["date_end"]."</strong> :</td><td>".$detailCalendar->cal_date_end[0]."</td></tr>
		<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["time_start"]."</strong> :</td><td>";
		
		if ($detailCalendar->cal_time_start[0] == "")
		{
			echo "".$strings["none"]."";
		} 
		else 
		{
			$comptHours = count($hourTimeArray);
			for ($i=0;$i<$comptHours;$i++) 
			{
				if($detailCalendar->cal_time_start[0] == $longTimeArray[$i])  
				{
					echo "".$hourTimeArray[$i]."";
				}
			}
		}
		
		echo "</td></tr>";
		echo "<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td valign='top'><strong>".$strings["time_end"]."</strong> :</td><td>";
		
		if ($detailCalendar->cal_time_end[0] == "")
		{
			echo "".$strings["none"]."";
		} 
		else 
		{
			$comptHours = count($hourTimeArray);
			
			for ($i=0;$i<$comptHours;$i++)
			{
				if($detailCalendar->cal_time_end[0] == $longTimeArray[$i])  
				{
					echo "".$hourTimeArray[$i]."";
				}
			}
		}
	}
	
	echo "</table><hr>";
}

if ($type == "monthPreview") 
{

	$block2 = new Block();
	$block2->heading("$monthName $year");

	echo "<table border='0' cellpadding='0' cellspacing='0' width='100%' class='listing'><tr>";

	for($daynumber = 1; $daynumber < 8; $daynumber++) {
		echo "<td width='14%' class='calendDays' valign='middle' align='center'>&nbsp;$dayNameArray[$daynumber]</td>";
		}
		
	echo "</tr></table>";

	//	Print the calendar
	echo "<table border='0' cellpadding='5' cellspacing='1' width='100%' class='calendar-blockout-line'><tr>";

	//LIMIT CALENDAR TO CURRENT PROJECT BUT SHOW ALL ASSIGNEES
	$tmpquery = "WHERE (tas.project = '$projectSession') AND (tas.owner = '$idSession' OR tas.published = '0') ORDER BY tas.name";
	$listTasks = new Request();
	$listTasks->openTasks($tmpquery);
	$comptListTasks = count($listTasks->tas_id);

	$tmpquery = "WHERE subtas.task = tas.id AND tas.project = '$projectSession' AND (tas.owner = '$idSession' OR tas.published = '0') ORDER BY subtas.name";
	$listSubtasks = new Request();
	$listSubtasks->openSubtasks($tmpquery);
	$comptListSubtasks = count($listSubtasks->subtas_id);

	$comptListCalendarScan = "0";

	for ($g=0;$g<$comptListTasks;$g++) {
		if (substr($listTasks->tas_start_date[$g],0,7) == substr($dateCalend,0,7)) {
			$gantt = "true";
			}
		}
		
	$weekremain = ($daysmonth -(7-($firstday - 1)));
	$daysremain = ($weekremain -(floor($weekremain / 7))*7);
	$colsremain = ((7-$daysremain));

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
		$dayRecurr = _dayOfWeek(mktime(0,0,0,$month,$a,$year));
		
		$tmpquery = "WHERE (cal.owner = '$idSession' AND ((cal.date_start <= '$dateLink' AND cal.date_end >= '$dateLink' AND cal.recurring = '0') OR ((cal.date_start <= '$dateLink' AND cal.date_end >= '$dateLink') AND cal.recurring = '1' AND cal.recur_day = '$dayRecurr'))) OR (cal.broadcast = '1' AND ((cal.date_start <= '$dateLink' AND cal.date_end >= '$dateLink' AND cal.recurring = '0') OR ((cal.date_start <= '$dateLink' AND cal.date_end >= '$dateLink') AND cal.recurring = '1' AND cal.recur_day = '$dayRecurr'))) ORDER BY cal.shortname";
		$listCalendarScan = new Request();
		$listCalendarScan->openCalendar($tmpquery);
		$comptListCalendarScan = count($listCalendarScan->cal_id);
		
		if (($i < $firstday) || ($a == "00")) { 
			echo "<td width='14%' class='even'>&nbsp;</td>";

			} else {

			if ($dateLink == $dateToday) {
				$classCell = "evenassigned";
				} else {
				$classCell = "odd";
				}

			echo "<td width='14%' height='100' align='left' valign='top' class='$classCell' onmouseover=\"this.style.backgroundColor='".$block2->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><div align='right'><h3>$day</h3></div>";
			
			if ($comptListCalendarScan != "0") 
			{
				for ($h=0;$h<$comptListCalendarScan;$h++) 
				{
					if ($listCalendarScan->cal_broadcast[$h] == "1")
					{
						echo "<div align='center' class='calendar-broadcast-event'><a href='$PHP_SELF?dateEnreg=".$listCalendarScan->cal_id[$h]."&type=calendDetail&dateCalend=$dateLink' class='calendar-broadcast-todo-event'><b>".$listCalendarScan->cal_shortname[$h]."</b></a></div>";
					}
				}
			}

			if ($comptListTasks != "0") 
			{
				
				for ($h=0;$h<$comptListTasks;$h++) 
				{

					if ($listTasks->tas_status[$h] == "3" || $listTasks->tas_status[$h] == "2") 
					{
						if ($listTasks->tas_start_date[$h] == $dateLink && $listTasks->tas_start_date[$h] != $listTasks->tas_due_date[$h]) 
						{
							echo "<b>".$strings["task"]."</b>: ";
							echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-start-date'>".$listTasks->tas_name[$h]."</a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
						}

						if ($listTasks->tas_due_date[$h] == $dateLink && $listTasks->tas_start_date[$h] != $listTasks->tas_due_date[$h]) 
						{
							echo "<b>".$strings["task"]."</b>: ";
							if ($listTasks->tas_due_date[$h] <= $date && $listTasks->tas_completion[$h] != "10") 
							{
								echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-due-date'><b>".$listTasks->tas_name[$h]."</b></a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
							} 
							else 
							{
								echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-due-date'>".$listTasks->tas_name[$h]."</a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
							}
						}
						
						if ($listTasks->tas_start_date[$h] == $dateLink && $listTasks->tas_due_date[$h] == $dateLink) 
						{
							echo "<b>".$strings["task"]."</b>: ";
							
							if ($listTasks->tas_due_date[$h] <= $date && $listTasks->tas_completion[$h] != "10") 
							{
								echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-due-date'><b>".$listTasks->tas_name[$h]."</b></a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
							} 
							else 
							{
								echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-due-date'>".$listTasks->tas_name[$h]."</a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
							}
						}
					} 
					else 
					{
						if ($listTasks->tas_start_date[$h] == $dateLink && $listTasks->tas_start_date[$h] != $listTasks->tas_due_date[$h]) 
						{
							echo "<b>".$strings["task"]."</b>: ";
							echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."'>".$listTasks->tas_name[$h]."</a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
						}
						
						if ($listTasks->tas_due_date[$h] == $dateLink && $listTasks->tas_start_date[$h] != $listTasks->tas_due_date[$h]) 
						{
							echo "<b>".$strings["task"]."</b>: ";
							if ($listTasks->tas_due_date[$h] <= $date && $listTasks->tas_completion[$h] != "10") 
							{
								echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."'><b>".$listTasks->tas_name[$h]."</b></a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
							} 
							else 
							{
								echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."'>".$listTasks->tas_name[$h]."</a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
							}
						}

						if ($listTasks->tas_start_date[$h] == $dateLink && $listTasks->tas_due_date[$h] == $dateLink) 
						{
							echo "<b>".$strings["task"]."</b>: ";
							if ($listTasks->tas_due_date[$h] <= $date && $listTasks->tas_completion[$h] != "10") 
							{
								echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."'><b>".$listTasks->tas_name[$h]."</b></a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
							} 
							else 
							{
								echo "<a href='../projects_site/teamtaskdetail.php?id=".$listTasks->tas_id[$h]."'>".$listTasks->tas_name[$h]."</a><br />(".$listTasks->tas_mem_name[$h].")<br /><br />";
							}
						}
					}					
				}
			}
				
			if ($comptListSubtasks != "0") 
			{
			
				for ($h=0;$h<$comptListSubtasks;$h++) 
				{
					
					if ($listSubtasks->subtas_status[$h] == "3" || $listSubtasks->subtas_status[$h] == "2") 
					{
						if ($listSubtasks->subtas_start_date[$h] == $dateLink && $listSubtasks->subtas_start_date[$h] != $listSubtasks->subtas_due_date[$h]) 
						{
							echo "<b>".$strings["subtask"]."</b>: ";
							echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."' class='calendar-results-start-date'>".$listSubtasks->subtas_name[$h]."</a><br />(".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
						}
						
						if ($listSubtasks->subtas_due_date[$h] == $dateLink && $listSubtasks->subtas_start_date[$h] != $listSubtasks->subtas_due_date[$h]) 
						{
							echo "<b>".$strings["subtask"]."</b>: ";
							if ($listSubtasks->subtas_due_date[$h] <= $date && $listSubtasks->subtas_completion[$h] != "10") 
							{
								echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."' class='calendar-results-due-date'><b>".$listSubtasks->subtas_name[$h]."</b></a><br />(".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
							} 
							else 
							{
								echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."' class='calendar-results-due-date'>".$listSubtasks->subtas_name[$h]."</a><br />(".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
							}
						}
						
						if ($listSubtasks->subtas_start_date[$h] == $dateLink && $listSubtasks->subtas_due_date[$h] == $dateLink) 
						{
							echo "<b>".$strings["subtask"]."</b>: ";
							if ($listSubtasks->subtas_due_date[$h] <= $date && $listSubtasks->subtas_completion[$h] != "10") 
							{
								echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."' class='calendar-results-due-date'><b>".$listSubtasks->subtas_name[$h]."</b></a>(<br />".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
							} 
							else 
							{
								echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."' class='calendar-results-due-date'>".$listSubtasks->subtas_name[$h]."</a><br />(".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
							}
						}
					} 
					else 
					{
						if ($listSubtasks->subtas_start_date[$h] == $dateLink && $listSubtasks->subtas_start_date[$h] != $listSubtasks->subtas_due_date[$h]) 
						{
							echo "<b>".$strings["subtask"]."</b>: ";
							echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."'>".$listSubtasks->subtas_name[$h]."</a><br />(".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
						}
						
						if ($listSubtasks->subtas_due_date[$h] == $dateLink && $listSubtasks->subtas_start_date[$h] != $listSubtasks->subtas_due_date[$h]) 
						{
							echo "<b>".$strings["subtask"]."</b>: ";
							if ($listSubtasks->subtas_due_date[$h] <= $date && $listSubtasks->subtas_completion[$h] != "10") 
							{
								echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."'><b>".$listSubtasks->subtas_name[$h]."</b></a>(<br />".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
							} 
							else 
							{
								echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."'>".$listSubtasks->subtas_name[$h]."</a><br />(".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
							}
						}
						
						if ($listSubtasks->subtas_start_date[$h] == $dateLink && $listSubtasks->subtas_due_date[$h] == $dateLink) 
						{
							echo "<b>".$strings["subtask"]."</b>: ";
						
							if ($listSubtasks->subtas_due_date[$h] <= $date && $listSubtasks->subtas_completion[$h] != "10") 
							{
								echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."'><b>".$listSubtasks->subtas_name[$h]."</b></a><br />(".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
							} 
							else 
							{
								echo "<a href='../projects_site/teamsubtaskdetail.php?task=".$listSubtasks->subtas_task[$h]."&id=".$listSubtasks->subtas_id[$h]."'>".$listSubtasks->subtas_name[$h]."</a><br />(".$listSubtasks->subtas_mem_name[$h].")<br /><br />";
							}
						}
					}
				}
			}
			
			if ($comptListTasks == "0" || $comptListSubtasks == "0" || $comptListCalendarScan == "0") 
			{
				echo "<br />";
			}
				
			echo "&nbsp;</td>";
		}

		if (($i%7) == "0") 
		{
			echo "</tr>";
		}
	}

	//if (($i%7) != 1) {
	//	echo "</tr><tr>\n";
	//	}

	if ($colsremain != "7")
	{
		for ($j=0;$j<$colsremain;$j++)
		{
			echo "<td class='even'>&nbsp;</td>\n";
		}
	}

	echo "</tr></table>";

	if ($month == 1) 
	{
		$pyear = $year - 1;
		$pmonth = 12;
	} 
	else 
	{
		$pyear = $year;
		$pmonth = $month - 1;
	}

	if ($month == 12) 
	{
		$nyear = $year + 1;
		$nmonth = 1;
	} 
	else 
	{
		$nyear = $year;
		$nmonth = $month + 1;
	}

	$year = date("Y");
	$month = date("n");
	$day = date("j");

	if (strlen($month) == 1) 
	{
		$month = "0$month";
	}

	if (strlen($pmonth) == 1) 
	{
		$pmonth = "0$pmonth";
	}

	if (strlen($nmonth) == 1) 
	{
		$nmonth = "0$nmonth";
	}

	if (strlen($day) == 1) 
	{
		$day= "0$day";
	}
		
	$datePast = "$pyear-$pmonth-01";
	$dateNext = "$nyear-$nmonth-01";
	$dateToday = "$year-$month-$day";

	echo "	<br />
		<table cellspacing='0' border='0' cellpadding='0' align='right'>
		<tr>
			<th align='center'>&nbsp;&nbsp;<a href='$PHP_SELF?dateCalend=$datePast'>".$strings["previous"]."</a> | <a href='$PHP_SELF?dateCalend=$dateToday'>".$strings["today"]."</a> | <a href='$PHP_SELF?dateCalend=$dateNext'>".$strings["next"]."</a>&nbsp;&nbsp;</th>
		</tr>
		</table>
		<br />";

	if ($activeJpgraph == "true" && $gantt == "true") 
	{
		echo "
			<div id='ganttChart_taskList' class='ganttChart'>
				<img src='graphtasks.php?dateCalend=$dateCalend' alt=''><br/>
				<span class='listEvenBold''>".$blockPage->buildLink("http://www.aditus.nu/jpgraph/","JpGraph",powered)."</span>	
			</div>
		";
	}
}

include ("include_footer.php");
?>