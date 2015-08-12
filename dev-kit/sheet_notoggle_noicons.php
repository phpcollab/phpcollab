<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../dev-kit/sheet_notoggle_noicons.php

$checkSession = "true";
include_once('../includes/library.php');

$id = Util::returnGlobal('id','GET');

$tmpquery = "WHERE org.id = '$id'";
$clientDetail = new Request();
$clientDetail->openOrganizations($tmpquery);
$comptClientDetail = count($clientDetail->org_id);

if ($comptClientDetail == "0") {
	Util::headerFunction("../clients/listclients.php?msg=blankClient&".session_name()."=".session_id());
}

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

$block1 = new Block();

$block1->form = "ecD";
$block1->openForm("../projects/listprojects.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->heading($strings["organization"]." : ".$clientDetail->org_name[0]);

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"],$clientDetail->org_name[0]);
$block1->contentRow($strings["address"],$clientDetail->org_address1[0]);
$block1->contentRow($strings["phone"],$clientDetail->org_phone[0]);
$block1->contentRow($strings["url"],$blockPage->buildLink($clientDetail->org_url[0],$clientDetail->org_url[0],out));
$block1->contentRow($strings["email"],$blockPage->buildLink($clientDetail->org_email[0],$clientDetail->org_email[0],mail));

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["comments"],nl2br($clientDetail->org_comments[0]));
$block1->contentRow($strings["created"],Util::createDate($clientDetail->org_created[0],$timezoneSession));

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>