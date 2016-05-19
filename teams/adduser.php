<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../teams/adduser.php

$checkSession = "true";
include_once '../includes/library.php';

if ($enable_cvs == "true") {
include '../includes/cvslib.php';
}

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);
$comptProjectDetail = count($projectDetail->pro_id);

if ($comptProjectDetail == "0") {
	Util::headerFunction("../projects/listprojects.php?msg=blank");
	exit;
}

if ($action == "add") {
	$pieces = explode("**", $id);
	$id = str_replace("**",",",$id);

	if ($htaccessAuth == "true") {
		$Htpasswd = new Htpasswd;
		$Htpasswd->initialize("../files/".$projectDetail->pro_id[0]."/.htpasswd");

		$tmpquery = "WHERE mem.id IN($id)";
		$listMembers = new Request();
		$listMembers->openMembers($tmpquery);
		$comptListMembers = count($listMembers->mem_id);

		for ($i=0;$i<$comptListMembers;$i++) {
			$Htpasswd->addUser($listMembers->mem_login[$i],$listMembers->mem_password[$i]);
		}
	}
//if mantis bug tracker enabled	
	if ($enableMantis == "true") {
	//  include mantis library
		include '../mantis/core_API.php';
	}

	$comptAjout = count($pieces);
	for($i=0;$i<$comptAjout;$i++) {
		$tmpquery="INSERT INTO ".$tableCollab["teams"]."(project, member,published,authorized) VALUES ('".$projectDetail->pro_id[0]."','$pieces[$i]','1','0')";
		Util::connectSql("$tmpquery");
//if mantis bug tracker enabled
		if ($enableMantis == "true") {
			// Assign user to this project in mantis
			$f_access_level	= $team_user_level; // Developer access
			$f_project_id = $projectDetail->pro_id[0];
			$f_user_id = $pieces[$i];
			include '../mantis/user_proj_add.php';
		}	

//if CVS repository enabled
		if ($enable_cvs == "true") {
			$user_query = "WHERE mem.id = '$pieces[$i]'";
			$cvsMembers = new Request();
			$cvsMembers->openMembers($user_query);
			cvs_add_user($cvsMembers->mem_login[$i], $cvsMembers->mem_password[$i], $projectDetail->pro_id[0]);
		}
	}

if ($notifications == "true") {
$organization = "1";
	include '../teams/noti_addprojectteam.php';
}
	Util::headerFunction("../projects/viewproject.php?id=".$projectDetail->pro_id[0]."&msg=add");
}

include '../themes/' . THEME . '/header.php';

//echo "$tmpquery<br/>$comptMulti<br/>";

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../teams/listusers.php?id=".$projectDetail->pro_id[0],$strings["team_members"],in));
$blockPage->itemBreadcrumbs($strings["add_team"]);
$blockPage->closeBreadcrumbs();

$block1 = new Block();

$block1->form = "atpt";
$block1->openForm("../teams/adduser.php?project=$project#".$block1->form."Anchor");

$block1->heading($strings["add_team"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0,"add",$strings["add"]);
$block1->paletteIcon(1,"info",$strings["view"]);
$block1->paletteIcon(2,"edit",$strings["edit"]);
$block1->closePaletteIcon();

$block1->sorting("users",$sortingUser->sor_users[0],"mem.name ASC",$sortingFields = array(0=>"mem.name",1=>"mem.title",2=>"mem.login",3=>"mem.phone_work",4=>"log.connected"));

$tmpquery = "WHERE tea.project = '$project' AND mem.profil != '3'";
$concatMembers = new Request();
$concatMembers->openTeams($tmpquery);
$comptConcatMembers = count($concatMembers->tea_id);
for ($i=0;$i<$comptConcatMembers;$i++) {
	$membersTeam .= $concatMembers->tea_mem_id[$i];
		if ($i < $comptConcatMembers-1) {
			$membersTeam .= ",";
		} 
}

if ($demoMode == "true") {
$tmpquery = "WHERE mem.id NOT IN($membersTeam) AND mem.profil != '3' ORDER BY $block1->sortingValue";
} else {
$tmpquery = "WHERE mem.id NOT IN($membersTeam) AND mem.profil != '3' AND mem.id != '2' ORDER BY $block1->sortingValue";
}
$listMembers = new Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);

if ($comptListMembers != "0") {
	$block1->openResults();

	$block1->labels($labels = array(0=>$strings["full_name"],1=>$strings["title"],2=>$strings["user_name"],3=>$strings["work_phone"],4=>$strings["connected"]),"false");

for ($i=0;$i<$comptListMembers;$i++) {
if ($listMembers->mem_phone_work[$i] == "") {
	$listMembers->mem_phone_work[$i] = $strings["none"];
}
$block1->openRow();
$block1->checkboxRow($listMembers->mem_id[$i]);
$block1->cellRow($blockPage->buildLink("../users/viewuser.php?id=".$listMembers->mem_id[$i],$listMembers->mem_name[$i],in));
$block1->cellRow($listMembers->mem_title[$i]);
$block1->cellRow($blockPage->buildLink($listMembers->mem_email_work[$i],$listMembers->mem_login[$i],in));
$block1->cellRow($listMembers->mem_phone_work[$i]);
if ($listMembers->mem_log_connected[$i] > $dateunix-5*60) {
	$block1->cellRow($strings["yes"]." ".$z);
} else {
	$block1->cellRow($strings["no"]);
}
$block1->closeRow();
}
$block1->closeResults();
} else {
$block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0,"add","../teams/adduser.php?project=$project&action=add","false,true,true",$strings["add"]);
$block1->paletteScript(1,"info","../users/viewuser.php?","false,true,false",$strings["view"]);
$block1->paletteScript(2,"edit","../users/edituser.php?","false,true,false",$strings["edit"]);
$block1->closePaletteScript($comptListMembers,$listMembers->mem_id);

include '../themes/'.THEME.'/footer.php';
?>