<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../notes/deletenotes.php

$checkSession = "true";
include_once('../includes/library.php');

if ($action == "delete") {
	$id = str_replace("**",",",$id);
	$tmpquery1 = "DELETE FROM ".$tableCollab["notes"]." WHERE id IN($id)";
	Util::connectSql("$tmpquery1");
	Util::headerFunction("../projects/viewproject.php?id=$project&msg=delete&".session_name()."=".session_id());
	exit;
}

include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($strings["delete_note"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}


$block1 = new Block();
$block1->form = "saP";
$block1->openForm("../notes/deletenotes.php?project=$project&action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["delete_note"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE note.id IN($id) ORDER BY note.subject";
$listNotes = new Request();
$listNotes->openNotes($tmpquery);
$comptListNotes = count($listNotes->note_id);

for ($i=0;$i<$comptListNotes;$i++) {
$block1->contentRow("#".$listNotes->note_id[$i],$listNotes->note_subject[$i]);
}

$block1->contentRow("","<input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>