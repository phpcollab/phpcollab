<?php
#Application name: PhpCollab
#Status page: 0

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

$tmpquery = "WHERE pro.id = '$id'";
$projectDetail = new phpCollab\Request();
$projectDetail->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$id' AND tea.member = '$idSession'";
$memberTest = new phpCollab\Request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);
if ($comptMemberTest == "0") {
    $teamMember = "false";
} else {
    $teamMember = "true";
}

include '../themes/' . THEME . '/header.php';


$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], in));
    $blockPage->itemBreadcrumbs($strings["support_requests"]);
} else if ($supportType == "admin") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?", $strings["support_management"], in));
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
$block1->sorting("support_requests", $sortingUser->sor_support_requests[0], "sr.id ASC", $sortingFields = array(0 => "sr.id", 1 => "sr.subject", 2 => "sr.priority", 3 => "sr.status", 4 => "sr.date_open", 5 => "sr.date_close"));

$tmpquery = "WHERE sr.project = '$id' ORDER BY $block1->sortingValue";
$listRequests = new phpCollab\Request();
$listRequests->openSupportRequests($tmpquery);
$comptListRequests = count($listRequests->sr_id);

if ($comptListRequests != "0") {
    $block1->openResults();
    $block1->labels($labels = array(0 => $strings["id"], 1 => $strings["subject"], 2 => $strings["priority"], 3 => $strings["status"], 4 => $strings["date_open"], 5 => $strings["date_close"]), "false");

    for ($i = 0; $i < $comptListRequests; $i++) {
        $comptSta = count($requestStatus);
        for ($sr = 0; $sr < $comptSta; $sr++) {
            if ($listRequests->sr_status[$i] == $sr) {
                $currentStatus = $requestStatus[$sr];
            }
        }

        $comptPri = count($priority);
        for ($rp = 0; $rp < $comptPri; $rp++) {
            if ($listRequests->sr_priority[$i] == $rp) {
                $requestPriority = $priority[$rp];
            }
        }
        $block1->openRow();
        $block1->checkboxRow($listRequests->sr_id[$i]);
        $block1->cellRow($listRequests->sr_id[$i]);
        $block1->cellRow($blockPage->buildLink("../support/viewrequest.php?id=" . $listRequests->sr_id[$i], $listRequests->sr_subject[$i], in));
        $block1->cellRow($requestPriority);
        $block1->cellRow($currentStatus);
        $block1->cellRow($listRequests->sr_date_open[$i]);
        $block1->cellRow($listRequests->sr_date_close[$i]);
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
    $block1->closePaletteScript($comptListRequests, $listRequests->sr_id);
}

include '../themes/' . THEME . '/footer.php';
