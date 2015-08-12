<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../subtasks/deletesubtasks.php
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
** FILE: deletesubtasks.php
**
** DESC: Screen:  delete a sub task 
**
** HISTORY:
**	05/11/2004	-	fixed 1059973 
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

if ($action == "delete") {
	$id = str_replace("**",",",$id);
		
	//find parent task
	$tmpquery = "WHERE subtas.id IN($id)";
	$listSubtasks = new Request();
	$listSubtasks->openSubtasks($tmpquery);

	$tmpquery1 = "DELETE FROM ".$tableCollab["subtasks"]." WHERE id IN($id)";
	$tmpquery2 = "DELETE FROM ".$tableCollab["assignments"]." WHERE subtask IN($id)";

	/*
	$tmpquery = "WHERE tas.id IN($id)";
	$listTasks = new Request();
	$listTasks->openTasks($tmpquery);
	$comptListTasks = count($listTasks->tas_id);
		for ($i=0;$i<$comptListTasks;$i++) {
			if ($fileManagement == "true") {
				Util::deleteDirectory("../files/".$listTasks->tas_project[$i]."/".$listTasks->tas_id[$i]);
			}
		}
	*/
	Util::connectSql($tmpquery1);
	Util::connectSql($tmpquery2);

	//recompute average completion of the task
	Util::taskComputeCompletion(
	$listSubtasks->subtas_task[0],
	$tableCollab["tasks"]);

	if ($task != "") {	
		Util::headerFunction("../tasks/viewtask.php?id=$task&msg=delete&".session_name()."=".session_id());
		exit;
	} else {
		Util::headerFunction("../general/home.php?msg=delete&".session_name()."=".session_id());
		exit;
	}
}

$tmpquery = "WHERE tas.id = '$task'";
$taskDetail = new Request();
$taskDetail->openTasks($tmpquery);
$project = $taskDetail->tas_project[0];

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

if ($projectDetail->pro_enable_phase[0] != "0"){
	$tPhase = $taskDetail->tas_parent_phase[0];
    if (!$tPhase){ $tPhase = '0'; }
	$tmpquery = "WHERE pha.project_id = '".$taskDetail->tas_project[0]."' AND pha.order_num = '$tPhase'";
	$targetPhase = new Request();
	$targetPhase->openPhases($tmpquery);
}	

include('../themes/'.THEME.'/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
if ($task != "") {	
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));

if ($projectDetail->pro_phase_set[0] != "0"){
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=".$projectDetail->pro_id[0],$strings["phases"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=".$targetPhase->pha_id[0],$targetPhase->pha_name[0],in));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=".$projectDetail->pro_id[0],$strings["tasks"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=".$taskDetail->tas_id[0],$taskDetail->tas_name[0],in));
$blockPage->itemBreadcrumbs($strings["delete_subtasks"]);
} else {
$blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php?",$strings["home"],in));
$blockPage->itemBreadcrumbs($strings["my_tasks"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "saP";
$block1->openForm("../subtasks/deletesubtasks.php?task=$task&action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["delete_subtasks"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE subtas.id IN($id) ORDER BY subtas.name";
$listSubtasks = new Request();
$listSubtasks->openSubtasks($tmpquery);
$comptListSubtasks = count($listSubtasks->subtas_id);

for ($i=0;$i<$comptListSubtasks;$i++) {
echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">#".$listSubtasks->subtas_id[$i]."</td><td>".$listSubtasks->subtas_name[$i]."</td></tr>";
}

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" 
value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" 
onClick=\"history.back();\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>
