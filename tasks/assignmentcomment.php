<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../tasks/assignmentcomment.php

$checkSession = "true";
include_once '../includes/library.php';

if ($action == "update") {
	$acomm = Util::convertData($acomm);
	$tmpquery6 = "UPDATE ".$tableCollab["assignments"]." SET comments='$acomm' WHERE id = '$id'";
	Util::connectSql("$tmpquery6");
	Util::headerFunction("../tasks/viewtask.php?id=$task&msg=update&".session_name()."=".session_id());
	exit;
}

$bodyCommand = "onLoad=\"document.assignment_commentForm.acomm.focus();\"";
include '../themes/' . THEME . '/header.php';

$tmpquery = "WHERE tas.id = '$task'";
$taskDetail = new Request();
$taskDetail->openTasks($tmpquery);

$tmpquery = "WHERE pro.id = '".$taskDetail->tas_project[0]."'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=".$projectDetail->pro_id[0],$strings["tasks"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=".$taskDetail->tas_id[0],$taskDetail->tas_name[0],in));
$blockPage->itemBreadcrumbs($strings["assignment_comment"]);
$blockPage->closeBreadcrumbs();

$block1 = new Block();

$block1->form = "assignment_comment";
$block1->openForm("../tasks/assignmentcomment.php?action=update&id=$id&task=$task&".session_name()."=".session_id());

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["assignment_comment"]);
    
$block1->openContent();
$block1->contentTitle($strings["assignment_comment_info"]);

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["task"]." :</td><td>".$taskDetail->tas_name[0]."</td></tr>
<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["comments"]." :</td><td><input style=\"width: 400px;\" maxlength=\"128\" size=\"44\" name=\"acomm\"></input></td></tr>
<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"Save\" value=\"".$strings["save"]."\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>