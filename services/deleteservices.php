<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../services/deleteservices.php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $services = $container->getServicesLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

if ($session->get("profile") != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "delete") {
                $id = str_replace("**", ",", $request->request->get("id"));

                $services->deleteServices($id);
                phpCollab\Util::headerFunction("../services/listservices.php?msg=delete");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Services: Delete service',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }

}

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../services/listservices.php?", $strings["service_management"],
    "in"));
$blockPage->itemBreadcrumbs($strings["delete_services"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "service_delete";
$block1->openForm("../services/deleteservices.php?", null, $csrfHandler);

$block1->heading($strings["delete_services"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $request->query->get("id"));
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
                <button type="submit" value="delete" name="action">{$strings["delete"]}</button>
                <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();">
                <input type="hidden" value="$id" name="id">
            </td>
        </tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
