<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../bookmarks/deletebookmarks.php
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
** FILE: deletebookmarks.php
**
** DESC: Screen: remove bookmark from db
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

$checkSession = "true";
include_once('../includes/library.php');

if ($action == "delete") {
	$id = str_replace("**",",",$id);
	$tmpquery1 = "DELETE FROM ".$tableCollab["bookmarks"]." WHERE id IN($id)";
	Util::connectSql("$tmpquery1");
	Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=delete&".session_name()."=".session_id());
	exit;
}

$setTitle .= " : Delete ";

if (strpos($id, "**") !== false) {
    $setTitle .= "Entries";
} else {
    $setTitle .= "Entry";
}
include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=all",$strings["bookmarks"],in));
$blockPage->itemBreadcrumbs($strings["delete_bookmarks"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();
$block1->form = "saP";
$block1->openForm("../bookmarks/deletebookmarks.php?action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["delete_bookmarks"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE boo.id IN($id) ORDER BY boo.name";
$listBookmarks = new Request();
$listBookmarks->openBookmarks($tmpquery);
$comptListBookmarks = count($listBookmarks->boo_id);

for ($i=0;$i<$comptListBookmarks;$i++) {
$block1->contentRow("#".$listBookmarks->boo_id[$i],$listBookmarks->boo_name[$i]);
}

$block1->contentRow("","<input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>