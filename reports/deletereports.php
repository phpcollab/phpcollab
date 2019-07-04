<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../reports/deletereports.php

use phpCollab\Reports\Reports;

$checkSession = "true";
include_once '../includes/library.php';

$reports = new Reports();

if ($action == "delete") {
    $id = str_replace("**", ",", $id);
    $reports->deleteReports($id);
    phpCollab\Util::headerFunction("../reports/listreports.php?msg=deleteReport");
}

$setTitle .= " : Delete Report";
include APP_ROOT . '/themes/' . THEME . '/header.php';

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
$block1->openForm("../reports/deletereports.php?action=delete&id=$id");

$block1->heading($strings["delete_reports"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);

$listReports = $reports->getReportsByIds($id);

foreach ($listReports as $report) {
    $block1->contentRow("#" . $report["rep_id"], $report["rep_name"]);
}

$block1->contentRow("", '<input type="submit" name="delete" value="' . $strings["delete"] . '"> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
