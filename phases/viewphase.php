<?php

/*
** Application name: phpCollab
** Last Edit page: 19/05/2005
** Path by root:  ../phases/viewphase.php
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
** FILE: viewphase.php
**
** DESC: Screen: view phase info
**
** HISTORY:
**	19/05/2005	-	fixed and &amp; in link
**  24/05/2005	-	added http://www.php-collab.org/community/viewtopic.php?p=7144#7144
** -----------------------------------------------------------------------------
** TO-DO:
**
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

if ($action == "publish") {
if ($addToSite == "true") {
$multi = strstr($id,"**");
if ($multi != "") {
$id = str_replace("**",",",$id);
$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='0' WHERE id IN($id)";
} else {
$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='0' WHERE id = '$id'";
}
Util::connectSql("$tmpquery1");
$msg = "addToSite";
$id = $phase;
}

if ($removeToSite == "true") {
$multi = strstr($id,"**");
if ($multi != "") {
$id = str_replace("**",",",$id);
$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='1' WHERE id IN($id)";
} else {
$tmpquery1 = "UPDATE ".$tableCollab["tasks"]." SET published='1' WHERE id = '$id'";
}
Util::connectSql("$tmpquery1");
$msg = "removeToSite";
$id = $phase;
}
if ($addToSiteFile == "true") {
$multi = strstr($id,"**");
if ($multi != "") {
$id = str_replace("**",",",$id);
$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='0' WHERE id IN($id)";
} else {
$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='0' WHERE id = '$id'";
}
Util::connectSql("$tmpquery1");
$msg = "addToSite";
$id = $phase;
}

if ($removeToSiteFile == "true") {
$multi = strstr($id,"**");
if ($multi != "") {
$id = str_replace("**",",",$id);
$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='1' WHERE id IN($id)";
} else {
$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='1' WHERE id = '$id'";
}
Util::connectSql("$tmpquery1");
$msg = "removeToSite";
$id = $phase;
}
}

include '../themes/'.THEME.'/header.php';

$tmpquery = "WHERE pha.id = '$id'";
$phaseDetail = new Request();
$phaseDetail->openPhases($tmpquery);
$project = $phaseDetail->pha_project_id[0];
$phase = $phaseDetail->pha_id[0];

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
$memberTest = new Request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);
	if ($comptMemberTest == "0") {
		$teamMember = "false";
	} else {
		$teamMember = "true";
	}

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=".$projectDetail->pro_id[0],$strings["phases"],in));
$blockPage->itemBreadcrumbs($phaseDetail->pha_name[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();
$block1->form = "pppD";
$block1->openForm("../projects/listprojects.php?".session_name()."=".session_id()."#".$block1->form."Anchor");
$block1->headingToggle($strings["phase"]." : ".$phaseDetail->pha_name[0]);

if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5") {
$block1->openPaletteIcon();
$block1->paletteIcon(0,"edit",$strings["edit"]);
$block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"],$phaseDetail->pha_name[0]);
$block1->contentRow($strings["phase_id"],$phaseDetail->pha_id[0]);
$block1->contentRow($strings["status"],$phaseStatus[$phaseDetail->pha_status[0]]);

$parentPhase = $phaseDetail->pha_order_num[0];
$tmpquery = "WHERE tas.project = '$project' AND tas.parent_phase = '$parentPhase'";
$countPhaseTasks = new Request();
$countPhaseTasks->openTasks($tmpquery);
$comptlistTasks = count($countPhaseTasks->tas_id);

$comptlistTasksRow = "0";
$comptUncompleteTasks = "0";
for ($k=0;$k<$comptlistTasks;$k++) {
	if ($countPhaseTasks->tas_status[$k] == "2" || $countPhaseTasks->tas_status[$k] == "3" || $countPhaseTasks->tas_status[$k] == "4") {
	$comptUncompleteTasks = $comptUncompleteTasks + 1;
	}
}

$block1->contentRow($strings["total_tasks"],$comptlistTasks);
$block1->contentRow($strings["uncomplete_tasks"],$comptUncompleteTasks);
$block1->contentRow($strings["date_start"],$phaseDetail->pha_date_start[0]);
$block1->contentRow($strings["date_end"],$phaseDetail->pha_date_end[0]);
$block1->contentRow($strings["comments"],nl2br($phaseDetail->pha_comments[0]));

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5") {
$block1->openPaletteScript();
$block1->paletteScript(0,"edit","../phases/editphase.php?id=$id","true,true,true",$strings["edit"]);
$block1->closePaletteScript($comptListPhaese,$listPhases->pha_id);
}

$block2 = new Block();

$block2->form = "saP";
$block2->openForm("../phases/viewphase.php?".session_name()."=".session_id()."&id=$id#".$block2->form."Anchor");

$block2->headingToggle($strings["tasks"]);

$block2->openPaletteIcon();
if ($teamMember == "true" || $profilSession == "5") {
	$block2->paletteIcon(0,"add",$strings["add"]);
	$block2->paletteIcon(1,"remove",$strings["delete"]);
	$block2->paletteIcon(2,"copy",$strings["copy"]);
	//$block1->paletteIcon(3,"export",$strings["export"]);
	if ($sitePublish == "true") {
		$block2->paletteIcon(4,"add_projectsite",$strings["add_project_site"]);
		$block2->paletteIcon(5,"remove_projectsite",$strings["remove_project_site"]);
	}
}

$block2->paletteIcon(6,"info",$strings["view"]);
if ($teamMember == "true" || $profilSession == "5") {
	$block2->paletteIcon(7,"edit",$strings["edit"]);
}
$block2->closePaletteIcon();

$block2->sorting("tasks",$sortingUser->sor_tasks[0],"tas.name ASC",$sortingFields = array(0=>"tas.name",1=>"tas.priority",2=>"tas.status",3=>"tas.completion",4=>"tas.due_date",5=>"mem.login",6=>"tas.published"));

$tmpquery = "WHERE tas.project = '$project' AND tas.parent_phase = '$parentPhase' ORDER BY $block2->sortingValue";
$listTasks = new Request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

if ($comptListTasks != "0") {
	$block2->openResults();
	$block2->labels($labels = array(0=>$strings["task"],1=>$strings["priority"],2=>$strings["status"],3=>$strings["completion"],4=>$strings["due_date"],5=>$strings["assigned_to"],6=>$strings["published"]),"true");

for ($i=0;$i<$comptListTasks;$i++) {
if ($listTasks->tas_due_date[$i] == "") {
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
if ($listTasks->tas_due_date[$i] <= $date) {
	$block2->cellRow("<b>".$listTasks->tas_due_date[$i]."</b>");
} else {
	$block2->cellRow($listTasks->tas_due_date[$i]);
}
if ($listTasks->tas_start_date[$i] != "--" && $listTasks->tas_due_date[$i] != "--") {
$gantt = "true";
}
if ($listTasks->tas_assigned_to[$i] == "0") {
$block2->cellRow($strings["unassigned"]);
} else {
$block2->cellRow($blockPage->buildLink($listTasks->tas_mem_email_work[$i],$listTasks->tas_mem_login[$i],mail));
}
echo "</td>";
if ($sitePublish == "true") {
$block2->cellRow($statusPublish[$idPublish]);
}
$block2->closeRow();
}
$block2->closeResults();

if ($activeJpgraph == "true" && $gantt == "true") {
	echo "
		<div id='ganttChart_taskList' class='ganttChart'>
			<img src='graphtasks.php?".session_name()."=".session_id()."&project=".$projectDetail->pro_id[0]."&phase=".$phaseDetail->pha_order_num[0]."' alt=''><br/>
			<span class='listEvenBold''>".$blockPage->buildLink("http://www.aditus.nu/jpgraph/","JpGraph",powered)."</span>	
		</div>
	";
}
} else {
$block2->noresults();
}
$block2->closeToggle();
$block2->closeFormResults();

$block2->openPaletteScript();
if ($teamMember == "true" || $profilSession == "5") {
$block2->paletteScript(0,"add","../tasks/edittask.php?project=$project&phase=".$phaseDetail->pha_order_num[0]."","true,true,true",$strings["add"]);
$block2->paletteScript(1,"remove","../tasks/deletetasks.php?project=$project","false,true,true",$strings["delete"]);
$block2->paletteScript(2,"copy","../tasks/edittask.php?project=$project&docopy=true","false,true,false",$strings["copy"]);
//$block1->paletteScript(3,"export","export.php?","false,true,true",$strings["export"]);
if ($sitePublish == "true") {
$block2->paletteScript(4,"add_projectsite","../phases/viewphase.php?addToSite=true&phase=$phase&action=publish","false,true,true",$strings["add_project_site"]);
$block2->paletteScript(5,"remove_projectsite","../phases/viewphase.php?removeToSite=true&phase=$phase&action=publish","false,true,true",$strings["remove_project_site"]);
}
}
$block2->paletteScript(6,"info","../tasks/viewtask.php?","false,true,false",$strings["view"]);
if ($teamMember == "true" || $profilSession == "5") {
$block2->paletteScript(7,"edit","../tasks/edittask.php?project=$project&phase=".$phaseDetail->pha_order_num[0]."","false,true,false",$strings["edit"]);
}
$block2->closePaletteScript($comptListTasks,$listTasks->tas_id);


if ($fileManagement == "true") 
{

	$block3 = new Block();
	$block3->form = "tdC";
	$block3->openForm("../phases/viewphase.php?".session_name()."=".session_id()."&id=$id#".$block3->form."Anchor");
	$block3->headingToggle($strings["linked_content"]);
	$block3->openPaletteIcon();

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block3->paletteIcon(0,"add",$strings["add"]);
		$block3->paletteIcon(1,"remove",$strings["delete"]);
		
		if ($sitePublish == "true") 
		{
			$block3->paletteIcon(2,"add_projectsite",$strings["add_project_site"]);
			$block3->paletteIcon(3,"remove_projectsite",$strings["remove_project_site"]);
		}
	}

	$block3->paletteIcon(4,"info",$strings["view"]);

	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block3->paletteIcon(5,"edit",$strings["edit"]);
	}

	$block3->closePaletteIcon();
	$block3->sorting("files",$sortingUser->sor_files[0],"fil.name ASC",$sortingFields = array(0=>"fil.extension",1=>"fil.name",2=>"fil.owner",3=>"fil.date",4=>"fil.status",5=>"fil.published")); 

	$tmpquery = "WHERE fil.project = '".$projectDetail->pro_id[0]."' AND fil.phase = '".$phaseDetail->pha_id[0]."' AND fil.task = '0' AND fil.vc_parent = '0' ORDER BY $block3->sortingValue";
	$listFiles = new Request();
	$listFiles->openFiles($tmpquery);
	$comptListFiles = count($listFiles->fil_id);

	if ($comptListFiles != "0") 
	{
		$block3->openResults();
		$block3->labels($labels = array(0=>$strings["type"],1=>$strings["name"],2=>$strings["owner"],3=>$strings["date"],4=>$strings["approval_tracking"],5=>$strings["published"]),"true"); 

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
			$block3->openRow();
			$block3->checkboxRow($listFiles->fil_id[$i]);
			
			if ($existFile == "true") 
			{
				$block3->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$type,icone));
			} 
			else 
			{
				$block3->cellRow("&nbsp;");
			}

			if ($existFile == "true") 
			{
				$block3->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$listFiles->fil_name[$i],in));
			} 
			else 
			{
				$block3->cellRow($strings["missing_file"]." (".$listFiles->fil_name[$i].")");
			}

			$block3->cellRow($blockPage->buildLink($listFiles->fil_mem_email_work[$i],$listFiles->fil_mem_login[$i],mail));#added
			$block3->cellRow($listFiles->fil_date[$i]);
			$block3->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$statusFile[$idStatus],in));
			
			if ($sitePublish == "true") 
			{
				$block3->cellRow($statusPublish[$idPublish]);
			}
			$block3->closeRow();
		}
		$block3->closeResults();
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
		$block3->paletteScript(0,"add","../linkedcontent/addfile.php?project=".$projectDetail->pro_id[0]."&phase=".$phaseDetail->pha_id[0]."","true,true,true",$strings["add"]);
		$block3->paletteScript(1,"remove","../linkedcontent/deletefiles.php?project=".$projectDetail->pro_id[0]."&phase=".$phaseDetail->pha_id[0]."&sendto=phasedetail","false,true,true",$strings["delete"]);
		if ($sitePublish == "true") 
		{
			$block3->paletteScript(2,"add_projectsite","../phases/viewphase.php?addToSiteFile=true&phase=".$phaseDetail->pha_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
			$block3->paletteScript(3,"remove_projectsite","../phases/viewphase.php?removeToSiteFile=true&phase=".$phaseDetail->pha_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
		}
	}

	$block3->paletteScript(4,"info","../linkedcontent/viewfile.php?","false,true,false",$strings["view"]);
	if ($teamMember == "true" || $profilSession == "5") 
	{
		$block3->paletteScript(5,"edit","../linkedcontent/viewfile.php?edit=true","false,true,false",$strings["edit"]);
	}
	$block3->closePaletteScript($comptListFiles,$listFiles->fil_id);
}

include '../themes/'.THEME.'/footer.php';
?>