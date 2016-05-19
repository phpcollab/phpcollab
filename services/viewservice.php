<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../services/viewservice.php

$checkSession = "true";
include_once '../includes/library.php';

if ($profilSession != "0") {
	Util::headerFunction('../general/permissiondenied.php');
	exit;
}

$tmpquery = "WHERE serv.id = '$id'";
$detailService = new Request();
$detailService->openServices($tmpquery);
$comptDetailService = count($detailService->serv_id);

include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../services/listservices.php?",$strings["service_management"],in));
$blockPage->itemBreadcrumbs($detailService->serv_name[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "serviceD";
$block1->openForm("../services/viewservice.php#".$block1->form."Anchor");

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["service"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0,"remove",$strings["delete"]);
$block1->paletteIcon(1,"edit",$strings["edit"]);
$block1->closePaletteIcon();

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"],$detailService->serv_name[0]);
$block1->contentRow($strings["name_print"],$detailService->serv_name_print[0]);
$block1->contentRow($strings["hourly_rate"],$detailService->serv_hourly_rate[0]);

$block1->closeContent();
$block1->closeForm();

$block1->openPaletteScript();
$block1->paletteScript(0,"remove","../services/deleteservices.php?id=$id","true,true,true",$strings["delete"]);
$block1->paletteScript(1,"edit","../services/editservice.php?id=$id","true,true,true",$strings["edit"]);
$block1->closePaletteScript("","");

include '../themes/'.THEME.'/footer.php';
?>