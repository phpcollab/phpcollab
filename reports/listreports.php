<?php

use phpCollab\Reports\Reports;

$checkSession = "true";
include_once '../includes/library.php';

$strings = $GLOBALS["strings"];

$setTitle .= " : " . $strings["my_reports"];

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/listreports.php?", $strings["reports"], "in"));
$blockPage->itemBreadcrumbs($strings["my_reports"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "wbSe";
$block1->openForm("../reports/listreports.php#" . $block1->form . "Anchor");

$block1->heading($strings["my_reports"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0, "add", $strings["add"]);
$block1->paletteIcon(1, "remove", $strings["delete"]);
$block1->closePaletteIcon();

$block1->sorting("reports", $sortingUser["reports"], "rep.name ASC", $sortingFields = [0 => "rep.name", 1 => "rep.created"]);

$db = new phpCollab\Database();

$reports = new Reports();

$sorting = $block1->sortingValue;

$dataSet = $reports->getReportsByOwner($idSession, $sorting);

$reportCount = count($dataSet);

if ($dataSet) {
    $block1->openResults();
    $block1->labels($labels = [0 => $strings["name"], 1 => $strings["created"]], "false");

    foreach ($dataSet as $data) {
        $block1->openRow();
        $block1->checkboxRow($data["rep_id"]);
        $block1->cellRow($blockPage->buildLink("../reports/resultsreport.php?id=" . $data["rep_id"], $data["rep_name"], "in"));
        $block1->cellRow(phpCollab\Util::createDate($data["rep_created"], $timezoneSession));
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}

$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0, "add", "../reports/createreport.php?", "true,true,true", $strings["add"]);
$block1->paletteScript(1, "remove", "../reports/deletereports.php?", "false,true,true", $strings["delete"]);
$block1->paletteScript(2, "export", "../reports/exportreport.php?", "false,true,true", $strings["export"]);
$block1->closePaletteScript(count($dataSet), $dataSet[0]['rep_id']);

include APP_ROOT . '/themes/' . THEME . '/footer.php';
