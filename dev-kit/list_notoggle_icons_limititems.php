<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../dev-kit/list_notoggle_icons_limititems.php

$checkSession = "true";
include_once('../includes/library.php');

include('../themes/'.THEME.'/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?",$strings["organizations"],in));
$blockPage->itemBreadcrumbs($strings["organizations"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$blockPage->bornesNumber = "2";

$block1 = new Block();

$block1->form = "clientList";
$block1->openForm("../clients/listclients.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->heading($strings["organizations"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0,"add",$strings["add"]);
$block1->paletteIcon(1,"remove",$strings["delete"]);
$block1->closePaletteIcon();

$block1->borne = $blockPage->returnBorne("1");
$block1->rowsLimit = "5";

$block1->sorting("organizations",$sortingUser->sor_organizations[0],"org.name ASC",$sortingFields = array(0=>"org.name",1=>"org.phone",2=>"org.url"));

$tmpquery = "WHERE org.id != '1' ORDER BY $block1->sortingValue";

$block1->recordsTotal = Util::computeTotal($initrequest["organizations"]." ".$tmpquery);

$listOrganizations = new Request();
$listOrganizations->openOrganizations($tmpquery,$block1->borne,$block1->rowsLimit);
$comptListOrganizations = count($listOrganizations->org_id);

if ($comptListOrganizations != "0") {
$block1->openResults();
$block1->labels($labels = array(0=>$strings["name"],1=>$strings["phone"],2=>$strings["url"]),"false");

for ($i=0;$i<$comptListOrganizations;$i++) {
$block1->openRow();
$block1->checkboxRow($listOrganizations->org_id[$i]);
$block1->cellRow($blockPage->buildLink("../clients/viewclient.php?id=".$listOrganizations->org_id[$i],$listOrganizations->org_name[$i],in));
$block1->cellRow($listOrganizations->org_phone[$i]);
$block1->cellRow($blockPage->buildLink($listOrganizations->org_url[$i],$listOrganizations->org_url[$i],out));
$block1->closeRow();
}
$block1->closeResults();

$block1->bornesFooter("1",$blockPage->bornesNumber,"../dev-kit/list_notoggle_icons_limititems.php?","project=$project");

} else {
$block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0,"add","../clients/editclient.php?","true,false,false",$strings["add"]);
$block1->paletteScript(1,"remove","../clients/deleteclients.php?","false,true,true",$strings["delete"]);
$block1->closePaletteScript($comptListOrganizations,$listOrganizations->org_id);

$block2 = new Block();

$block2->form = "clientList2";
$block2->openForm("../clients/listclients.php?".session_name()."=".session_id()."#".$block2->form."Anchor");

$block2->heading($strings["organizations"]);

$block2->openPaletteIcon();
$block2->paletteIcon(0,"add",$strings["add"]);
$block2->paletteIcon(1,"remove",$strings["delete"]);
$block2->closePaletteIcon();

$block2->borne = $blockPage->returnBorne("2");
$block2->rowsLimit = "1";

$block2->sorting("organizations",$sortingUser->sor_organizations[0],"org.name ASC",$sortingFields = array(0=>"org.name",1=>"org.phone",2=>"org.url"));

$tmpquery = "WHERE org.id != '1' ORDER BY $block2->sortingValue";

$block2->recordsTotal = Util::computeTotal($initrequest["organizations"]." ".$tmpquery);

$listOrganizations2 = new Request();
$listOrganizations2->openOrganizations($tmpquery,$block2->borne,$block2->rowsLimit);
$comptlistOrganizations2 = count($listOrganizations2->org_id);

if ($comptlistOrganizations2 != "0") {
$block2->openResults();
$block2->labels($labels = array(0=>$strings["name"],1=>$strings["phone"],2=>$strings["url"]),"false",$sorting="false",$sortingOff = array(0=>"2",1=>"DESC"));

for ($i=0;$i<$comptlistOrganizations2;$i++) {
$block2->openRow();
$block2->checkboxRow($listOrganizations2->org_id[$i]);
$block2->cellRow($blockPage->buildLink("../clients/viewclient.php?id=".$listOrganizations2->org_id[$i],$listOrganizations2->org_name[$i],in));
$block2->cellRow($listOrganizations2->org_phone[$i]);
$block2->cellRow($blockPage->buildLink($listOrganizations2->org_url[$i],$listOrganizations2->org_url[$i],out));
$block2->closeRow();
}
$block2->closeResults();

$block2->bornesFooter("2",$blockPage->bornesNumber,"","project=$project");

} else {
$block2->noresults();
}
$block2->closeFormResults();

$block2->openPaletteScript();
$block2->paletteScript(0,"add","../clients/editclient.php?","true,false,false",$strings["add"]);
$block2->paletteScript(1,"remove","../clients/deleteclients.php?","false,true,true",$strings["delete"]);
$block2->closePaletteScript($comptlistOrganizations2,$listOrganizations2->org_id);

include('../themes/'.THEME.'/footer.php');
?>