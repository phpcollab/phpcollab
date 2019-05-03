<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../services/deleteservices.php

use phpCollab\Services\Services;

$checkSession = "true";
include_once '../includes/library.php';

$services = new Services();

if ($profilSession != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == "delete") {
        $id = str_replace("**", ",", $id);

        $services->deleteServices($id);
        phpCollab\Util::headerFunction("../services/listservices.php?msg=delete");
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../services/listservices.php?", $strings["service_management"], "in"));
$blockPage->itemBreadcrumbs($strings["delete_services"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "service_delete";
$block1->openForm("../services/deleteservices.php?action=delete");

$block1->heading($strings["delete_services"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$listServices = $services->getServicesByIds($id);

foreach ($listServices as $listService) {
    echo <<<TR
        <tr class="odd">
            <td class="leftvalue">&nbsp;</td>
            <td>{$listService["serv_name"]}&nbsp;({$listService["serv_name_print"]})</td>
        </tr>
TR;
}

echo <<<TR
        <tr class="odd">
            <td class="leftvalue">&nbsp;</td>
            <td>
                <input type="submit" name="delete" value="{$strings["delete"]}">
                <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();">
                <input type="hidden" value="$id" name="id">
            </td>
        </tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
