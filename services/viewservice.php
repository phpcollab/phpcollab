<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../services/viewservice.php

$checkSession = "true";
include_once '../includes/library.php';

$id = $request->query->get("id");

if (empty($id)) {
    phpCollab\Util::headerFunction('listservices.php?msg=permissiondenied');
}

$services = $container->getServicesLoader();

if ($session->get("profile") != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$detailService = $services->getService($id);

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../services/listservices.php?", $strings["service_management"],
    "in"));
$blockPage->itemBreadcrumbs($detailService["serv_name"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "serviceD";
$block1->openForm("../services/viewservice.php#" . $block1->form . "Anchor", null, $csrfHandler);

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["service"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0, "remove", $strings["delete"]);
$block1->paletteIcon(1, "edit", $strings["edit"]);
$block1->closePaletteIcon();

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"], $detailService["serv_name"]);
$block1->contentRow($strings["name_print"], $detailService["serv_name_print"]);
$block1->contentRow($strings["hourly_rate"], $detailService["serv_hourly_rate"]);

$block1->closeContent();
$block1->closeForm();

$block1->openPaletteScript();
$block1->paletteScript(0, "remove", "../services/deleteservices.php?id=$id", "true,true,true", $strings["delete"]);
$block1->paletteScript(1, "edit", "../services/editservice.php?id=$id", "true,true,true", $strings["edit"]);
$block1->closePaletteScript(count($detailService), array_column($detailService, 'serv_id'));

include APP_ROOT . '/views/layout/footer.php';
