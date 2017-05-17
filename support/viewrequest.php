<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include_once '../includes/library.php';

$teams = new \phpCollab\Teams\Teams();

$id = $_GET["id"];

if ($enableHelpSupport != "true") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($supportType == "admin") {
    if ($profilSession != "0") {
        phpCollab\Util::headerFunction('../general/permissiondenied.php');
    }
}

$tmpquery = "WHERE sr.id = '$id'";
$requestDetail = new phpCollab\Request();
$requestDetail->openSupportRequests($tmpquery);

$tmpquery = "WHERE sp.request_id = '$id' ORDER BY sp.date DESC";
$listPosts = new phpCollab\Request();
$listPosts->openSupportPosts($tmpquery);
$comptListPosts = count($listPosts->sp_id);

$teamMember = "false";

$teamMember = $teams->isTeamMember($requestDetail->sr_project[0], $idSession);

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $requestDetail->sr_project[0], $requestDetail->sr_pro_name[0], in));
} else if ($supportType == "admin") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?", $strings["support_management"], in));
}
$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $requestDetail->sr_project[0], $strings["support_requests"], in));
$blockPage->itemBreadcrumbs($requestDetail->sr_subject[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "sdt";
$block1->openForm("");

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["support_request"] . " : " . $requestDetail->sr_subject[0]);
if ($teamMember == "true" || $profilSession == "0") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "edit", $strings["edit_status"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);
    $block1->closePaletteIcon();
}
$block1->openContent();
$block1->contentTitle($strings["info"]);

$comptSupStatus = count($requestStatus);
for ($i = 0; $i < $comptSupStatus; $i++) {
    if ($requestDetail->sr_status[0] == $i) {
        $status = $requestStatus[$i];
    }
}
$comptPri = count($priority);
for ($i = 0; $i < $comptPri; $i++) {
    if ($requestDetail->sr_priority[0] == $i) {
        $requestPriority = $priority[$i];
    }
}

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $requestDetail->sr_project[0], $requestDetail->sr_pro_name[0], in));
$block1->contentRow($strings["subject"], $requestDetail->sr_subject[0]);
$block1->contentRow($strings["priority"], $requestPriority);
$block1->contentRow($strings["status"], $status);
$block1->contentRow($strings["date"], $requestDetail->sr_date_open[0]);
$block1->contentRow($strings["user"], $blockPage->buildLink($requestDetail->sr_mem_email_work[0], $requestDetail->sr_mem_name[0], mail));
$block1->contentRow($strings["message"], nl2br($requestDetail->sr_message[0]));

$block1->contentTitle($strings["responses"]);

if ($teamMember == "true" || $profilSession != "0") {
    $block1->contentRow("", $blockPage->buildLink("../support/addpost.php?id=" . $requestDetail->sr_id[0], $strings["add_support_response"], in));
}

for ($i = 0; $i < $comptListPosts; $i++) {
    $block1->contentRow($strings["posted_by"], $blockPage->buildLink($listPosts->sp_mem_email_work[$i], $listPosts->sp_mem_name[$i], mail));
    $block1->contentRow($strings["date"], phpCollab\Util::createDate($listPosts->sp_date[$i], $timezoneSession));

    if ($teamMember == "true" || $profilSession == "0") {
        $block1->contentRow($blockPage->buildLink("../support/deleterequests.php?action=deleteP&id=" . $listPosts->sp_id[$i], $strings["delete_message"], in), nl2br($listPosts->sp_message[$i]));
    } else {
        $block1->contentRow("", nl2br($listPosts->sp_message[$i]));
    }
    $block1->contentRow("", "", "true");
}

if ($status == $requestStatus[0]) {
    $status = "new";
} elseif ($status == $requestStatus[1]) {
    $status = "open";
} elseif ($status == $requestStatus[2]) {
    $status = "complete";
}

$block1->closeContent();
$block1->openPaletteScript();
$block1->paletteScript(0, "edit", "../support/addpost.php?action=status&id=" . $requestDetail->sr_id[0] . "", "true,true,true", $strings["edit_status"]);
$block1->paletteScript(1, "remove", "../support/deleterequests.php?action=deleteR&sendto=$status&id=" . $requestDetail->sr_id[0] . "", "true,true,true", $strings["delete"]);
$block1->closePaletteScript($comptListRequests, $listRequests->sr_id);
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';

