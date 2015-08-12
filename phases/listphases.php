<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../phases/listphases.php

$checkSession = "true";
include_once('../includes/library.php');

include('../themes/'.THEME.'/header.php');

$tmpquery = "WHERE pro.id = '$id'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$id' AND tea.member = '$idSession'";
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
$blockPage->itemBreadcrumbs($strings["phases"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

if ($teamMember == "true" || $profilSession == "5"){
$block7 = new Block();
$block7->form = "wbSe";
$block7->openForm("../phases/listphases.php?id=$id&".session_name()."=".session_id()."#".$block7->form."Anchor");
$block7->headingToggle($strings["phases"]);
$block7->openPaletteIcon();

$block7->paletteIcon(0,"info",$strings["view"]);

if ($teamMember == "true" || $profilSession == "5") {
if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5") {
	$block7->paletteIcon(1,"edit",$strings["edit"]);
}
}
$block7->closePaletteIcon();

$block7->sorting("phases",$sortingUser->sor_phases[0],"pha.order_num ASC",$sortingFields = array(0=>"pha.order_num",1=>"pha.name",2=>"none",3=>"none",4=>"pha.status",5=>"pha.date_start",6=>"pha.date_end"));

$tmpquery = "WHERE pha.project_id = '$id' ORDER BY $block7->sortingValue";
$listPhases = new Request();
$listPhases->openPhases($tmpquery);
$comptListPhases = count($listPhases->pha_id);

if ($comptListPhases != "0") {
	$block7->openResults();
	$block7->labels($labels = array(0=>$strings["order"],1=>$strings["name"],2=>$strings["total_tasks"],3=>$strings["uncomplete_tasks"],4=>$strings["status"],5=>$strings["date_start"],6=>$strings["date_end"]),"false");

$tmpquery = "WHERE tas.project = '$id'";
$countPhaseTasks = new Request();
$countPhaseTasks->openTasks($tmpquery);
$comptlistTasks = count($countPhaseTasks->tas_id);

for ($i=0;$i<$comptListPhases;$i++) {

$comptlistTasksRow = "0";
$comptUncompleteTasks = "0";
for ($k=0;$k<$comptlistTasks;$k++) {
	if ($listPhases->pha_order_num[$i] == $countPhaseTasks->tas_parent_phase[$k]) {
		$comptlistTasksRow = $comptlistTasksRow + 1;
		if ($countPhaseTasks->tas_status[$k] == "2" || $countPhaseTasks->tas_status[$k] == "3" || $countPhaseTasks->tas_status[$k] == "4") {
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
} else {
$block7->noresults();
}
$block7->closeToggle();
$block7->closeFormResults();

$block7->openPaletteScript();
$block7->paletteScript(0,"info","../phases/viewphase.php?","false,true,true",$strings["view"]);
if ($teamMember == "true" || $profilSession == "5") {
if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "0" || $profilSession == "5") {
$block7->paletteScript(1,"edit","../phases/editphase.php?","false,true,true",$strings["edit"]);
}
}
$block7->closePaletteScript($comptListPhases,$listPhases->pha_id);
}

include('../themes/'.THEME.'/footer.php');
?>