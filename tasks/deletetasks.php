<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../tasks/deletetasks.php

$checkSession = "true";
include_once('../includes/library.php');

if ($action == "delete") {
	$id = str_replace("**",",",$id);
	$tmpquery1 = "DELETE FROM ".$tableCollab["tasks"]." WHERE id IN($id)";
	$tmpquery2 = "DELETE FROM ".$tableCollab["assignments"]." WHERE task IN($id)";
	$tmpquery3 = "DELETE FROM ".$tableCollab["subtasks"]." WHERE task IN($id)";

	$tmpquery = "WHERE tas.id IN($id)";
	$listTasks = new request();
	$listTasks->openTasks($tmpquery);
	$comptListTasks = count($listTasks->tas_id);
		for ($i=0;$i<$comptListTasks;$i++) {
			if ($fileManagement == "true") {
				Util::deleteDirectory("../files/".$listTasks->tas_project[$i]."/".$listTasks->tas_id[$i]);
			}
		}
	Util::connectSql("$tmpquery1");
	Util::connectSql("$tmpquery2");
	Util::connectSql("$tmpquery3");
	
//recompute number of completed tasks of the project
	$tmpquery = "WHERE pro.id = '".$listTasks->tas_project[0]."'";
	$projectDetail = new request();
	$projectDetail->openProjects($tmpquery);

	Util::projectComputeCompletion(
	$listTasks->tas_project[$i],
	$tableCollab["projects"]);

	if ($project != "") {	
		Util::headerFunction("../projects/viewproject.php?id=$project&msg=delete&".session_name()."=".session_id());
		exit;
	} else {
		Util::headerFunction("../general/home.php?msg=delete&".session_name()."=".session_id());
		exit;
	}
}

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

include('../themes/'.THEME.'/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
if ($project != "") {	
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($strings["delete_tasks"]);
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
$block1->openForm("../tasks/deletetasks.php?project=$project&action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["delete_tasks"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE tas.id IN($id) ORDER BY tas.name";
$listTasks = new request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

for ($i=0;$i<$comptListTasks;$i++) {
echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">#".$listTasks->tas_id[$i]."</td><td>".$listTasks->tas_name[$i]."</td></tr>";
}

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" 
value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" 
onClick=\"history.back();\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>
