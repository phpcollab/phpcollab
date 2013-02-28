<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../dev-kit/list_notoggle_icons_nochecbox_nosorting.php

$checkSession = "true";
include_once('../includes/library.php');

include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?",$strings["organizations"],in));
$blockPage->itemBreadcrumbs($strings["organizations"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new block();

$block1->form = "clientList";
$block1->openForm("../clients/listclients.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->heading($strings["organizations"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0,"add",$strings["add"]);
$block1->paletteIcon(1,"remove",$strings["delete"]);
$block1->closePaletteIcon();

$tmpquery = "WHERE org.id != '1' ORDER BY org.url DESC";
$listOrganizations = new request();
$listOrganizations->openOrganizations($tmpquery);
$comptListOrganizations = count($listOrganizations->org_id);

if ($comptListOrganizations != "0") {
$block1->openResults($checkbox="false");
$block1->labels($labels = array(0=>$strings["name"],1=>$strings["phone"],2=>$strings["url"]),"false",$sorting="false",$sortingOff = array(0=>"2",1=>"DESC"));

for ($i=0;$i<$comptListOrganizations;$i++) {
$block1->openRow();
$block1->checkboxRow($listOrganizations->org_id[$i],$checkbox="false");
$block1->cellRow($blockPage->buildLink("../clients/viewclient.php?id=".$listOrganizations->org_id[$i],$listOrganizations->org_name[$i],in));
$block1->cellRow($listOrganizations->org_phone[$i]);
$block1->cellRow($blockPage->buildLink($listOrganizations->org_url[$i],$listOrganizations->org_url[$i],out));
$block1->closeRow();
}
$block1->closeResults();
} else {
$block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0,"add","../clients/editclient.php?","true,false,false",$strings["add"]);
$block1->paletteScript(1,"remove","../clients/deleteclients.php?","false,true,true",$strings["delete"]);
$block1->closePaletteScript($comptListOrganizations,$listOrganizations->org_id);

include('../themes/'.THEME.'/footer.php');
?>