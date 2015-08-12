<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../reports/deletereports.php

$checkSession = "true";
include_once('../includes/library.php');

if ($action == "delete") {
	$id = str_replace("**",",",$id);
	$tmpquery1 = "DELETE FROM ".$tableCollab["reports"]." WHERE id IN($id)";
	Util::connectSql("$tmpquery1");
	Util::headerFunction("../general/home.php?msg=deleteReport&".session_name()."=".session_id());
	exit;
}

$setTitle .= " : Delete Report";
include('../themes/'.THEME.'/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/listreports.php?",$strings["my_reports"],in));
$blockPage->itemBreadcrumbs($strings["delete_reports"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "saS";
$block1->openForm("../reports/deletereports.php?action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["delete_reports"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE rep.id IN($id) ORDER BY rep.name";
$listReports = new Request();
$listReports->openReports($tmpquery);
$comptListReports = count($listReports->rep_id);

for ($i=0;$i<$comptListReports;$i++) {
$block1->contentRow("#".$listReports->rep_id[$i],$listReports->rep_name[$i]);
}

$block1->contentRow("","<input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>