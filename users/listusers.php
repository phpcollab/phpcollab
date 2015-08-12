<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../users/listusers.php

$checkSession = "true";
include_once('../includes/library.php');

if ($profilSession != "0") {
	Util::headerFunction('../general/permissiondenied.php?'.session_name().'='.session_id());
	exit;
}

$setTitle .= " : List Users";

include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($strings["user_management"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "ulU";
$block1->openForm("../users/listusers.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->heading($strings["user_management"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0,"add",$strings["add"]);
$block1->paletteIcon(1,"remove",$strings["delete"]);
$block1->paletteIcon(2,"info",$strings["view"]);
$block1->paletteIcon(3,"edit",$strings["edit"]);
$block1->paletteIcon(4,"export",$strings["export"]);
$block1->paletteIcon(5,"email",$strings["email"]); 
$block1->closePaletteIcon();

$block1->sorting("users",$sortingUser->sor_users[0],"mem.name ASC",$sortingFields = array(0=>"mem.name",1=>"mem.login",2=>"mem.email_work",3=>"mem.profil",4=>"log.connected"));

if ($demoMode == "true") {
	$tmpquery = "WHERE mem.id != '1' AND mem.profil != '3' ORDER BY $block1->sortingValue";
} else {
	//$tmpquery = "WHERE mem.id != '1' AND mem.profil != '3' AND mem.id != '2' ORDER BY $block1->sortingValue";
	$tmpquery = "WHERE mem.id != '2' ORDER BY $block1->sortingValue";
}
$listMembers = new Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);

if ($comptListMembers != "0") {
	$block1->openResults();

	$block1->labels($labels = array(0=>$strings["full_name"],1=>$strings["user_name"],2=>$strings["email"],3=>$strings["profile"],4=>$strings["connected"]),"false");

for ($i=0;$i<$comptListMembers;$i++) {
$idProfil = $listMembers->mem_profil[$i];
$block1->openRow();
$block1->checkboxRow($listMembers->mem_id[$i]);
$block1->cellRow($blockPage->buildLink("../users/viewuser.php?id=".$listMembers->mem_id[$i],$listMembers->mem_name[$i],in));
$block1->cellRow($listMembers->mem_login[$i]);
$block1->cellRow($blockPage->buildLink($listMembers->mem_email_work[$i],$listMembers->mem_email_work[$i],mail));
$block1->cellRow($profil[$idProfil]);
if ($listMembers->mem_log_connected[$i] > $dateunix-5*60) {
	$block1->cellRow($strings["yes"]." ".$strings["clients_connected"]." (".$listMembers->mem_last_page[$i].")");
} else {
	$block1->cellRow($strings["no"]." (".$listMembers->mem_last_page[$i].")");
}
$block1->closeRow();
}
$block1->closeResults();
} else {
$block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0,"add","../users/edituser.php?","true,true,true",$strings["add"]);
$block1->paletteScript(1,"remove","../users/deleteusers.php?","false,true,true",$strings["delete"]);
$block1->paletteScript(2,"info","../users/viewuser.php?","false,true,false",$strings["view"]);
$block1->paletteScript(3,"edit","../users/edituser.php?","false,true,false",$strings["edit"]);
$block1->paletteScript(4,"export","../users/exportusers.php?","true,false,true",$strings["export"]);
$block1->paletteScript(5,"email","../users/emailusers.php?","false,true,true",$strings["email"]); 
$block1->closePaletteScript($comptListMembers,$listMembers->mem_id);

include '../themes/'.THEME.'/footer.php';
?>