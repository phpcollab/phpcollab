<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../calendar/deletecalendar.php
** Authors: Ceam / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: deletecalendar.php
**
** DESC: screen: delete calendar item from db
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**	check for better template usage
**
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';

if ($action == "delete") {
	$id = str_replace("**",",",$id);
	$tmpquery1 = "DELETE FROM ".$tableCollab["calendar"]." WHERE id IN($id)";
	Util::connectSql("$tmpquery1");
	Util::headerFunction("../calendar/viewcalendar.php?msg=delete");
	exit;
}

$setTitle .= " : Delete Calendar";
if (strpos($id, "**") !== false) {
    $setTitle .= " Entries";
} else { 
    $setTitle .= " Entry";
} 
    
include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?",$strings["calendar"],in));
$blockPage->itemBreadcrumbs($strings["delete"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}


$block1 = new Block();
$block1->form = "saP";
$block1->openForm("../calendar/deletecalendar.php?project=$project&action=delete&id=$id");

$block1->heading($strings["delete"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE cal.id IN($id) ORDER BY cal.subject";
$listCalendar = new Request();
$listCalendar->openCalendar($tmpquery);
$comptListCalendar = count($listCalendar->cal_id);

for ($i=0;$i<$comptListCalendar;$i++) {
echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">#".$listCalendar->cal_id[$i]."</td><td>".$listCalendar->cal_shortname[$i]."</td></tr>";
}

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>