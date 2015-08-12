<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../teams/deleteusers.php

$checkSession = "true";
include_once('../includes/library.php');

if ($enable_cvs == "true") {
include("../includes/cvslib.php");
}

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);
$comptProjectDetail = count($projectDetail->pro_id);

//test exists selected project, redirect to list if not
if ($comptProjectDetail == "0") {
	Util::headerFunction("../projects/listprojects.php?msg=blank&".session_name()."=".session_id());
	exit;
}

if ($action == "delete") {
$id = str_replace("**",",",$id);

	if ($htaccessAuth == "true") {
		$Htpasswd = new Htpasswd;
		$Htpasswd->initialize("../files/".$projectDetail->pro_id[0]."/.htpasswd");

		$tmpquery = "WHERE mem.id IN($id)";
		$listMembers = new Request();
		$listMembers->openMembers($tmpquery);
		$comptListMembers = count($listMembers->mem_id);

		for ($i=0;$i<$comptListMembers;$i++) {
			$Htpasswd->deleteUser($listMembers->mem_login[$i]);
		}
	}
//if mantis bug tracker enabled	
	if ($enableMantis == "true") {
	//  include mantis library
		include( "../mantis/core_API.php" );
	}

$multi = strstr($id,",");
	if ($multi != "") {
	$pieces = explode(",",$id);
	$compt = count($pieces);
		for ($i=0;$i<$compt;$i++) {
			if ($projectDetail->pro_owner[0] != $pieces[$i]) {
				$tmpquery1 = "DELETE FROM ".$tableCollab["teams"]." WHERE member = '$pieces[$i]' AND project = '$project'";
				Util::connectSql("$tmpquery1");

//if mantis bug tracker enabled
			if ($enableMantis == "true") {
// Unassign multiple user from this project in mantis
				$f_project_id = $project;
				$f_user_id = $pieces[$i];
				include("../mantis/user_proj_delete.php");
			}

//if CVS repository enabled
				if ($enable_cvs == "true") {
					$user_query = "WHERE mem.id = '$pieces[$i]'";
					$cvsMember = new Request();
					$cvsMember->openMembers($user_query);
					cvs_delete_user($cvsMember->mem_login[$i], $project);
				}
			}
			if ($projectDetail->pro_owner[0] == $pieces[$i]) {
				$foundOwner = "true";
			}
		}
		if ($foundOwner == "true") {
			$msg = "deleteTeamOwnerMix";
		} else {
			$msg = "delete";
		}
	} else {
		$tmpquery1 = "DELETE FROM ".$tableCollab["teams"]." WHERE member = '$id' AND project = '$project'";
		if ($projectDetail->pro_owner[0] == $id) {
			$msg = "deleteTeamOwner";
		} else {
			Util::connectSql("$tmpquery1");
			$msg = "delete";

//if mantis bug tracker enabled
		if ($enableMantis == "true") {
// Unassign single user from this project in mantis
			$f_project_id = $project;
			$f_user_id = $id;
			include("../mantis/user_proj_delete.php");
		}

//if CVS repository enabled
			if ($enable_cvs == "true") {
				$user_query = "WHERE mem.id = '$id'";
				$cvsMember = new Request();
				$cvsMember->openMembers($user_query);
				cvs_delete_user($cvsMember->mem_login[0], $project);
			}
		}
	}

//$tmpquery3 = "UPDATE ".$tableCollab["tasks"]." SET assigned_to='0' WHERE assigned_to IN($id) AND assigned_to != '$projectDetail->pro_owner[0]'";
//Util::connectSql("$tmpquery3");

if ($notifications == "true") {
$organization = "1";
	include("../teams/noti_removeprojectteam.php");
}
	Util::headerFunction("../projects/viewproject.php?id=$project&msg=$msg&".session_name()."=".session_id());
}

include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($strings["remove_team"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "crM";
$block1->openForm("../teams/deleteusers.php?project=$project&action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["remove_team"]);

$block1->openContent();
$block1->contentTitle($strings["remove_team_info"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE mem.id IN($id) ORDER BY mem.name";
$listMembers = new Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);

for ($i=0;$i<$comptListMembers;$i++) {
$block1->contentRow("#".$listMembers->mem_id[$i],$listMembers->mem_login[$i]." (".$listMembers->mem_name[$i].")");
}

$block1->contentRow("","<input type=\"SUBMIT\" value=\"".$strings["remove"]."\">&#160;<input type=\"BUTTON\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>