<?php
/*
** Application name: phpCollab
** Last Edit page: 02/08/2007
** Path by root: ../includes/calendar.php
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
** FILE: viewuser.php
**
** DESC: Screen:	displays the details of a client user 
**
** HISTORY:
** 	02/08/2007	-	added Last Viewed Page code - Mindblender
**	
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

$tmpquery = "WHERE mem.id = '$id'";
$userDetail = new Request();
$userDetail->openMembers($tmpquery);
$comptUserDetail = count($userDetail->mem_id);

if ($userDetail->mem_profil[0] == "3") {
	Util::headerFunction("../users/viewclientuser.php?id=$id&organization=".$userDetail->mem_organization[0]."&".session_name()."=".session_id());
	exit;
}

if ($comptUserDetail == "0") {
	Util::headerFunction("../users/listusers.php?msg=blankUser&".session_name()."=".session_id());
	exit;
}

$setTitle .= " : User Management (" . $userDetail->mem_login[0] . ")";
    

include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/listusers.php?",$strings["user_management"],in));
$blockPage->itemBreadcrumbs($userDetail->mem_login[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "userD";
$block1->openForm("../users/viewuser.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["user_profile"]);

$block1->openPaletteIcon();
if ($profilSession == "0") {
if ($id != "1" && $id != "2") {
	$block1->paletteIcon(0,"remove",$strings["delete"]);
}
$block1->paletteIcon(1,"edit",$strings["edit"]);
}
$block1->paletteIcon(2,"export",$strings["export"]);
$block1->paletteIcon(3,"email",$strings["email"]); 
$block1->closePaletteIcon();

$block1->openContent();
$block1->contentTitle($strings["user_details"]);

$block1->contentRow($strings["user_name"],$userDetail->mem_login[0]);
$block1->contentRow($strings["full_name"],$userDetail->mem_name[0]);
$block1->contentRow($strings["title"],$userDetail->mem_title[0]);
$block1->contentRow($strings["email"],$blockPage->buildLink($userDetail->mem_email_work[0],$userDetail->mem_email_work[0],mail));
$block1->contentRow($strings["work_phone"],$userDetail->mem_phone_work[0]);
$block1->contentRow($strings["home_phone"],$userDetail->mem_phone_home[0]);
$block1->contentRow($strings["mobile_phone"],$userDetail->mem_mobile[0]);
$block1->contentRow($strings["fax"],$userDetail->mem_fax[0]);


if ($userDetail->mem_profil[0] == "0") {
	$permission = $strings["administrator_permissions"];
} else if ($userDetail->mem_profil[0] == "1") {
	$permission = $strings["project_manager_permissions"];
} else if ($userDetail->mem_profil[0] == "2") {
	$permission = $strings["user_permissions"];
} else if ($userDetail->mem_profil[0] == "4") {
	$permission = $strings["disabled_permissions"];
} else if ($userDetail->mem_profil[0] == "5") {
	$permission = $strings["project_manager_administrator_permissions"];
}
$block1->contentRow($strings["permissions"],$permission);

$block1->contentRow($strings["comments"],nl2br($userDetail->mem_comments[0]));
$block1->contentRow($strings["account_created"],Util::createDate($userDetail->mem_created[0],$timezoneSession));
$block1->contentRow($strings["last_page"],$userDetail->mem_last_page[0]);
$block1->contentTitle($strings["information"]);

$tmpquery = "SELECT tea.id FROM ".$tableCollab["teams"]." tea LEFT OUTER JOIN ".$tableCollab["projects"]." pro ON pro.id = tea.project WHERE tea.member = '".$userDetail->mem_id[0]."' AND pro.status IN(0,2,3)";
Util::computeTotal($tmpquery);
$valueProjects = $countEnregTotal;

$tmpquery = "SELECT tas.id FROM ".$tableCollab["tasks"]." tas LEFT OUTER JOIN ".$tableCollab["projects"]." pro ON pro.id = tas.project WHERE tas.assigned_to = '".$userDetail->mem_id[0]."' AND tas.status IN(0,2,3) AND pro.status IN(0,2,3)";
Util::computeTotal($tmpquery);
$valueTasks = $countEnregTotal;

$tmpquery = "SELECT note.id FROM ".$tableCollab["notes"]." note LEFT OUTER JOIN ".$tableCollab["projects"]." pro ON pro.id = note.project WHERE note.owner = '".$userDetail->mem_id[0]."' AND pro.status IN(0,2,3)";
Util::computeTotal($tmpquery);
$valueNotes = $countEnregTotal;

$block1->contentRow($strings["projects"],$valueProjects);
$block1->contentRow($strings["tasks"],$valueTasks);
$block1->contentRow($strings["notes"],$valueNotes);

if ($userDetail->mem_log_connected[0] > $dateunix-5*60) {
	$connected_result = $strings["yes"]." ".$z;
} else {
	$connected_result = $strings["no"];
}
$block1->contentRow($strings["connected"],$connected_result);

$block1->closeContent();
$block1->closeForm();

$block1->openPaletteScript();
if ($profilSession == "0") {
if ($id != "1" && $id != "2") {
	$block1->paletteScript(0,"remove","../users/deleteusers.php?id=$id&","true,true,true",$strings["delete"]);
}
$block1->paletteScript(1,"edit","../users/edituser.php?id=$id&","true,true,true",$strings["edit"]);
}
$block1->paletteScript(2,"export","../users/exportuser.php?id=$id&","true,true,true",$strings["export"]);
$block1->paletteScript(3,"email","../users/emailusers.php?id=$id&","true,true,true",$strings["email"]); 
$block1->closePaletteScript("","");

include '../themes/'.THEME.'/footer.php';
?>