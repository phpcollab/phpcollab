<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/deleteprojectsite.php

$checkSession = "true";
include_once '../includes/library.php';

if ($action == "delete") {
	$tmpquery = "UPDATE ".$tableCollab["projects"]." SET published='1' WHERE id = '$project'";
	Util::connectSql("$tmpquery");
	Util::headerFunction("../projects/viewproject.php?id=$project&msg=removeProjectSite");
	exit;
}

include '../themes/' . THEME . '/header.php';

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewprojectsite.php?id=".$projectDetail->pro_id[0],$strings["project_site"],in));
$blockPage->itemBreadcrumbs($strings["delete_projectsite"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "projectsite_delete";
$block1->openForm("../projects/deleteprojectsite.php?action=delete&project=$project");

$block1->heading($strings["delete_projectsite"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$block1->contentRow("",$projectDetail->pro_name[0]);

$block1->contentRow("","<input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>