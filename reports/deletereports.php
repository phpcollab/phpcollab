<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../reports/deletereports.php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

$id = str_replace("**", ",", $request->query->get('id'));

if (empty($id) || empty(preg_replace("/[^0-9s]/", "", $id))) {
    phpCollab\Util::headerFunction("../reports/listreports.php");
}

$reports = $container->getReportsLoader();

$listReports = $reports->getReportsByIds($id);

if (empty($listReports)) {
    phpCollab\Util::headerFunction("../reports/listreports.php");
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "delete") {
                $id = str_replace("**", ",", $id);
                $reports->deleteReports($id);
                phpCollab\Util::headerFunction("../reports/listreports.php?msg=deleteReport");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Reports: Delete report' => $id,
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

$setTitle .= " : Delete Report";
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/listreports.php?", $strings["my_reports"], "in"));
$blockPage->itemBreadcrumbs($strings["delete_reports"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "saS";
$block1->openForm("../reports/deletereports.php?action=delete&id=" . $id, null, $csrfHandler);

$block1->heading($strings["delete_reports"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

foreach ($listReports as $report) {
    $block1->contentRow("#" . $report["rep_id"], $report["rep_name"]);
}

$block1->contentRow("",
    '<button type="submit" name="action" value="delete">' . $strings["delete"] . '</button> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
