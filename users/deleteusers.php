<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../users/deleteusers.php

$checkSession = "true";
include_once('../includes/library.php');

//CVS library
include("../includes/cvslib.php");

if ($action == "delete") {
	if ($at == "0") {
		$atProject = "1";
	} else {
		$atProject = $at;
	}

	$id = str_replace("**",",",$id);
	$tmpquery1 = "DELETE FROM ".$tableCollab["members"]." WHERE id IN($id)";
	$tmpquery2 = "UPDATE ".$tableCollab["projects"]." SET owner='$atProject' WHERE owner IN($id)";
	$tmpquery3 = "UPDATE ".$tableCollab["tasks"]." SET assigned_to='$at' WHERE assigned_to IN($id)";
	$tmpquery4 = "UPDATE ".$tableCollab["assignments"]." SET assigned_to='$at',assigned='$dateheure' WHERE assigned_to IN($id)";
	$tmpquery5 = "DELETE FROM ".$tableCollab["sorting"]." WHERE member IN($id)";
	$tmpquery6 = "DELETE FROM ".$tableCollab["notifications"]." WHERE member IN($id)";
	$tmpquery7 = "DELETE FROM ".$tableCollab["teams"]." WHERE member IN($id)";

	$tmpquery = "WHERE pro.owner IN($id)";
	$listProjects = new Request();
	$listProjects->openProjects($tmpquery);
	$comptListProjects = count($listProjects->pro_id);
	for ($i=0;$i<$comptListProjects;$i++) {
			$listTeams->tea_id = "";
			$listTeams->tea_project = "";
			$listTeams->tea_member = "";
			$listTeams->tea_published = "";
			$listTeams->tea_authorized = "";
			$listTeams->tea_mem_login = "";
			$listTeams->tea_pro_id = "";

			$tmpquery = "WHERE tea.project = '".$listProjects->pro_id[$i]."' AND tea.member = '$atProject'";
			$listTeams = new Request();
			$listTeams->openTeams($tmpquery);
			$comptListTeams = count($listTeams->tea_id);
				if ($comptListTeams == "0") {
					$tmpquery = "INSERT INTO ".$tableCollab["teams"]."(project,member,published,authorized) VALUES('".$listProjects->pro_id[$i]."','$atProject','1','0')";

					Util::connectSql("$tmpquery");
				}
	}

//if CVS repository enabled
	if ($enable_cvs == "true") {
		$pieces = explode(",",$id);
		for ($j=0;$j<(count($pieces));$j++) {

//remove the users from every repository
			$listTeams->tea_id = "";
			$listTeams->tea_project = "";
			$listTeams->tea_member = "";
			$listTeams->tea_published = "";
			$listTeams->tea_authorized = "";
			$listTeams->tea_mem_login = "";
			$listTeams->tea_pro_id = "";

			$tmpquery = "WHERE tea.member = '$pieces[$j]'";
			$listTeams = new Request();
			$listTeams->openTeams($tmpquery);
			$comptListTeams = count($listTeams->tea_id);
			for ($i=0;$i<$comptListTeams;$i++) {
				cvs_delete_user($listTeams->tea_mem_login[$i], $listTeams->tea_pro_id[$i]);
			}
		}
	}
	Util::connectSql("$tmpquery1");
	Util::connectSql("$tmpquery2");
	Util::connectSql("$tmpquery3");
	Util::connectSql("$tmpquery4");
	Util::connectSql("$tmpquery5");
	Util::connectSql("$tmpquery6");
	Util::connectSql("$tmpquery7");
//if mantis bug tracker enabled
	if ($enableMantis == "true") {
// Call mantis function to remove user
		include ("../mantis/user_delete.php");
	}

	Util::headerFunction("../users/listusers.php?msg=delete&".session_name()."=".session_id());
	exit;
}

include('../themes/'.THEME.'/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/listusers.php?",$strings["user_management"],in));
$blockPage->itemBreadcrumbs($strings["delete_users"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "user_delete";
$block1->openForm("../users/deleteusers.php?action=delete&".session_name()."=".session_id());

$block1->heading($strings["delete_users"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE mem.id IN($id) ORDER BY mem.name";
$listMembers = new Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);


for ($i=0;$i<$comptListMembers;$i++) {
	echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$listMembers->mem_login[$i]."&nbsp;(".$listMembers->mem_name[$i].")</td></tr>";
}

$tmpquery = "SELECT pro.id FROM ".$tableCollab["projects"]." pro WHERE pro.owner IN($id)";
Util::computeTotal($tmpquery);
$totalProjects = $countEnregTotal;

$tmpquery = "SELECT tas.id FROM ".$tableCollab["tasks"]." tas WHERE tas.assigned_to IN($id)";
Util::computeTotal($tmpquery);

$totalTasks = $countEnregTotal;

$block1->contentTitle($strings["reassignment_user"]);

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$strings["there"]." $totalProjects ".$strings["projects"]." ".$strings["owned_by"]."</td></tr>
<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$strings["there"]." $totalTasks ".$strings["tasks"]." ".$strings["owned_by"]."</td></tr>
<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><b>".$strings["reassign_to"]." : </b> ";

$tmpquery = "WHERE mem.profil != '3' AND mem.id NOT IN($id) ORDER BY mem.name";
$reassign = new Request();
$reassign->openMembers($tmpquery);
$comptReassign = count($reassign->mem_id);

echo "<select name=\"at\">
<option value=\"0\" selected>".$strings["unassigned"]."</option>";

for ($i=0;$i<$comptReassign;$i++) {
echo "<option value=\"".$reassign->mem_id[$i]."\">".$reassign->mem_login[$i]." / ".$reassign->mem_name[$i]."</option>";
}

echo "</select></td></tr>
<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\"><input type=\"hidden\" value=\"$id\" name=\"id\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>