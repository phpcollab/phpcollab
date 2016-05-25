<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../dev-kit/page.php

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
	$blockPage->messagebox($msgLabel);
}

//blocks here

include '../themes/'.THEME.'/footer.php';
?>