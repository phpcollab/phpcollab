<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../linkedcontent/deletefiles.php

$checkSession = "true";
include_once '../includes/library.php';

if ($task == "") {
	$task = "0";
}

if ($action == "delete") {
$id = str_replace("**",",",$id);
$tmpquery1 = "DELETE FROM ".$tableCollab["files"]." WHERE id IN($id) OR vc_parent IN($id)";

$tmpquery = "WHERE fil.id IN($id) OR fil.vc_parent IN($id) ORDER BY fil.name";
$listFiles = new Request();
$listFiles->openFiles($tmpquery);
$comptListFiles = count($listFiles->fil_id);
	for ($i=0;$i<$comptListFiles;$i++) {
		if ($task != "0") {
			if (file_exists ("../files/".$project."/".$task."/".$listFiles->fil_name[$i])) {
				Util::deleteFile("files/".$project."/".$task."/".$listFiles->fil_name[$i]);
			}
		} else {
			if (file_exists ("../files/".$project."/".$listFiles->fil_name[$i])) {
				Util::deleteFile("files/".$project."/".$listFiles->fil_name[$i]);
			}
		}
	}
	Util::connectSql("$tmpquery1");
	if ($sendto == "filedetails"){
		Util::headerFunction("../linkedcontent/viewfile.php?id=".$listFiles->fil_vc_parent[0]."&msg=deleteFile");
	} else {
		if ($task != "0") {
			Util::headerFunction("../tasks/viewtask.php?id=$task&msg=deleteFile");
			exit;
		} else {
			Util::headerFunction("../projects/viewproject.php?id=$project&msg=deleteFile");
			exit;
		}
	}
}

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

if ($task != "0") {
	$tmpquery = "WHERE tas.id = '$task'";
	$taskDetail = new Request();
	$taskDetail->openTasks($tmpquery);
}

include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));

if ($task != "0") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=".$projectDetail->pro_id[0],$strings["tasks"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=".$taskDetail->tas_id[0],$taskDetail->tas_name[0],in));
}

$blockPage->itemBreadcrumbs($strings["unlink_files"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "saC";
$block1->openForm("../linkedcontent/deletefiles.php?project=$project&task=$task&action=delete&id=$id&sendto=$sendto");

$block1->heading($strings["unlink_files"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE fil.id IN($id) ORDER BY fil.name";
$listFiles = new Request();
$listFiles->openFiles($tmpquery);
$comptListFiles = count($listFiles->fil_id);

for ($i=0;$i<$comptListFiles;$i++) {
	echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$listFiles->fil_name[$i]."</td></tr>";
}

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"".$strings["delete"]."\">&#160;<input type=\"BUTTON\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>