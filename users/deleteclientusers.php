<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../users/deleteclientusers.php

$checkSession = "true";
include_once('../includes/library.php');
$tmpquery = "WHERE org.id = '$organization'";
$detailOrganization = new request();
$detailOrganization->openOrganizations($tmpquery);
$comptDetailOrganization = count($detailOrganization->org_id);

if ($action == "delete") {
	$id = str_replace("**",",",$id);
	$tmpquery1 = "DELETE FROM ".$tableCollab["members"]." WHERE id IN($id)";
	$tmpquery2 = "UPDATE ".$tableCollab["tasks"]." SET assigned_to='$at' WHERE assigned_to IN($id)";
	$tmpquery3 = "UPDATE ".$tableCollab["assignments"]." SET assigned_to='$at',assigned='$dateheure' WHERE assigned_to IN($id)";
	$tmpquery4 = "DELETE FROM ".$tableCollab["notifications"]." WHERE member IN($id)";
	$tmpquery5 = "DELETE FROM ".$tableCollab["teams"]." WHERE member IN($id)";
	Util::connectSql("$tmpquery1");
	Util::connectSql("$tmpquery2");
	Util::connectSql("$tmpquery3");
	Util::connectSql("$tmpquery4");
	Util::connectSql("$tmpquery5");
//if mantis bug tracker enabled
	if ($enableMantis == "true") {
// Call mantis function to remove user
		include ("../mantis/user_delete.php");
	}

	Util::headerFunction("../clients/viewclient.php?id=$organization&msg=delete&".session_name()."=".session_id());
	exit;
}

include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?",$strings["clients"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=".$detailOrganization->org_id[0],$detailOrganization->org_name[0],in));
$blockPage->itemBreadcrumbs($strings["delete_users"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new block();

$block1->form = "client_user_delete";
$block1->openForm("../users/deleteclientusers.php?organization=$organization&action=delete&".session_name()."=".session_id());

$block1->heading($strings["delete_users"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE mem.id IN($id) ORDER BY mem.name";

$listMembers = new request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);

for ($i=0;$i<$comptListMembers;$i++) {
echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td>".$listMembers->mem_login[$i]."&nbsp;(".$listMembers->mem_name[$i].")</td></tr>";
}

$tmpquery = "SELECT tas.id FROM ".$tableCollab["tasks"]." tas WHERE tas.assigned_to IN($id)";
Util::computeTotal($tmpquery);
$totalTasks = $countEnregTotal;

$block1->contentTitle($strings["reassignment_clientuser"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td>".$strings["there"]." $totalTasks ".$strings["tasks"]." ".$strings["owned_by"]."</td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><b>".$strings["reassign_to"]." : </b> ";

$tmpquery = "WHERE mem.profil != '3' ORDER BY mem.name";
$reassign = new request();
$reassign->openMembers($tmpquery);
$comptReassign = count($reassign->mem_id);

echo "<select name='at'>
<option value='0' selected>".$strings["unassigned"]."</option>";

for ($i=0;$i<$comptReassign;$i++) {
echo "<option value='".$reassign->mem_id[$i]."'>".$reassign->mem_login[$i]." / ".$reassign->mem_name[$i]."</option>";
}

echo "</select></td></tr>

<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><input type='submit' name='delete' value='".$strings["delete"]."'> <input type='button' name='cancel' value='".$strings["cancel"]."' onClick='history.back();'><input type='hidden' value='$id' name='id'></td></tr>";

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>