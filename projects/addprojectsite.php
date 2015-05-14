<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/addprojectsite.php

$checkSession = "true";
include_once('../includes/library.php');

$tmpquery = "WHERE pro.id = '$id'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);
$comptProjectDetail = count($projectDetail->pro_id);

if ($comptProjectDetail == "0") {
	Util::headerFunction("../projects/listprojects.php?msg=blankProject&".session_name()."=".session_id());
	exit;
}
if ($idSession != $projectDetail->pro_owner[0] && $profilSession != "5") {
	Util::headerFunction("../projects/listprojects.php?msg=projectOwner&".session_name()."=".session_id());
	exit;
}

if ($action == "create") {
	$tmpquery = "UPDATE ".$tableCollab["projects"]." SET published='0' WHERE id = '$id'";
	Util::connectSql("$tmpquery");
	Util::headerFunction("../projects/viewprojectsite.php?id=$id&msg=createProjectSite&".session_name()."=".session_id());
	exit;
}

include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=$id",$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($strings["create_projectsite"]);
$blockPage->closeBreadcrumbs();

$block1 = new block();

$block1->form = "csdD";
$block1->openForm("../projects/addprojectsite.php?action=create&id=$id&".session_name()."=".session_id());

$block1->heading($strings["create_projectsite"]);

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["project"],$blockPage->buildLink("../projects/viewproject.php?id=$id",$projectDetail->pro_name[0],in));
if ($projectDetail->pro_org_id[0] == "1") {
	$block1->contentRow($strings["organization"],$strings["none"]);
} else {
	$block1->contentRow($strings["organization"],$blockPage->buildLink("../clients/viewclient.php?id=".$projectDetail->pro_org_id[0],$projectDetail->pro_org_name[0],in));

}
$block1->contentRow("","<input type=\"SUBMIT\" value=\"".$strings["create"]."\">");

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>