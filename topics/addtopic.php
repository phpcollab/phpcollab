<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../topics/addtopic.php
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
** FILE: addtopic.php
**
** DESC: Screen: add new topic
**
** HISTORY:
** 	26/01/2004	-	file comment added
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

$checkSession = "true";
include_once('../includes/library.php');

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

if ($projectDetail->pro_org_id[0] == "1") {
	$projectDetail->pro_org_name[0] = $strings["none"];
}

if ($action == "add") {

	if ($pub == "") {
		$pub = "1";
	}

	$ttt = Util::convertData($ttt);
	$tpm = Util::convertData($tpm);
	$tmpquery1 = "INSERT INTO ".$tableCollab["topics"]."(project,owner,subject,status,last_post,posts,published) VALUES('$project','$idSession','$ttt','1','$dateheure','1','$pub')";
	Util::connectSql("$tmpquery1");
	$tmpquery = $tableCollab["topics"];
	Util::getLastId($tmpquery);
	$num = $lastId[0];
	unset($lastId);
	Util::autoLinks($tpm);
	$tmpquery2 = "INSERT INTO ".$tableCollab["posts"]."(topic,member,created,message) VALUES('$num','$idSession','$dateheure','$newText')";
	Util::connectSql("$tmpquery2");

	if ($notifications == "true") {
		include("../topics/noti_newtopic.php");
	}

	Util::headerFunction("../topics/viewtopic.php?project=$project&id=$num&msg=add&".session_name()."=".session_id());
}

$teamMember = "false";
$tmpquery = "WHERE tea.project = '".$projectDetail->pro_id[0]."' AND tea.member = '$idSession'";
$memberTest = new Request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);
	
if ($comptMemberTest == "0") {
	$teamMember = "false";
} else {
	$teamMember = "true";
}

if ($teamMember == "false" && $projectsFilter == "true") { 
	header("Location:../general/permissiondenied.php?".session_name()."=".session_id()); 
	exit; 
} 

$bodyCommand = "onLoad=\"document.ctTForm.ttt.focus();\"";
include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=".$projectDetail->pro_id[0],$strings["discussions"],in));
$blockPage->itemBreadcrumbs($strings["add_discussion"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "ctT";
$block1->openForm("../topics/addtopic.php?project=".$projectDetail->pro_id[0]."&action=add&".session_name()."=".session_id());

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["add_discussion"]);

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"],$blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0]." (#".$projectDetail->pro_id[0].")",in));
$block1->contentRow($strings["organization"],$projectDetail->pro_org_name[0]);
$block1->contentRow($strings["owner"],$blockPage->buildLink("../users/viewuser.php?id=".$projectDetail->pro_mem_id[0],$projectDetail->pro_mem_name[0],in)." (".$blockPage->buildLink($projectDetail->pro_mem_email_work[0],$projectDetail->pro_mem_login[0],mail).")");

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["topic"],"<input size='44' value='$ttt' style='width: 400px' name='ttt' maxlength='64' type='TEXT'>");
$block1->contentRow($strings["message"],"<textarea rows='10' style='width: 400px; height: 160px;' name='tpm' cols='47'>$tpm</textarea>");
$block1->contentRow($strings["published"],"<input size='32' value='0' name='pub' type='checkbox'>");
$block1->contentRow("","<input type='SUBMIT' value='".$strings["save"]."'>");

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>