<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../projects/viewproject.php
** Authors: Ceam / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewproject.php
**
** DESC: Screen: show project details
**
** HISTORY:
** 	17/05/2005	-	added new document info
**	17/05/2005	-	fixed copy task issue
**  22/05/2005	-	added [MOD] file owner label in linked content list
** -----------------------------------------------------------------------------
** TO-DO:
**	 
**
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');
include("../includes/customvalues.php");

$id = Util::returnGlobal('id','REQUEST');
$project = Util::returnGlobal('project','REQUEST');
$action = Util::returnGlobal('action','GET');

if ($action == "publish") 
{
	$closeTopic = Util::returnGlobal('closeTopic','GET');

	if ($closeTopic == "true") 
	{
		$multi = strstr($id,"**");

		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["topics"]." SET status='0' WHERE id IN($id)";
			$pieces = explode(",",$id);
			$num = count($pieces);
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["topics"]." SET status='0' WHERE id = '".Util::fixInt($id)."'";
			$num = "1";
		}
		
		Util::connectSql("$tmpquery1");
		$msg = "closeTopic";
		$id = $project;
	}

	$addToSiteTask = Util::returnGlobal('addToSiteTask','GET');

	if ($addToSiteTask == "true") 
	{
		$multi = strstr($id,"**");
		
		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='0' WHERE id IN($id)";
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='0' WHERE id = '".Util::fixInt($id)."'";
		}
		
		Util::connectSql("$tmpquery1");
		$msg = "addToSite";
		$id = $project;
	}

	$removeToSiteTask = Util::returnGlobal('removeToSiteTask','GET');

	if ($removeToSiteTask == "true") 
	{
		$multi = strstr($id,"**");

		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='1' WHERE id IN($id)";
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='1' WHERE id = '".Util::fixInt($id)."'";
		}
	
		Util::connectSql("$tmpquery1");
		$msg = "removeToSite";
		$id = $project;
	}

	$addToSiteTopic = Util::returnGlobal('addToSiteTopic','GET');

	if ($addToSiteTopic == "true") 
	{
		$multi = strstr($id,"**");

		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["topics"]." SET published='0' WHERE id IN($id)";
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["topics"]." SET published='0' WHERE id = '".Util::fixInt($id)."'";
		}

			Util::connectSql("$tmpquery1");
			$msg = "addToSite";
			$id = $project;
	}

	$removeToSiteTopic = Util::returnGlobal('removeToSiteTopic','GET');
	if ($removeToSiteTopic == "true") 
	{
		$multi = strstr($id,"**");
		
		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["topics"]." SET published='1' WHERE id IN($id)";
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["topics"]." SET published='1' WHERE id = '".Util::fixInt($id)."'";
		}

		Util::connectSql("$tmpquery1");
		$msg = "removeToSite";
		$id = $project;
	}

	$addToSiteTeam = Util::returnGlobal('addToSiteTeam','GET');
	if ($addToSiteTeam == "true") 
	{
		$multi = strstr($id,"**");
		
		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["teams"]." SET published='0' WHERE member IN($id) AND project = '$project'";
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["teams"]." SET published='0' WHERE member = '$id' AND project = '$project'";
		}
		
		Util::connectSql("$tmpquery1");
		$msg = "addToSite";
		$id = $project;
	}

	$removeToSiteTeam = Util::returnGlobal('removeToSiteTeam','GET');
	if ($removeToSiteTeam == "true") 
	{
		$multi = strstr($id,"**");

		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["teams"]." SET published='1' WHERE member IN($id) AND project = '$project'";
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["teams"]." SET published='1' WHERE member = '$id' AND project = '$project'";
		}

		Util::connectSql("$tmpquery1");
		$msg = "removeToSite";
		$id = $project;
	}

	$addToSiteFile = Util::returnGlobal('addToSiteFile','GET');

	if ($addToSiteFile == "true") 
	{
		$id = str_replace("**",",",$id);
		$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='0' WHERE id IN($id) OR vc_parent IN ($id)";
		Util::connectSql("$tmpquery1");
		$msg = "addToSite";
		$id = $project;
	}

	$removeToSiteFile = Util::returnGlobal('removeToSiteFile','GET');

	if ($removeToSiteFile == "true") 
	{
		$id = str_replace("**",",",$id);
		$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='1' WHERE id IN($id) OR vc_parent IN ($id)";
		Util::connectSql("$tmpquery1");
		$msg = "removeToSite";
		$id = $project;
	}

	$addToSiteNote = Util::returnGlobal('addToSiteNote','GET');

	if ($addToSiteNote == "true") 
	{
		$multi = strstr($id,"**");
		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["notes"]." SET published='0' WHERE id IN($id)";
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["notes"]." SET published='0' WHERE id = '".Util::fixInt($id)."'";
		}
		
		Util::connectSql("$tmpquery1");
		$msg = "addToSite";
		$id = $project;
	}

	$removeToSiteNote = Util::returnGlobal('removeToSiteNote','GET');
	if ($removeToSiteNote == "true") 
	{
		$multi = strstr($id,"**");
		
		if ($multi != "") 
		{
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["notes"]." SET published='1' WHERE id IN($id)";
		} 
		else 
		{
			$tmpquery1 = "UPDATE ".$tableCollab["notes"]." SET published='1' WHERE id = '".Util::fixInt($id)."'";
		}
		
		Util::connectSql("$tmpquery1");
		$msg = "removeToSite";
		$id = $project;
	}

}

if ($msg == "demo") 
{
	$id = $project;
}

$tmpquery = "WHERE pro.id = '$id'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);
$comptProjectDetail = count($projectDetail->pro_id);

if ($comptProjectDetail == "0") 
{
	Util::headerFunction("../projects/listprojects.php?msg=blankProject&".session_name()."=".session_id());
	exit;
}

$tmpquery = "WHERE tas.project = '$id' ORDER BY tas.name";
$listTasksTime = new Request();
$listTasksTime->openTasks($tmpquery);
$comptListTasksTime = count($listTasksTime->tas_id);
if ($comptListTasksTime != "0") 
{
	for ($i=0;$i<$comptListTasksTime;$i++) 
	{
		$estimated_time = $estimated_time + $listTasksTime->tas_estimated_time[$i];
		$actual_time = $actual_time + $listTasksTime->tas_actual_time[$i];

		if ($listTasksTime->tas_complete_date[$i] != "" && $listTasksTime->tas_complete_date[$i] != "--" && $listTasksTime->tas_due_date[$i] != "--") 
		{
			$diff = Util::diffDate($listTasksTime->tas_complete_date[$i],$listTasksTime->tas_due_date[$i]);
			$diff_time = $diff_time + $diff;
		}
	}

	if ($diff_time > 0) 
	{
		$diff_time = "<b>+$diff_time</b>";
	}
}

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$id' AND tea.member = '".Util::fixInt($idSession)."'";
$memberTest = new Request();
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

if ($enableHelpSupport == "true" && ($teamMember == "true" || $profilSession == "5")) 
{
	$tmpquery = "WHERE sr.status = '0' AND sr.project = '".$projectDetail->pro_id[0]."'";
	$listNewRequests = new Request();
	$listNewRequests->openSupportRequests($tmpquery);
	$comptListNewRequests = count($listNewRequests->sr_id);

	$tmpquery = "WHERE sr.status = '1' AND sr.project = '".$projectDetail->pro_id[0]."'";
	$listOpenRequests = new Request();
	$listOpenRequests->openSupportRequests($tmpquery);
	$comptListOpenRequests = count($listOpenRequests->sr_id);

	$tmpquery = "WHERE sr.status = '2' AND sr.project = '".$projectDetail->pro_id[0]."'";
	$listCompleteRequests = new Request();
	$listCompleteRequests->openSupportRequests($tmpquery);
	$comptListCompleteRequests = count($listCompleteRequests->sr_id);
}

$setTitle .= " : View Project (" . $projectDetail->pro_name[0] . ")";

include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($projectDetail->pro_name[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") 
{
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$blockPage->bornesNumber = "4";

$idStatus = $projectDetail->pro_status[0];
$idPriority = $projectDetail->pro_priority[0];

$block1 = new Block();

$block1->form = "pdD";
$block1->openForm("../projects/listprojects.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->headingToggle($strings["project"]." : ".$projectDetail->pro_name[0]);

if ($idSession == $projectDetail->pro_owner[0] || $enable_cvs == "true" || $profilSession == "0" || $profilSession == "5") 
{
	$block1->openPaletteIcon();

	if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5") 
	{
		$block1->paletteIcon(0,"remove",$strings["delete"]);
		$block1->paletteIcon(1,"copy",$strings["copy"]);
		$block1->paletteIcon(2,"export",$strings["export"]);
		$block1->paletteIcon(3,"edit",$strings["edit"]);
	}

	if ($enable_cvs == "true") 
	{
		$block1->paletteIcon(4,"cvs",$strings["browse_cvs"]);
	}
	
	//if mantis bug tracker enabled
	if ($enableMantis == "true") 
	{
		$block1->paletteIcon(5,"bug",$strings["bug"]);
	}

	$block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"],$projectDetail->pro_name[0]);
$block1->contentRow($strings["project_id"],$projectDetail->pro_id[0]);
$block1->contentRow($strings["priority"],"<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt=\"\"> ".$priority[$idPriority]);

//List open phases and link to phase details
if ($projectDetail->pro_phase_set[0] != "0")
{
	$tmpquery = "WHERE pha.project_id = '$id' AND status = '1' ORDER BY pha.order_num";
	$currentPhase = new Request();
	$currentPhase->openPhases($tmpquery);
	$comptCurrentPhase = count($currentPhase->pha_id);

	if ($comptCurrentPhase == 0)
	{
		$block1->contentRow($strings["current_phase"],$strings["no_current_phase"]);
	}
	else
	{
		for ($i=0;$i<$comptCurrentPhase;$i++)
		{
			if ($i != $comptCurrentPhase)
			{
				 $pnum = $i+1;
				 $phasesList .= "$pnum.<a href=\"../phases/viewphase.php?$transmitSid&id=".$currentPhase->pha_id[$i]."\">".$currentPhase->pha_name[$i]."</a>  ";
			}
		}
		$block1->contentRow($strings["current_phase"],$phasesList);
	}
}
else
{
	$block1->contentRow($strings["phase_enabled"],$strings["false"]);
}

$block1->contentRow($strings["description"],nl2br($projectDetail->pro_description[0]));
$block1->contentRow($strings["url_dev"],$blockPage->buildLink($projectDetail->pro_url_dev[0],$projectDetail->pro_url_dev[0],out));
$block1->contentRow($strings["url_prod"],$blockPage->buildLink($projectDetail->pro_url_prod[0],$projectDetail->pro_url_prod[0],out));
$block1->contentRow($strings["owner"],$blockPage->buildLink("../users/viewuser.php?id=".$projectDetail->pro_mem_id[0],$projectDetail->pro_mem_name[0],in)." (".$blockPage->buildLink($projectDetail->pro_mem_email_work[0],$projectDetail->pro_mem_login[0],mail).")");
$block1->contentRow($strings["created"],Util::createDate($projectDetail->pro_created[0],$timezoneSession));
$block1->contentRow($strings["modified"],Util::createDate($projectDetail->pro_modified[0],$timezoneSession));

if ($projectDetail->pro_org_id[0] == "1") 
{
	$block1->contentRow($strings["organization"],$strings["none"]);
} 
else 
{
	$block1->contentRow($strings["organization"],$blockPage->buildLink("../clients/viewclient.php?id=".$projectDetail->pro_org_id[0],$projectDetail->pro_org_name[0],in));
}

$block1->contentRow($strings["status"],$status[$idStatus]);

if ($fileManagement == "true") 
{
	$block1->contentRow($strings["max_upload"].$blockPage->printHelp("max_file_size"),Util::convertSize($projectDetail->pro_upload_max[0]));
	$block1->contentRow($strings["project_folder_size"].$blockPage->printHelp("project_disk_space"),Util::convertSize(Util::folderInfoSize("../files/".$projectDetail->pro_id[0]."/")));
}

$block1->contentRow($strings["estimated_time"],$estimated_time." ".$strings["hours"]);
$block1->contentRow($strings["actual_time"],$actual_time." ".$strings["hours"]);
$block1->contentRow($strings["scope_creep"].$blockPage->printHelp("project_scope_creep"),$diff_time." ".$strings["days"]);

if ($sitePublish == "true") 
{
	if ($projectDetail->pro_published[0] == "1") 
	{
		$block1->contentRow($strings["project_site"],"&lt;".$blockPage->buildLink("../projects/addprojectsite.php?id=$id",$strings["create"]."...",in)."&gt;");
	} 
	else 
	{
		$block1->contentRow($strings["project_site"],"&lt;".$blockPage->buildLink("../projects/viewprojectsite.php?id=$id",$strings["details"],in)."&gt;");
	}
}

if ($enableInvoicing == "true" && ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5")) 
{
	if ($projectDetail->pro_invoicing[0] == "1") 
	{
		$block1->contentRow($strings["invoicing"],$strings["true"]);
	} 
	else 
	{
		$block1->contentRow($strings["invoicing"],$strings["false"]);
	}

	$block1->contentRow($strings["hourly_rate"],$projectDetail->pro_hourly_rate[0]);
}

if ($enableHelpSupport == "true" && ($teamMember == "true"  || $profilSession == "5") && $supportType == "team") 
{
	$block1->contentTitle($strings["support"]);
	$block1->contentRow($strings["new_requests"],"$comptListNewRequests - ".$blockPage->buildLink("../support/support.php?action=new&project=".$projectDetail->pro_id[0],$strings["manage_new_requests"],in));
	$block1->contentRow($strings["open_requests"],"$comptListOpenRequests - ".$blockPage->buildLink("../support/support.php?action=open&project=".$projectDetail->pro_id[0],$strings["manage_open_requests"],in));
	$block1->contentRow($strings["closed_requests"],"$comptListCompleteRequests - ".$blockPage->buildLink("../support/support.php?action=complete&project=".$projectDetail->pro_id[0],$strings["manage_closed_requests"],in));
}

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($idSession == $projectDetail->pro_owner[0] || $enable_cvs == "true" || $profilSession == "0" || $profilSession == "5") 
{
	$block1->openPaletteScript();

	if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5") 
	{
		$block1->paletteScript(0,"remove","../projects/deleteproject.php?id=$id","true,true,false",$strings["delete"]);
		$block1->paletteScript(1,"copy","../projects/editproject.php?id=".$projectDetail->pro_id[0]."&docopy=true","true,true,false",$strings["copy"]);
		$block1->paletteScript(2,"export","../projects/exportproject.php?languageSession=$languageSession&type=project&id=".$projectDetail->pro_id[0]."","true,true,false",$strings["export"]);
		$block1->paletteScript(3,"edit","../projects/editproject.php?id=".$projectDetail->pro_id[0]."&docopy=false","true,true,false",$strings["edit"]);
	}

	if ($enable_cvs == "true") 
	{
		$block1->paletteScript(4,"cvs","../browsecvs/browsecvs.php?id=$id","true,true,false",$strings["browse_cvs"]);
	}
	
	if ($enableMantis == "true") 
	{
		$block1->paletteScript(5,"bug",$pathMantis."login.php?id=".$projectDetail->pro_id[0]."&url=http://{$HTTP_HOST}{$REQUEST_URI}&username=$loginSession&password=$passwordSession","true,true,false",$strings["bug"]);
	}

	$block1->closePaletteScript("","");
}

//Phase or Task list block
if ($projectDetail->pro_phase_set[0] != "0")
{
	$block7 = new Block();
	$block7->form = "wbSe";
	$block7->openForm("../projects/viewproject.php?id=$id&".session_name()."=".session_id()."#".$block7->form."Anchor");
	$block7->headingToggle($strings["phases"]);
	$block7->openPaletteIcon();

	$block7->paletteIcon(0,"info",$strings["view"]);

	if ($teamMember == "true" || $profilSession == "5") 
	{
		if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5") 
		{
			$block7->paletteIcon(1,"edit",$strings["edit"]);
		}
	}
	
	$block7->closePaletteIcon();

	$block7->sorting("phases",$sortingUser->sor_phases[0],"pha.order_num ASC",$sortingFields = array(0=>"pha.order_num",1=>"pha.name",2=>"none",3=>"none",4=>"pha.status",5=>"pha.date_start",6=>"pha.date_end"));

	$tmpquery = "WHERE pha.project_id = '$id' ORDER BY $block7->sortingValue";
	$listPhases = new Request();
	$listPhases->openPhases($tmpquery);
	$comptListPhases = count($listPhases->pha_id);

	if ($comptListPhases != "0") 
	{
		$block7->openResults();
		$block7->labels($labels = array(0=>$strings["order"],1=>$strings["name"],2=>$strings["total_tasks"],3=>$strings["uncomplete_tasks"],4=>$strings["status"],5=>$strings["date_start"],6=>$strings["date_end"]),"false");

		$tmpquery = "WHERE tas.project = '$id'";
		$countPhaseTasks = new Request();
		$countPhaseTasks->openTasks($tmpquery);
		$comptlistTasks = count($countPhaseTasks->tas_id);

		for ($i=0;$i<$comptListPhases;$i++) 
		{

			$comptlistTasksRow = "0";
			$comptUncompleteTasks = "0";

			for ($k=0;$k<$comptlistTasks;$k++) 
			{
				if ($listPhases->pha_order_num[$i] == $countPhaseTasks->tas_parent_phase[$k]) 
				{
					$comptlistTasksRow = $comptlistTasksRow + 1;
					
					if ($countPhaseTasks->tas_status[$k] == "2" || $countPhaseTasks->tas_status[$k] == "3" || $countPhaseTasks->tas_status[$k] == "4") 
					{
						$comptUncompleteTasks = $comptUncompleteTasks + 1;
					}
				}
			}

			$block7->openRow();
			$block7->checkboxRow($listPhases->pha_id[$i]);
			$block7->cellRow($listPhases->pha_order_num[$i]);
			$block7->cellRow($blockPage->buildLink("../phases/viewphase.php?id=".$listPhases->pha_id[$i],$listPhases->pha_name[$i],in));
			$block7->cellRow($comptlistTasksRow);
			$block7->cellRow($comptUncompleteTasks);
			$block7->cellRow($phaseStatus[$listPhases->pha_status[$i]]);
			$block7->cellRow($listPhases->pha_date_start[$i]);
			$block7->cellRow($listPhases->pha_date_end[$i]);
			$block7->closeRow();
		}

		$block7->closeResults();
	} 
	else 
	{
		$block7->noresults();
	}

	$block7->closeToggle();
	$block7->closeFormResults();

	$block7->openPaletteScript();
	$block7->paletteScript(0,"info","../phases/viewphase.php?","false,true,true",$strings["view"]);

	if ($teamMember == "true" || $profilSession == "5") 
	{
		if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5") 
		{
			$block7->paletteScript(1,"edit","../phases/editphase.php?","false,true,true",$strings["edit"]);
		}
	}

	$block7->closePaletteScript($comptListPhases,$listPhases->pha_id);

}
else
{
	$block2 = new Block();
	$block2->form = "wbTuu";
	$block2->openForm("../projects/viewproject.php?".session_name()."=".session_id()."&id=".$projectDetail->pro_id[0]."#".$block2->form."Anchor");

	$block2->headingToggle($strings["tasks"]);

	$block2->openPaletteIcon();

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block2->paletteIcon(0,"add",$strings["add"]);
		$block2->paletteIcon(1,"remove",$strings["delete"]);
		$block2->paletteIcon(2,"copy",$strings["copy"]);
		//$block2->paletteIcon(3,"export",$strings["export"]);

		if ($sitePublish == "true") 
		{
			$block2->paletteIcon(4,"add_projectsite",$strings["add_project_site"]);
			$block2->paletteIcon(5,"remove_projectsite",$strings["remove_project_site"]);
		}
	}

	$block2->paletteIcon(6,"info",$strings["view"]);
	
	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block2->paletteIcon(7,"edit",$strings["edit"]);
	}

	$block2->closePaletteIcon();

	$block2->borne = $blockPage->returnBorne("1");
	$block2->rowsLimit = "5";

	$block2->sorting("project_tasks",$sortingUser->sor_project_tasks[0],"tas.name ASC",$sortingFields = array(0=>"tas.name",1=>"tas.priority",2=>"tas.status",3=>"tas.completion",4=>"tas.due_date",5=>"mem.login",6=>"tas.published"));

	$tmpquery = "WHERE tas.project = '$id' ORDER BY $block2->sortingValue";

	$block2->recordsTotal = Util::computeTotal($initrequest["tasks"]." ".$tmpquery);

	$listTasks = new Request();
	$listTasks->openTasks($tmpquery,$block2->borne,$block2->rowsLimit);
	$comptListTasks = count($listTasks->tas_id);

	if ($comptListTasks != "0") 
	{
		$block2->openResults();
		$block2->labels($labels = array(0=>$strings["name"],1=>$strings["priority"],2=>$strings["status"],3=>$strings["completion"],4=>$strings["due_date"],5=>$strings["assigned_to"],6=>$strings["published"]),"true");

		for ($i=0;$i<$comptListTasks;$i++) 
		{
			if ($listTasks->tas_due_date[$i] == "") 
			{
				$listTasks->tas_due_date[$i] = $strings["none"];
			}

			$idStatus = $listTasks->tas_status[$i];
			$idPriority = $listTasks->tas_priority[$i];
			$idPublish = $listTasks->tas_published[$i];
			$complValue = ($listTasks->tas_completion[$i]>0) ? $listTasks->tas_completion[$i]."0 %": $listTasks->tas_completion[$i]." %"; 
			$block2->openRow();
			$block2->checkboxRow($listTasks->tas_id[$i]);
			$block2->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=".$listTasks->tas_id[$i],$listTasks->tas_name[$i],in));
			$block2->cellRow("<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt=\"\"> ".$priority[$idPriority]);
			$block2->cellRow($status[$idStatus]);
			$block2->cellRow($complValue);

			if ($listTasks->tas_due_date[$i] <= $date && $listTasks->tas_completion[$i] != "10") 
			{
				$block2->cellRow("<b>".$listTasks->tas_due_date[$i]."</b>");
			} 
			else 
			{
				$block2->cellRow($listTasks->tas_due_date[$i]);
			}
			
			if ($listTasks->tas_start_date[$i] != "--" && $listTasks->tas_due_date[$i] != "--") 
			{
				$gantt = "true";
			}
			
			if ($listTasks->tas_assigned_to[$i] == "0") 
			{
				$block2->cellRow($strings["unassigned"]);
			} 
			else 
			{
				$block2->cellRow($blockPage->buildLink($listTasks->tas_mem_email_work[$i],$listTasks->tas_mem_login[$i],mail));
			}

			if ($sitePublish == "true") 
			{
				$block2->cellRow($statusPublish[$idPublish]);
			}
			
			$block2->closeRow();
		}
		
		$block2->closeResults();
		$block2->bornesFooter("1",$blockPage->bornesNumber,"../tasks/listtasks.php?project=$id&","id=$id");

		if ($activeJpgraph == "true" && $gantt == "true") 
		{
			echo "
				<div id='ganttChart_taskList' class='ganttChart'>
					<img src='../tasks/graphtasks.php?".session_name()."=".session_id()."&project=".$projectDetail->pro_id[0]."' alt=''><br/>
					<span class='listEvenBold''>".$blockPage->buildLink("http://www.aditus.nu/jpgraph/","JpGraph",powered)."</span>	
				</div>
			";
		}

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
		$block2->paletteScript(0,"add","../tasks/edittask.php?project=".$projectDetail->pro_id[0]."","true,false,false",$strings["add"]);
		$block2->paletteScript(1,"remove","../tasks/deletetasks.php?project=".$projectDetail->pro_id[0]."","false,true,true",$strings["delete"]);
		$block2->paletteScript(2,"copy","../tasks/edittask.php?project=".$projectDetail->pro_id[0]."&docopy=true","false,true,false",$strings["copy"]);
		//$block2->paletteScript(3,"export","../projects/exportproject.php?","false,true,true",$strings["export"]);
		if ($sitePublish == "true") 
		{
			$block2->paletteScript(4,"add_projectsite","../projects/viewproject.php?addToSiteTask=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
			$block2->paletteScript(5,"remove_projectsite","../projects/viewproject.php?removeToSiteTask=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
		}
	}

	$block2->paletteScript(6,"info","../tasks/viewtask.php?","false,true,false",$strings["view"]);
	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block2->paletteScript(7,"edit","../tasks/edittask.php?project=".$projectDetail->pro_id[0]."","false,true,true",$strings["edit"]);
	}

	$block2->closePaletteScript($comptListTasks,$listTasks->tas_id);
}

$block3 = new Block();
$block3->form = "pdH";
$block3->openForm("../projects/viewproject.php?id=$id&".session_name()."=".session_id()."#".$block3->form."Anchor");
$block3->headingToggle($strings["discussions"]);
$block3->openPaletteIcon();

if ($teamMember == "true" || $profilSession == "5") 
{
	$block3->paletteIcon(0,"add",$strings["add"]);
}

if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") 
{
	$block3->paletteIcon(1,"remove",$strings["delete"]);
	$block3->paletteIcon(2,"lock",$strings["close"]);
	
	if ($sitePublish == "true") 
	{
		$block3->paletteIcon(3,"add_projectsite",$strings["add_project_site"]);
		$block3->paletteIcon(4,"remove_projectsite",$strings["remove_project_site"]);
	}
}

$block3->paletteIcon(5,"info",$strings["view"]);
$block3->closePaletteIcon();
$block3->borne = $blockPage->returnBorne("2");
$block3->rowsLimit = "5";
$block3->sorting("project_discussions",$sortingUser->sor_project_discussions[0],"topic.last_post DESC",$sortingFields = array(0=>"topic.subject",1=>"mem.login",2=>"topic.posts",3=>"topic.last_post",4=>"topic.status",5=>"topic.published"));

$tmpquery = "WHERE topic.project = '$id' ORDER BY $block3->sortingValue";

$block3->recordsTotal = Util::computeTotal($initrequest["topics"]." ".$tmpquery);

$listTopics = new Request();
$listTopics->openTopics($tmpquery,$block3->borne,$block3->rowsLimit);
$comptListTopics = count($listTopics->top_id);

if ($comptListTopics != "0") 
{
	$block3->openResults();

	$block3->labels($labels = array(0=>$strings["topic"],1=>$strings["owner"],2=>$strings["posts"],3=>$strings["last_post"],4=>$strings["status"],5=>$strings["published"]),"true");

	for ($i=0;$i<$comptListTopics;$i++) 
	{
		$idStatus = $listTopics->top_status[$i];
		$idPublish = $listTopics->top_published[$i];
		$block3->openRow();
		$block3->checkboxRow($listTopics->top_id[$i]);
		$block3->cellRow($blockPage->buildLink("../topics/viewtopic.php?id=".$listTopics->top_id[$i],$listTopics->top_subject[$i],in));
		$block3->cellRow($blockPage->buildLink($listTopics->top_mem_email_work[$i],$listTopics->top_mem_login[$i],mail));
		$block3->cellRow($listTopics->top_posts[$i]);
		
		if ($listTopics->top_last_post[$i] > $lastvisiteSession) 
		{
			$block3->cellRow("<b>".Util::createDate($listTopics->top_last_post[$i],$timezoneSession)."</b>");
		} 
		else 
		{
			$block3->cellRow(Util::createDate($listTopics->top_last_post[$i],$timezoneSession));
		}
		
		$block3->cellRow($statusTopic[$idStatus]);
		
		if ($sitePublish == "true") 
		{
			$block3->cellRow($statusPublish[$idPublish]);
		}

		$block3->closeRow();
	}
	
	$block3->closeResults();
	$block3->bornesFooter("2",$blockPage->bornesNumber,"../topics/listtopics.php?project=$id&","id=$id");

	} 
	else 
	{
		$block3->noresults();
	}

	$block3->closeToggle();
	$block3->closeFormResults();
	$block3->openPaletteScript();

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block3->paletteScript(0,"add","../topics/addtopic.php?project=".$projectDetail->pro_id[0]."","true,false,false",$strings["add"]);
	}

	if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") 
	{
		$block3->paletteScript(1,"remove","../topics/deletetopics.php?project=".$projectDetail->pro_id[0]."","false,true,true",$strings["delete"]);
		$block3->paletteScript(2,"lock","../projects/viewproject.php?closeTopic=true&project=$id&action=publish","false,true,true",$strings["close"]);
		
		if ($sitePublish == "true") 
		{
			$block3->paletteScript(3,"add_projectsite","../projects/viewproject.php?addToSiteTopic=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
			$block3->paletteScript(4,"remove_projectsite","../projects/viewproject.php?".session_name()."=".session_id()."&removeToSiteTopic=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
		}
	}

$block3->paletteScript(5,"info","../topics/viewtopic.php?","false,true,false",$strings["view"]);
$block3->closePaletteScript($comptListTopics,$listTopics->top_id);

$block4 = new Block();
$block4->form = "pdM";
$block4->openForm("../projects/viewproject.php?".session_name()."=".session_id()."&id=".$projectDetail->pro_id[0]."#".$block4->form."Anchor");
$block4->headingToggle($strings["team"]);
$block4->openPaletteIcon();

if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") 
{
	$block4->paletteIcon(0,"add",$strings["add"]);
	$block4->paletteIcon(1,"remove",$strings["delete"]);

	if ($sitePublish == "true") 
	{
		$block4->paletteIcon(2,"add_projectsite",$strings["add_project_site"]);
		$block4->paletteIcon(3,"remove_projectsite",$strings["remove_project_site"]);
	}
}

$block4->paletteIcon(4,"info",$strings["view"]);
$block4->paletteIcon(5,"email",$strings["email"]);
$block4->closePaletteIcon();
$block4->borne = $blockPage->returnBorne("3");
$block4->rowsLimit = "5";
$block4->sorting("team",$sortingUser->sor_team[0],"mem.name ASC",$sortingFields = array(0=>"mem.name",1=>"mem.title",2=>"mem.login",3=>"mem.phone_work",4=>"log.connected",5=>"tea.published"));

$tmpquery = "WHERE tea.project = '$id' AND mem.profil != '3' ORDER BY $block4->sortingValue";

$block4->recordsTotal = Util::computeTotal($initrequest["teams"]." ".$tmpquery);

$listTeam = new Request();
$listTeam->openTeams($tmpquery,$block4->borne,$block4->rowsLimit);
$comptListTeam = count($listTeam->tea_id);

$block4->openResults();
$block4->labels($labels = array(0=>$strings["full_name"],1=>$strings["title"],2=>$strings["user_name"],3=>$strings["work_phone"],4=>$strings["connected"],5=>$strings["published"]),"true");

for ($i=0;$i<$comptListTeam;$i++) 
{
	if ($listTeam->tea_mem_phone_work[$i] == "") 
	{
		$listTeam->tea_mem_phone_work[$i] = $strings["none"];
	}

	if ($listTeam->tea_mem_title[$i] == "") 
	{
		$listTeam->tea_mem_title[$i] = $strings["none"];
	}

	$idPublish = $listTeam->tea_published[$i];
	$block4->openRow();
	$block4->checkboxRow($listTeam->tea_mem_id[$i]);
	$block4->cellRow($blockPage->buildLink("../users/viewuser.php?id=".$listTeam->tea_mem_id[$i],$listTeam->tea_mem_name[$i],in));	
	$block4->cellRow($listTeam->tea_mem_title[$i]);
	$block4->cellRow($blockPage->buildLink($listTeam->tea_mem_email_work[$i],$listTeam->tea_mem_login[$i],mail));
	$block4->cellRow($listTeam->tea_mem_phone_work[$i]);

	if ($listTeam->tea_log_connected[$i] > $dateunix-5*60) 
	{
		$block4->cellRow($strings["yes"]." ".$z);
	} 
	else 
	{
		$block4->cellRow($strings["no"]);
	}

	if ($sitePublish == "true") 
	{
		$block4->cellRow($statusPublish[$idPublish]);
	}
	
	$block4->closeRow();
}

$block4->closeResults();
$block4->bornesFooter("3",$blockPage->bornesNumber,"../teams/listusers.php?id=$id&","id=$id");
$block4->closeToggle();
$block4->closeFormResults();
$block4->openPaletteScript();

if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") 
{
	$block4->paletteScript(0,"add","../teams/adduser.php?project=".$projectDetail->pro_id[0]."","true,true,true",$strings["add"]);
	$block4->paletteScript(1,"remove","../teams/deleteusers.php?project=".$projectDetail->pro_id[0]."","false,true,true",$strings["delete"]);
	
	if ($sitePublish == "true") 
	{
	$block4->paletteScript(2,"add_projectsite","../projects/viewproject.php?addToSiteTeam=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
	$block4->paletteScript(3,"remove_projectsite","../projects/viewproject.php?removeToSiteTeam=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
	}
}

$block4->paletteScript(4,"info","../users/viewuser.php?","false,true,false",$strings["view"]);
$block4->paletteScript(5,"email","../users/emailusers.php?","false,true,true",$strings["email"]); 
$block4->closePaletteScript($comptListTeam,$listTeam->tea_mem_id);

/**
 * Begin Linked Content
 */
if ($fileManagement == "true") 
{

	$block5 = new Block();
	$block5->form = "tdC";
	$block5->openForm("../projects/viewproject.php?".session_name()."=".session_id()."&id=$id#".$block5->form."Anchor");
	$block5->headingToggle($strings["linked_content"]);
	$block5->openPaletteIcon();

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block5->paletteIcon(0,"add",$strings["add"]);
		$block5->paletteIcon(1,"remove",$strings["delete"]);
	
		if ($sitePublish == "true") 
		{
			$block5->paletteIcon(2,"add_projectsite",$strings["add_project_site"]);
			$block5->paletteIcon(3,"remove_projectsite",$strings["remove_project_site"]);
		}
	}

	$block5->paletteIcon(4,"info",$strings["view"]);

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block5->paletteIcon(5,"edit",$strings["edit"]);
	}
	
	$block5->closePaletteIcon();
	$block5->sorting("files",$sortingUser->sor_files[0],"fil.name ASC",$sortingFields = array(0=>"fil.extension",1=>"fil.name",2=>"fil.owner",3=>"fil.date",4=>"fil.status",5=>"fil.published"));

	$tmpquery = "WHERE fil.project = '$id' AND fil.task = '0' AND fil.vc_parent = '0' AND fil.phase = '0' ORDER BY $block5->sortingValue";
	$listFiles = new Request();
	$listFiles->openFiles($tmpquery);
	$comptListFiles = count($listFiles->fil_id);

	if ($comptListFiles != "0") 
	{
		$block5->openResults();
		$block5->labels($labels=array(0=>$strings["type"],1=>$strings["name"],2=>$strings["owner"],3=>$strings["date"],4=>$strings["approval_tracking"],5=>$strings["published"]),"true");

		for ($i=0;$i<$comptListFiles;$i++)
		{
			$existFile = "false";
			$idStatus = $listFiles->fil_status[$i];

			$idPublish = $listFiles->fil_published[$i];
			$type = FileHandler::fileInfoType( $listFiles->fil_extension[$i]);
			
			if (file_exists("../files/".$listFiles->fil_project[$i]."/".$listFiles->fil_name[$i])) 
			{
				$existFile = "true";
			}
			
			$block5->openRow();
			$block5->checkboxRow($listFiles->fil_id[$i]);

			if ($existFile == "true") 
			{
				$block5->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$type,icone));

			} 
			else 
			{
				$block5->cellRow("&nbsp;");
			}

			if ($existFile == "true") 
			{
				$block5->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$listFiles->fil_name[$i],in));
			} 
			else 
			{
				$block5->cellRow($strings["missing_file"]." (".$listFiles->fil_name[$i].")");
			}
			
			$block5->cellRow($blockPage->buildLink($listFiles->fil_mem_email_work[$i],$listFiles->fil_mem_login[$i],mail));
			$block5->cellRow($listFiles->fil_date[$i]);
			$block5->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$statusFile[$idStatus],in));
			
			if ($sitePublish == "true") 
			{
				$block5->cellRow($statusPublish[$idPublish]);
			}
			
			$block5->closeRow();
		}
		
		$block5->closeResults();
	} 
	else 
	{
		$block5->noresults();
	}

	$block5->closeToggle();
	$block5->closeFormResults();
	$block5->openPaletteScript();

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block5->paletteScript(0,"add","../linkedcontent/addfile.php?project=$id","true,true,true",$strings["add"]);
		$block5->paletteScript(1,"remove","../linkedcontent/deletefiles.php?project=$id","false,true,true",$strings["delete"]);
		
		if ($sitePublish == "true") 
		{
			$block5->paletteScript(2,"add_projectsite","../projects/viewproject.php?addToSiteFile=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
			$block5->paletteScript(3,"remove_projectsite","../projects/viewproject.php?removeToSiteFile=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
		}
	}

	$block5->paletteScript(4,"info","../linkedcontent/viewfile.php?","false,true,false",$strings["view"]);

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block5->paletteScript(5,"edit","../linkedcontent/viewfile.php?edit=true","false,true,false",$strings["edit"]);
	}
	
	$block5->closePaletteScript($comptListFiles,$listFiles->fil_id);
}
/**
 * End Linked Content
 * -------------------
 * Begin Notes Section
 */
$block6 = new Block();
$block6->form = "wbJ";
$block6->openForm("../projects/viewproject.php?".session_name()."=".session_id()."&id=".$projectDetail->pro_id[0]."#".$block6->form."Anchor");
$block6->headingToggle($strings["notes"]);
$block6->openPaletteIcon();

if ($teamMember == "true" || $profilSession == "5") 
{
	$block6->paletteIcon(0,"add",$strings["add"]);
	$block6->paletteIcon(1,"remove",$strings["delete"]);
	//$block6->paletteIcon(2,"export",$strings["export"]);
	if ($sitePublish == "true") 
	{
		$block6->paletteIcon(3,"add_projectsite",$strings["add_project_site"]);
		$block6->paletteIcon(4,"remove_projectsite",$strings["remove_project_site"]);
	}
}

$block6->paletteIcon(5,"info",$strings["view"]);
if ($teamMember == "true" || $profilSession == "5")
{
	$block6->paletteIcon(6,"edit",$strings["edit"]);
}

$block6->closePaletteIcon();
$block6->borne = $blockPage->returnBorne("4");
$block6->rowsLimit = "5";

$comptTopic = count($topicNote);

if ($comptTopic != "0") 
{
	$block6->sorting("notes",$sortingUser->sor_notes[0],"note.date DESC",$sortingFields = array(0=>"note.subject",1=>"note.topic",2=>"note.date",3=>"mem.login",4=>"note.published"));
} 
else 
{
	$block6->sorting("notes",$sortingUser->sor_notes[0],"note.date DESC",$sortingFields = array(0=>"note.subject",1=>"note.date",2=>"mem.login",3=>"note.published"));
}

$tmpquery = "WHERE note.project = '$id' ORDER BY $block6->sortingValue";

$block6->recordsTotal = Util::computeTotal($initrequest["notes"]." ".$tmpquery);
$listNotes = new Request();
$listNotes->openNotes($tmpquery,$block6->borne,$block6->rowsLimit);
$comptListNotes = count($listNotes->note_id);

if ($comptListNotes != "0") 
{
	$block6->openResults();

	if ($comptTopic != "0") 
	{
		$block6->labels($labels = array(0=>$strings["subject"],1=>$strings["topic"],2=>$strings["date"],3=>$strings["owner"],4=>$strings["published"]),"true");
	} 
	else 
	{
		$block6->labels($labels = array(0=>$strings["subject"],1=>$strings["date"],2=>$strings["owner"],3=>$strings["published"]),"true");
	}
	
	for ($i=0;$i<$comptListNotes;$i++) 
	{
		$idPublish = $listNotes->note_published[$i];
		$block6->openRow();
		$block6->checkboxRow($listNotes->note_id[$i]);
		$block6->cellRow($blockPage->buildLink("../notes/viewnote.php?id=".$listNotes->note_id[$i],$listNotes->note_subject[$i],in));
		
		if ($comptTopic != "0") 
		{
			$block6->cellRow($topicNote[$listNotes->note_topic[$i]]);
		}

		$block6->cellRow($listNotes->note_date[$i]);
		$block6->cellRow($blockPage->buildLink($listNotes->note_mem_email_work[$i],$listNotes->note_mem_login[$i],mail));
		
		if ($sitePublish == "true") 
		{
			$block6->cellRow($statusPublish[$idPublish]);
		} 
		else 
		{
			$block6->cellRow("&nbsp;");
		}
		
		$block6->closeRow();
	}
	
	$block6->closeResults();
	$block6->bornesFooter("4",$blockPage->bornesNumber,"../notes/listnotes.php?project=$id&","id=$id");
} 
else 
{
	$block6->noresults();
}

$block6->closeToggle();
$block6->closeFormResults();
$block6->openPaletteScript();

if ($teamMember == "true" || $profilSession == "5") 
{
	$block6->paletteScript(0,"add","../notes/editnote.php?project=".$projectDetail->pro_id[0]."","true,true,true",$strings["add"]);
	$block6->paletteScript(1,"remove","../notes/deletenotes.php?project=".$projectDetail->pro_id[0]."","false,true,true",$strings["delete"]);
	//$block6->paletteScript(2,"export","../projects/exportproject.php?","false,true,true",$strings["export"]);
	if ($sitePublish == "true") 
	{
		$block6->paletteScript(3,"add_projectsite","../projects/viewproject.php?addToSiteNote=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
		$block6->paletteScript(4,"remove_projectsite","../projects/viewproject.php?removeToSiteNote=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
	}
}

$block6->paletteScript(5,"info","../notes/viewnote.php?","false,true,false",$strings["view"]);

if ($teamMember == "true" || $profilSession == "5") 
{
	$block6->paletteScript(6,"edit","../notes/editnote.php?project=".$projectDetail->pro_id[0]."","false,true,false",$strings["edit"]);
}

$block6->closePaletteScript($comptListNotes,$listNotes->note_id);
/**
 * End Notes section
 */

include '../themes/'.THEME.'/footer.php';
?>