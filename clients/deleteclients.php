<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../clients/deleteclients.php
** Authors: Ceam / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: deleteclients.php
**
** DESC: screen: delete client info
**
** HISTORY:
** 	2003-10-23	-	main page for client module
** -----------------------------------------------------------------------------
** TO-DO:
**	
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

if ($action == "delete") {
	$id = str_replace("**",",",$id);
	$tmpquery = "WHERE org.id IN($id)";
	$listOrganizations = new request();
	$listOrganizations->openOrganizations($tmpquery);
	$comptListOrganizations = count($listOrganizations->org_id);
	for ($i=0;$i<$comptListOrganizations;$i++) {
		if (file_exists("logos_clients/".$listOrganizations->org_id[$i].".".$listOrganizations->org_extension_logo[$i])) {
			@unlink("logos_clients/".$listOrganizations->org_id[$i].".".$listOrganizations->org_extension_logo[$i]);
		}
	}
	$tmpquery1 = "DELETE FROM ".$tableCollab["organizations"]." WHERE id IN($id)";
	$tmpquery2 = "UPDATE ".$tableCollab["projects"]." SET organization='1' WHERE organization IN($id)";
	$tmpquery3 = "DELETE FROM ".$tableCollab["members"]." WHERE organization IN($id)";
	Util::connectSql("$tmpquery1");
	Util::connectSql("$tmpquery2");
	Util::connectSql("$tmpquery3");
	Util::headerFunction("../clients/listclients.php?msg=delete&".session_name()."=".session_id());
}

$setTitle .= " : Delete Client";

include('../themes/'.THEME.'/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?",$strings["clients"],in));
$blockPage->itemBreadcrumbs($strings["delete_organizations"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "saP";
$block1->openForm("../clients/deleteclients.php?action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["delete_organizations"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE org.id IN($id) ORDER BY org.name";
$listOrganizations = new request();
$listOrganizations->openOrganizations($tmpquery);
$comptListOrganizations = count($listOrganizations->org_id);

for ($i=0;$i<$comptListOrganizations;$i++) {
$block1->contentRow("#".$listOrganizations->org_id[$i],$listOrganizations->org_name[$i]);
}

$block1->contentRow("","<input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();

$block1->note($strings["delete_organizations_note"]);

include('../themes/'.THEME.'/footer.php');
?>