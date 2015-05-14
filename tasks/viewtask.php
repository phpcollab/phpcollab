<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../tasks/viewtask.php
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
** FILE: viewtask.php
**
** DESC: Screen:  view  task information
**
** HISTORY:
**	05/11/2004	-	fixed 1059973 
**	19/05/2005	-	fixed and &amp; in link
**  22/05/2005	-	added [MOD] file owner label in linked content list
**  10/02/2007  -   Changed JPGraph implementation
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

if ($task != "") 
{
	$cheatCode = "true";
}

if ($action == "publish") 
{

	if ($addToSite == "true") 
	{
		$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='0' WHERE id = '$id'";
		Util::connectSql("$tmpquery1");
		$msg = "addToSite";
	}

	if ($removeToSite == "true") 
	{
		$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='1' WHERE id = '$id'";
		Util::connectSql("$tmpquery1");
		$msg = "removeToSite";
	}

	if ($addToSiteFile == "true") 
	{
		$id = str_replace("**",",",$id);
		$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='0' WHERE id IN($id) OR vc_parent IN ($id)";
		Util::connectSql("$tmpquery1");
		$msg = "addToSite";
		$id = $task;
	}

	if ($removeToSiteFile == "true") 
	{
		$id = str_replace("**",",",$id);
		$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='1' WHERE id IN($id) OR vc_parent IN ($id)";
		Util::connectSql("$tmpquery1");
		$msg = "removeToSite";
		$id = $task;
	}
}

if ($task != "" && $cheatCode == "true") 
{
	$id = $task;
}

$tmpquery = "WHERE tas.id = '$id'";
$taskDetail = new request();
$taskDetail->openTasks($tmpquery);

$tmpquery = "WHERE pro.id = '".$taskDetail->tas_project[0]."'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

if ($projectDetail->pro_enable_phase[0] != "0")
{
	$tPhase = $taskDetail->tas_parent_phase[0];
	if (!$tPhase){ $tPhase = '0'; }
	$tmpquery = "WHERE pha.project_id = '".$taskDetail->tas_project[0]."' AND pha.order_num = '$tPhase'";
	$targetPhase = new request();
	$targetPhase->openPhases($tmpquery);
}

$teamMember = "false";
$tmpquery = "WHERE tea.project = '".$taskDetail->tas_project[0]."' AND tea.member = '$idSession'";
$memberTest = new request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);

if ($comptMemberTest == "0") 
{
	$teamMember = "false";
} 
else 
{
	$teamMember = "true";
}

if ($teamMember == "false" && $projectsFilter == "true") 
{ 
	header("Location:../general/permissiondenied.php?".session_name()."=".session_id()); 
	exit; 
} 

include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));

if ($projectDetail->pro_phase_set[0] != "0")
{
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=".$projectDetail->pro_id[0],$strings["phases"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=".$targetPhase->pha_id[0],$targetPhase->pha_name[0],in));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=".$projectDetail->pro_id[0],$strings["tasks"],in));
$blockPage->itemBreadcrumbs($taskDetail->tas_name[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") 
{
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new block();

$block1->form = "tdD";
$block1->openForm("../tasks/viewtask.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->headingToggle($strings["task"]." : ".$taskDetail->tas_name[0]);

if ($teamMember == "true" || $profilSession == "5") 
{
	$block1->openPaletteIcon();
	$block1->paletteIcon(0,"remove",$strings["delete"]);
	$block1->paletteIcon(1,"copy",$strings["copy"]);
	//$block1->paletteIcon(2,"export",$strings["export"]);
	
	if ($sitePublish == "true") 
	{
		$block1->paletteIcon(3,"add_projectsite",$strings["add_project_site"]);
		$block1->paletteIcon(4,"remove_projectsite",$strings["remove_project_site"]);
	}

	$block1->paletteIcon(5,"edit",$strings["edit"]);
	$block1->closePaletteIcon();
}

if ($projectDetail->pro_org_id[0] == "1") 
{
	$projectDetail->pro_org_name[0] = $strings["none"];
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"],$blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));

if ($projectDetail->pro_phase_set[0] != "0")
{
	$block1->contentRow($strings["phase"],$blockPage->buildLink("../phases/viewphase.php?id=".$targetPhase->pha_id[0],$targetPhase->pha_name[0],in));
}

$block1->contentRow($strings["organization"],$projectDetail->pro_org_name[0]);

$block1->contentRow($strings["created"],Util::createDate($taskDetail->tas_created[0],$timezoneSession));
$block1->contentRow($strings["assigned"],Util::createDate($taskDetail->tas_assigned[0],$timezoneSession));
$block1->contentRow($strings["modified"],Util::createDate($taskDetail->tas_modified[0],$timezoneSession));

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"],$taskDetail->tas_name[0]);

$block1->contentRow($strings["description"],nl2br($taskDetail->tas_description[0]));
if ($taskDetail->tas_assigned_to[0] == "0") 
{
	$block1->contentRow($strings["assigned_to"],$strings["unassigned"]);
} 
else 
{
	$block1->contentRow($strings["assigned_to"],$blockPage->buildLink("../users/viewuser.php?id=".$taskDetail->tas_mem_id[0],$taskDetail->tas_mem_name[0],in)." (".$blockPage->buildLink($taskDetail->tas_mem_email_work[0],$taskDetail->tas_mem_login[0],mail).")");
}

$idStatus = $taskDetail->tas_status[0];
$idPriority = $taskDetail->tas_priority[0];
$idPublish = $taskDetail->tas_published[0];
$complValue = ($taskDetail->tas_completion[0]>0) ? $taskDetail->tas_completion[0]."0 %": $taskDetail->tas_completion[0]." %"; 
$block1->contentRow($strings["status"],$status[$idStatus]);
$block1->contentRow($strings["completion"],$complValue);
$block1->contentRow($strings["priority"],"<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt=\"\"> ".$priority[$idPriority]);
$block1->contentRow($strings["start_date"],$taskDetail->tas_start_date[0]);

if ($taskDetail->tas_due_date[0] <= $date && $taskDetail->tas_completion[0] != "10") 
{
	$block1->contentRow($strings["due_date"],"<b>".$taskDetail->tas_due_date[0]."</b>");
} 
else 
{
	$block1->contentRow($strings["due_date"],$taskDetail->tas_due_date[0]);
}

if ($taskDetail->tas_complete_date[0] != "" && $taskDetail->tas_complete_date[0] != "--" && $taskDetail->tas_due_date[0] != "--") 
{
	$diff = Util::diffDate($taskDetail->tas_complete_date[0],$taskDetail->tas_due_date[0]);

	if ($diff > 0) 
	{
		$diff = "<b>+$diff</b>";
	}
	
	$block1->contentRow($strings["complete_date"],$taskDetail->tas_complete_date[0]);
	$block1->contentRow($strings["scope_creep"].$blockPage->printHelp("task_scope_creep"),"$diff ".$strings["days"]);
}

$block1->contentRow($strings["estimated_time"],$taskDetail->tas_estimated_time[0]." ".$strings["hours"]);
$block1->contentRow($strings["actual_time"],$taskDetail->tas_actual_time[0]." ".$strings["hours"]);

if ($sitePublish == "true") 
{
	$block1->contentRow($strings["published"],$statusPublish[$idPublish]);
}

if ($enableInvoicing == "true") {
	if ($taskDetail->tas_invoicing[0] == "1") 
	{
		$block1->contentRow($strings["invoicing"],$strings["true"]);
	} 
	else 
	{
		$block1->contentRow($strings["invoicing"],$strings["false"]);
	}
}

$block1->contentRow($strings["worked_hours"],$taskDetail->tas_worked_hours[0]);
$block1->contentRow($strings["comments"],nl2br($taskDetail->tas_comments[0]));

$block1->contentTitle($strings["updates_task"]);
$tmpquery = "WHERE upd.type='1' AND upd.item = '$id' ORDER BY upd.created DESC";
$listUpdates = new request();
$listUpdates->openUpdates($tmpquery);
$comptListUpdates=count($listUpdates->upd_id);

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>";

if ($comptListUpdates != "0") 
{
	$j = 1;

	for ($i=0;$i<$comptListUpdates;$i++)
	{
		if (preg_match('/\[status:([0-9])\]/',$listUpdates->upd_comments[$i]))
		{
			preg_match('|\[status:([0-9])\]|i',$listUpdates->upd_comments[$i],$matches);
			$listUpdates->upd_comments[$i] = preg_replace('/\[status:([0-9])\]/', '',$listUpdates->upd_comments[$i].'<br/>');
			$listUpdates->upd_comments[$i] .= $strings["status"]." ".$status[$matches[1]];
		}
		if (preg_match('/\[priority:([0-9])\]/',$listUpdates->upd_comments[$i]))
		{
			preg_match('|\[priority:([0-9])\]|i',$listUpdates->upd_comments[$i],$matches);
			$listUpdates->upd_comments[$i] = preg_replace('/\[priority:([0-9])\]/','',$listUpdates->upd_comments[$i].'<br/>');
			$listUpdates->upd_comments[$i] .= $strings["priority"]." ".$priority[$matches[1]];
		}
		if (preg_match('/\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]/',$listUpdates->upd_comments[$i]))
		{
			preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|i',$listUpdates->upd_comments[$i],$matches);
			$listUpdates->upd_comments[$i] = preg_replace('/\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]/','',$listUpdates->upd_comments[$i].'<br/>');
			$listUpdates->upd_comments[$i] .= $strings["due_date"].' '.$matches[1];
		}

		$abbrev = stripslashes(substr($listUpdates->upd_comments[$i],0,100));
		echo "<b>".$j.".</b> <i>".Util::createDate($listUpdates->upd_created[$i],$timezoneSession)."</i> $abbrev";

		if (100 < strlen($listUpdates->upd_comments[$i])) 
		{
			echo "...<br/>";
		} 
		else 
		{
			echo "<br/>";
		}

		$j++;
	}

	echo "<br/>".$blockPage->buildLink("../tasks/historytask.php?type=1&item=$id",$strings["show_details"],in);
} 
else 
{
	echo $strings["no_items"];
}

echo "</td></tr>";

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($teamMember == "true" || $profilSession == "5") 
{
	$block1->openPaletteScript();
	$block1->paletteScript(0,"remove","../tasks/deletetasks.php?project=".$taskDetail->tas_project[0]."&id=".$taskDetail->tas_id[0]."","true,true,false",$strings["delete"]);
	$block1->paletteScript(1,"copy","../tasks/edittask.php?project=".$taskDetail->tas_project[0]."&id=".$taskDetail->tas_id[0]."&docopy=true","true,true,false",$strings["copy"]);
	//$block1->paletteScript(2,"export","export.php?","true,true,false",$strings["export"]);

	if ($sitePublish == "true") 
	{
		$block1->paletteScript(3,"add_projectsite","../tasks/viewtask.php?addToSite=true&id=".$taskDetail->tas_id[0]."&action=publish","true,true,true",$strings["add_project_site"]);
		$block1->paletteScript(4,"remove_projectsite","../tasks/viewtask.php?removeToSite=true&id=".$taskDetail->tas_id[0]."&action=publish","true,true,true",$strings["remove_project_site"]);
	}
	$block1->paletteScript(5,"edit","../tasks/edittask.php?project=".$taskDetail->tas_project[0]."&id=".$taskDetail->tas_id[0]."&docopy=false","true,true,false",$strings["edit"]);
	$block1->closePaletteScript("","");
}

if ($fileManagement == "true") 
{

	$block2 = new block();
	$block2->form = "tdC";
	$block2->openForm("../tasks/viewtask.php?".session_name()."=".session_id()."&id=$id#".$block2->form."Anchor");
	$block2->headingToggle($strings["linked_content"]);
	$block2->openPaletteIcon();

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block2->paletteIcon(0,"add",$strings["add"]);
		$block2->paletteIcon(1,"remove",$strings["delete"]);
		
		if ($sitePublish == "true") 
		{
			$block2->paletteIcon(2,"add_projectsite",$strings["add_project_site"]);
			$block2->paletteIcon(3,"remove_projectsite",$strings["remove_project_site"]);
		}
	}

	$block2->paletteIcon(4,"info",$strings["view"]);
	
	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block2->paletteIcon(5,"edit",$strings["edit"]);
	}

	$block2->closePaletteIcon();
	$block2->sorting("files",$sortingUser->sor_files[0],"fil.name ASC",$sortingFields = array(0=>"fil.extension",1=>"fil.name",2=>"fil.owner",3=>"fil.date",4=>"fil.status",5=>"fil.published"));
	
	$tmpquery = "WHERE fil.task = '$id' AND fil.vc_parent = '0' ORDER BY $block2->sortingValue";
	$listFiles = new request();
	$listFiles->openFiles($tmpquery);
	$comptListFiles = count($listFiles->fil_id);

	if ($comptListFiles != "0") 
	{
		$block2->openResults();
		$block2->labels($labels = array(0=>$strings["type"],1=>$strings["name"],2=>$strings["owner"],3=>$strings["date"],4=>$strings["approval_tracking"],5=>$strings["published"]),"true");

		include("../includes/files_types.php");

		for ($i=0;$i<$comptListFiles;$i++) 
		{
			$existFile = "false";
			$idStatus = $listFiles->fil_status[$i];
			$idPublish = $listFiles->fil_published[$i];

			$type = file_info_type($listFiles->fil_extension[$i]);
			if (file_exists("../files/".$listFiles->fil_project[$i]."/".$listFiles->fil_task[$i]."/".$listFiles->fil_name[$i])) 
			{
				$existFile = "true";
			}
			
			$block2->openRow();
			$block2->checkboxRow($listFiles->fil_id[$i]);
			
			if ($existFile == "true") 
			{
				$block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$type,icone));
			} 
			else 
			{
				$block2->cellRow("&nbsp;");
			}
			
			if ($existFile == "true") 
			{
				$block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$listFiles->fil_name[$i],in));
			} 
			else 
			{
				$block2->cellRow($strings["missing_file"]." (".$listFiles->fil_name[$i].")");
			}

			$block2->cellRow($blockPage->buildLink($listFiles->fil_mem_email_work[$i],$listFiles->fil_mem_login[$i],mail));
			$block2->cellRow($listFiles->fil_date[$i]);
			$block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$statusFile[$idStatus],in));
			
			if ($sitePublish == "true") 
			{
				$block2->cellRow($statusPublish[$idPublish]);
			}
			$block2->closeRow();
		}
		$block2->closeResults();
	} 
	else 
	{
		$block2->noresults();
	}
	
	$block2->closeToggle();
	$block2->closeFormResults();
	$block2->openPaletteScript();

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block2->paletteScript(0,"add","../linkedcontent/addfile.php?project=".$taskDetail->tas_project[0]."&task=$id","true,true,true",$strings["add"]);
		$block2->paletteScript(1,"remove","../linkedcontent/deletefiles.php?project=".$projectDetail->pro_id[0]."&task=".$taskDetail->tas_id[0]."","false,true,true",$strings["delete"]);
		
		if ($sitePublish == "true") 
		{
			$block2->paletteScript(2,"add_projectsite","../tasks/viewtask.php?addToSiteFile=true&task=".$taskDetail->tas_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
			$block2->paletteScript(3,"remove_projectsite","../tasks/viewtask.php?removeToSiteFile=true&task=".$taskDetail->tas_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
		}
	}

	$block2->paletteScript(4,"info","../linkedcontent/viewfile.php?","false,true,false",$strings["view"]);
	
	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block2->paletteScript(5,"edit","../linkedcontent/viewfile.php?".session_name()."=".session_id()."&edit=true","false,true,false",$strings["edit"]);
	}
	$block2->closePaletteScript($comptListFiles,$listFiles->fil_id);
}

$block3 = new block();

$block3->form = "ahT";
$block3->openForm("../tasks/viewtask.php?".session_name()."=".session_id()."&id=$id#".$block3->form."Anchor");
$block3->headingToggle($strings["assignment_history"]);
$block3->sorting("assignment",$sortingUser->sor_assignment[0],"ass.assigned DESC",$sortingFields = array(0=>"ass.comments",1=>"mem1.login",2=>"mem2.login",3=>"ass.assigned"));

$tmpquery = "WHERE ass.task = '$id' ORDER BY $block3->sortingValue";
$listAssign = new request();
$listAssign->openAssignments($tmpquery);
$comptListAssign = count($listAssign->ass_id);

$block3->openResults($checkbox="false");
$block3->labels($labels = array(0=>$strings["comment"],1=>$strings["assigned_by"],2=>$strings["to"],3=>$strings["assigned_on"]),"false");

for ($i=0;$i<$comptListAssign;$i++) 
{
	$block3->openRow();
	$block3->checkboxRow($listAssign->ass_id[$i],$checkbox="false");
	if ($listAssign->ass_comments[$i] != "") 
	{
		$block3->cellRow($listAssign->ass_comments[$i]);
	} 
	else if ($listAssign->ass_assigned_to[$i] == "0") 
	{
		$block3->cellRow($strings["task_unassigned"]);
	} 
	else 
	{
		$block3->cellRow($strings["task_assigned"]." ".$listAssign->ass_mem2_name[$i]." (".$listAssign->ass_mem2_login[$i].")");
	}
	
	$block3->cellRow($blockPage->buildLink($listAssign->ass_mem1_email_work[$i],$listAssign->ass_mem1_login[$i],mail));
	
	if ($listAssign->ass_assigned_to[$i] == "0") 
	{
		$block3->cellRow($strings["unassigned"]);
	} 
	else 
	{
		$block3->cellRow($blockPage->buildLink($listAssign->ass_mem2_email_work[$i],$listAssign->ass_mem2_login[$i],mail));
	}
	$block3->cellRow(Util::createDate($listAssign->ass_assigned[$i],$timezoneSession));
	$block3->closeRow();
}

$block3->closeResults();
$block3->closeToggle();
$block3->closeFormResults();

$block4 = new block();
$block4->form = "subT";
$block4->openForm("../tasks/viewtask.php?".session_name()."=".session_id()."&task=$id#".$block4->form."Anchor");
$block4->headingToggle($strings["subtasks"]);
$block4->openPaletteIcon();

if ($teamMember == "true" || $profilSession == "5") 
{
	$block4->paletteIcon(0,"add",$strings["add"]);
	$block4->paletteIcon(1,"remove",$strings["delete"]);
	//$block4->paletteIcon(2,"copy",$strings["copy"]);
	//$block4->paletteIcon(3,"export",$strings["export"]);
	/*if ($sitePublish == "true") {
		$block4->paletteIcon(4,"add_projectsite",$strings["add_project_site"]);
		$block4->paletteIcon(5,"remove_projectsite",$strings["remove_project_site"]);
	}*/
}
$block4->paletteIcon(6,"info",$strings["view"]);

if ($teamMember == "true" || $profilSession == "5") 
{
	$block4->paletteIcon(7,"edit",$strings["edit"]);
}

$block4->closePaletteIcon();
$block4->sorting("subtasks",$sortingUser->sor_subtasks[0],"subtas.name ASC",$sortingFields = array(0=>"subtas.name",1=>"subtas.priority",2=>"subtas.status",3=>"subtas.completion",4=>"subtas.due_date",5=>"mem.login",6=>"subtas.published"));

$tmpquery = "WHERE subtas.task = '$id' ORDER BY $block4->sortingValue";
$listSubtasks = new request();
$listSubtasks->openSubtasks($tmpquery);
$comptListSubtasks = count($listSubtasks->subtas_id);

if ($comptListSubtasks != "0") 
{
	$block4->openResults();
	$block4->labels($labels = array(0=>$strings["subtask"],1=>$strings["priority"],2=>$strings["status"],3=>$strings["completion"],4=>$strings["due_date"],5=>$strings["assigned_to"],6=>$strings["published"]),"true");

	for ($i=0;$i<$comptListSubtasks;$i++) 
	{
		if ($listSubtasks->subtas_due_date[$i] == "") 
		{
			$listSubtasks->subtas_due_date[$i] = $strings["none"];
		}
		
		$idStatus = $listSubtasks->subtas_status[$i];
		$idPriority = $listSubtasks->subtas_priority[$i];
		$idPublish = $listSubtasks->subtas_published[$i];
		$complValue = ($listSubtasks->subtas_completion[$i]>0) ? $listSubtasks->subtas_completion[$i]."0 %": $listSubtasks->subtas_completion[$i]." %"; 
		
		$block4->openRow();
		$block4->checkboxRow($listSubtasks->subtas_id[$i]);
		$block4->cellRow($blockPage->buildLink("../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$i]."&task=$id",$listSubtasks->subtas_name[$i],in));
		$block4->cellRow("<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt=\"\"> ".$priority[$idPriority]);
		$block4->cellRow($status[$idStatus]);
		$block4->cellRow($complValue);
		
		if ($listSubtasks->subtas_due_date[$i] <= $date && $listSubtasks->subtas_completion[$i] != "10") 
		{
			$block4->cellRow("<b>".$listSubtasks->subtas_due_date[$i]."</b>");
		} 
		else 
		{
			$block4->cellRow($listSubtasks->subtas_due_date[$i]);
		}
		
		if ($listSubtasks->subtas_start_date[$i] != "--" && $listSubtasks->subtas_due_date[$i] != "--") 
		{
			$gantt = "true";
		}
		
		if ($listSubtasks->subtas_assigned_to[$i] == "0") 
		{
			$block4->cellRow($strings["unassigned"]);
		} 
		else 
		{
			$block4->cellRow($blockPage->buildLink($listSubtasks->subtas_mem_email_work[$i],$listSubtasks->subtas_mem_login[$i],mail));
		}
		
		if ($sitePublish == "true") 
		{
			$block4->cellRow($statusPublish[$idPublish]);
		}
		$block4->closeRow();
	}
	$block4->closeResults();

	if ($activeJpgraph == "true" && $gantt == "true") 
	{
		echo "
			<div id='ganttChart_taskList' class='ganttChart'>
				<img src='../subtasks/graphsubtasks.php?".session_name()."=".session_id()."&task=".$id."' alt=''><br/>
				<span class='listEvenBold''>".$blockPage->buildLink("http://www.aditus.nu/jpgraph/","JpGraph",powered)."</span>	
			</div>
		";
	}
} 
else 
{
	$block4->noresults();
}

$block4->closeToggle();
$block4->closeFormResults();
$block4->openPaletteScript();

if ($teamMember == "true" || $profilSession == "5") 
{
	$block4->paletteScript(0,"add","../subtasks/editsubtask.php?task=$id","true,false,false",$strings["add"]);
	$block4->paletteScript(1,"remove","../subtasks/deletesubtasks.php?task=$id","false,true,true",$strings["delete"]);

	//$block4->paletteScript(2,"copy","../subtasks/editsubtask.php?task=$id&docopy=true","false,true,false",$strings["copy"]);
	//$block4->paletteScript(3,"export","export.php?","false,true,true",$strings["export"]);
	/*if ($sitePublish == "true") {
	$block4->paletteScript(4,"add_projectsite","../tasks/viewtask.php?addToSite=true&task=$id&action=publish","false,true,true",$strings["add_project_site"]);
	$block4->paletteScript(5,"remove_projectsite","../tasks/viewtask.php?removeToSite=true&task=$id&action=publish","false,true,true",$strings["remove_project_site"]);
	}*/
}
$block4->paletteScript(6,"info","../subtasks/viewsubtask.php?task=$id","false,true,false",$strings["view"]);

if ($teamMember == "true" || $profilSession == "5") 
{
	$block4->paletteScript(7,"edit","../subtasks/editsubtask.php?task=$id","false,true,true",$strings["edit"]);
}
$block4->closePaletteScript($comptListSubtasks,$listSubtasks->subtas_id);

include('../themes/'.THEME.'/footer.php');
?>