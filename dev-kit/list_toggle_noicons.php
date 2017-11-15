<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../dev-kit/list_toggle_noicons.php

$checkSession = "true";
include_once '../includes/library.php';

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?",$strings["organizations"],in));
$blockPage->itemBreadcrumbs($strings["organizations"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "clientList";
$block1->openForm("../clients/listclients.php#".$block1->form."Anchor");

$block1->headingToggle($strings["organizations"]);

$block1->sorting("organizations",$sortingUser->sor_organizations[0],"org.name ASC",$sortingFields = array(0=>"org.name",1=>"org.phone",2=>"org.url"));

$tmpquery = "WHERE org.id != '1' ORDER BY $block1->sortingValue";
$listOrganizations = new phpCollab\Request();
$listOrganizations->openOrganizations($tmpquery);
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
} else {
$block1->noresults();
}
$block1->closeToggle();
$block1->closeFormResults();

include '../themes/'.THEME.'/footer.php';
