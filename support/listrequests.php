<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Projects\Projects;
use phpCollab\Support\Support;
use phpCollab\Teams\Teams;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

if ($enableHelpSupport != "true") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($supportType == "admin") {
    if ($profilSession != "0") {
        phpCollab\Util::headerFunction('../general/permissiondenied.php');
    }
}

$support = new Support();
$projects = new Projects();
$teams = new Teams();

$projectDetail = $projects->getProjectById($id);

$teamMember = "false";

$teamMember = $teams->isTeamMember($id, $idSession);

include APP_ROOT . '/themes/' . THEME . '/header.php';


$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
    $blockPage->itemBreadcrumbs($strings["support_requests"]);
} elseif ($supportType == "admin") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?", $strings["support_management"], "in"));
    $blockPage->itemBreadcrumbs($strings["support_requests"]);
}
$blockPage->closeBreadcrumbs();


if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "srs";
$block1->openForm("../support/listrequests.php?&id=$id#" . $block1->form . "Anchor");
$block1->heading($strings["support_requests"]);
if ($teamMember == "true" || $profilSession == "0") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(1, "edit", $strings["edit_status"]);
    $block1->paletteIcon(2, "remove", $strings["delete"]);
    $block1->paletteIcon(3, "info", $strings["view"]);
    $block1->closePaletteIcon();
}
$block1->sorting("support_requests", $sortingUser["support_requests"], "sr.id ASC", $sortingFields = array(0 => "sr.id", 1 => "sr.subject", 2 => "sr.priority", 3 => "sr.status", 4 => "sr.date_open", 5 => "sr.date_close"));

$listRequests = $support->getSupportRequestByProject($id, $block1->sortingValue);

if (!empty($listRequests)) {
    $block1->openResults();
    $block1->labels($labels = array(0 => $strings["id"], 1 => $strings["subject"], 2 => $strings["priority"], 3 => $strings["status"], 4 => $strings["date_open"], 5 => $strings["date_close"]), "false");

    foreach ($listRequests as $request) {
        $comptSta = count($requestStatus);
        $currentStatus = Util::isBlank();

        for ($sr = 0; $sr < $comptSta; $sr++) {
            if ($request["sr_status"] == $sr) {
                $currentStatus = $requestStatus[$sr];
            }
        }

        $comptPri = count($priority);
        for ($rp = 0; $rp < $comptPri; $rp++) {
            if ($request["sr_priority"] == $rp) {
                $requestPriority = $priority[$rp];
            }
        }
        $block1->openRow();
        $block1->checkboxRow($request["sr_id"]);
        $block1->cellRow($request["sr_id"]);
        $block1->cellRow($blockPage->buildLink("../support/viewrequest.php?id=" . $request["sr_id"], $request["sr_subject"], "in"));
        $block1->cellRow($requestPriority);
        $block1->cellRow($currentStatus);
        $block1->cellRow($request["sr_date_open"]);
        $block1->cellRow($request["sr_date_close"]);
        $block1->closeRow();
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}
$block1->closeFormResults();
if ($teamMember == "true" || $profilSession == "0") {
    $block1->openPaletteScript();
    $block1->paletteScript(1, "edit", "../support/addpost.php?action=status", "false,true,false", $strings["edit_status"]);
    $block1->paletteScript(2, "remove", "../support/deleterequests.php?action=deleteR", "false,true,true", $strings["delete"]);
    $block1->paletteScript(3, "info", "../support/viewrequest.php?", "false,true,false", $strings["view"]);
    $block1->closePaletteScript(count($listRequests), $listRequests[0]["sr_id"]);
}

include APP_ROOT . '/themes/' . THEME . '/footer.php';
