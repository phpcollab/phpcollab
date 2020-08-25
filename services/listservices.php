<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../services/listservices.php

use phpCollab\Services\Services;

$checkSession = "true";
include_once '../includes/library.php';

$services = new Services();

if ($session->get("profile") != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$setTitle .= " : List Services";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($strings["service_management"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "servList";
$block1->openForm("../services/listservices.php#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->heading($strings["service_management"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0, "add", $strings["add"]);
$block1->paletteIcon(1, "remove", $strings["delete"]);
$block1->paletteIcon(2, "info", $strings["view"]);
$block1->paletteIcon(3, "edit", $strings["edit"]);
$block1->closePaletteIcon();

$listServices = $services->getAllServices('serv.name ASC');

if ($listServices) {
    $block1->openResults();

    $block1->labels($labels = array(0 => $strings["name"], 1 => $strings["hourly_rate"]), "false", $sorting = "false", $sortingOff = array(0 => "0", 1 => "ASC"));

    foreach ($listServices as $listService) {
        $block1->openRow();
        $block1->checkboxRow($listService["serv_id"]);
        $block1->cellRow($blockPage->buildLink("../services/viewservice.php?id=" . $listService["serv_id"], $listService["serv_name"], "in"));
        $block1->cellRow($listService["serv_hourly_rate"]);
        $block1->closeRow();
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0, "add", "../services/editservice.php?", "true,true,true", $strings["add"]);
$block1->paletteScript(1, "remove", "../services/deleteservices.php?", "false,true,true", $strings["delete"]);
$block1->paletteScript(2, "info", "../services/viewservice.php?", "false,true,false", $strings["view"]);
$block1->paletteScript(3, "edit", "../services/editservice.php?", "false,true,false", $strings["edit"]);
$block1->closePaletteScript(count($listServices), array_column($listServices, 'serv_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
