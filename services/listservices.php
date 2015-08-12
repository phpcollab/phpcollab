<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../services/listservices.php

$checkSession = "true";
include_once '../includes/library.php';

if ($profilSession != "0") {
	Util::headerFunction('../general/permissiondenied.php?'.session_name().'='.session_id());
	exit;
}

$setTitle .= " : List Services";
include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($strings["service_management"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "servList";
$block1->openForm("../services/listservices.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->heading($strings["service_management"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0,"add",$strings["add"]);
$block1->paletteIcon(1,"remove",$strings["delete"]);
$block1->paletteIcon(2,"info",$strings["view"]);
$block1->paletteIcon(3,"edit",$strings["edit"]);
$block1->closePaletteIcon();

$tmpquery = "ORDER BY serv.name ASC";
$listServices = new Request();
$listServices->openServices($tmpquery);
$comptListServices = count($listServices->serv_id);

if ($comptListServices!= "0") {
	$block1->openResults();

	$block1->labels($labels = array(0=>$strings["name"],1=>$strings["hourly_rate"]),"false",$sorting="false",$sortingOff = array(0=>"0",1=>"ASC"));

for ($i=0;$i<$comptListServices;$i++) {
$block1->openRow();
$block1->checkboxRow($listServices->serv_id[$i]);
$block1->cellRow($blockPage->buildLink("../services/viewservice.php?id=".$listServices->serv_id[$i],$listServices->serv_name[$i],in));
$block1->cellRow($listServices->serv_hourly_rate[$i]);
$block1->closeRow();
}
$block1->closeResults();
} else {
$block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0,"add","../services/editservice.php?","true,true,true",$strings["add"]);
$block1->paletteScript(1,"remove","../services/deleteservices.php?","false,true,true",$strings["delete"]);
$block1->paletteScript(2,"info","../services/viewservice.php?","false,true,false",$strings["view"]);
$block1->paletteScript(3,"edit","../services/editservice.php?","false,true,false",$strings["edit"]);
$block1->closePaletteScript($comptListServices,$listServices->serv_id);

include '../themes/'.THEME.'/footer.php';
?>