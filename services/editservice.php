<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../services/editservice.php

use phpCollab\Util;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

if ($session->get("profile") != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}
$services = $container->getServicesLoader();

$id = $request->query->get('id');

$name = '';
$namePrinted = '';
$hourlyRate = '';

if (!empty($id)) {
    if ($request->isMethod('post')) {

        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($request->request->get('action') == "update") {
                    $name = Util::convertData($request->request->get('name'));
                    $namePrinted = Util::convertData($request->request->get('name_printed'));
                    $hourlyRate = $request->request->get('hourly_rate');
                    try {
                        $services->updateService($id, $name, $namePrinted, $hourlyRate);
                    } catch (Exception $e) {
                        $logger->error('Services (update)', ['Exception message', $e->getMessage()]);
                        $error = $strings["action_not_allowed"];
                    }

                    phpCollab\Util::headerFunction("../services/listservices.php?msg=update");
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Services: Edit service' => $id,
                '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }

    }

    $detailService = $services->getService($id);

    //set values in form
    $name = $detailService["serv_name"];
    $namePrinted = $detailService["serv_name_print"];
    $hourlyRate = $detailService["serv_hourly_rate"];
}

//case add service
if (empty($id) && $request->isMethod('post')) {

    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "add") {
                //replace quotes by html code in name and address
                $name = phpCollab\Util::convertData($request->request->get('name'));
                $namePrinted = phpCollab\Util::convertData($request->request->get('name_printed'));
                $hourlyRate = $request->request->get('hourly_rate');

                try {
                    $services->addService($name, $namePrinted, $hourlyRate);

                    phpCollab\Util::headerFunction("../services/listservices.php?msg=add");
                } catch (Exception $e) {
                    $logger->error('Services (add)', ['Exception message', $e->getMessage()]);
                    $error = $strings["action_not_allowed"];
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Services: Add service',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }

}

/* Titles */
if ($id == '') {
    $setTitle .= " : Add Service";
} else {
    $setTitle .= " : Edit Service (" . $detailService["serv_name"] . ")";
}

$bodyCommand = 'onLoad="document.serv_editForm.name.focus();"';

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../services/listservices.php?", $strings["service_management"],
    "in"));

if ($id == "") {
    $blockPage->itemBreadcrumbs($strings["add_service"]);
}
if ($id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../services/viewservice.php?id=$id", $detailService["serv_name"],
        "in"));
    $blockPage->itemBreadcrumbs($strings["edit_service"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if ($id == "") {
    $block1->form = "serv_edit";
    $submitValue = "add";
    $block1->openForm("../services/editservice.php?#" . $block1->form . "Anchor", null, $csrfHandler);
}
if ($id != "") {
    $block1->form = "serv_edit";
    $submitValue = "update";
    $block1->openForm("../services/editservice.php?id=$id#" . $block1->form . "Anchor", null, $csrfHandler);
}

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if (empty($id)) {
    $block1->heading($strings["add_service"]);
}
if (!empty($id)) {
    $block1->heading($strings["edit_service"] . " : " . $detailService["serv_name"]);
}

$block1->openContent();

if ($id == "") {
    $block1->contentTitle($strings["details"]);
}
if ($id != "") {
    $block1->contentTitle($strings["details"]);
}

echo <<<TR
<tr class="odd">
    <td class="leftvalue">{$strings["name"]} :</td>
    <td><input size="24" style="width: 250px;" type="text" name="name" value="{$name}"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["name_print"]} :</td>
    <td><input size="24" style="width: 250px;" type="text" name="name_printed" value="{$namePrinted}"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["hourly_rate"]} :</td>
    <td><input size="24" style="width: 250px;" type="text" name="hourly_rate" value="{$hourlyRate}"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">&nbsp;</td>
    <td><button type="submit" name="action" value="{$submitValue}">{$strings["save"]}</button></td>
</tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
