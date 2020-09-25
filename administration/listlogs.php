<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../administration/listlogs.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: listlogs.php
**
** DESC: Screen: users log
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

if ($session->get('profile') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$setTitle .= " : Logs";

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($strings["logs"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();
$block1->form = "adminD";
$block1->openForm("../administration/listlogs.php?action=delete&&id=$id#" . $block1->form . "Anchor", null,
    $csrfHandler);
$block1->heading($strings["logs"]);
$block1->openResults($checkbox = "false");
$block1->labels($labels = [
    0 => $strings["user_name"],
    1 => $strings["ip"],
    2 => $strings["session"],
    3 => $strings["compteur"],
    4 => $strings["last_visit"],
    5 => $strings["connected"]
], "false", $sorting = "false", $sortingOff = [0 => "4", 1 => "DESC"]);

$logsData = $loginLogs->getLogs('logs.last_visite DESC');

$dateunix = date("U");

foreach ($logsData as $log) {
    $block1->openRow();
    $block1->checkboxRow($log['id'], $checkbox = "false");
    $block1->cellRow($log['login']);
    $block1->cellRow($log['ip']);
    $block1->cellRow($log['session']);
    $block1->cellRow($log['compt']);
    $block1->cellRow(phpCollab\Util::createDate($log['last_visite'], $session->get("timezone")));

    if ($log['profil'] == "3") {
        $z = "(Client on project site)";
    } else {
        $z = "";
    }

    if ($log['connected'] === true) {
        $block1->cellRow($strings["yes"] . " " . $z);
    } else {
        $block1->cellRow($strings["no"]);
    }

    $block1->closeRow();
}

$block1->closeResults();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
