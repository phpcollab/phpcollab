<?php
/*
** Application name: phpCollab
** Last Edit page: 02/08/2007
** Path by root: ../includes/calendar.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewclientuser.php
**
** DESC: Screen:	displays the details of a client user
**
** HISTORY:
** 	02/08/2007	-	added Last Viewed Page code - Mindblender
**
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

$members = new \phpCollab\Members\Members();
$organizations = new \phpCollab\Organizations\Organizations();

$orgId = $_GET['organization'];
$id = $_GET['id'];

if (empty($id) || empty($orgId)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

$userDetail = $members->getMemberById($id);

if (empty($userDetail)) {
    phpCollab\Util::headerFunction("../clients/viewclient.php?msg=blankUser&id=$orgId");
}
$memberOrganization = $userDetail['mem_organization'];

$idSession = \phpCollab\Util::returnGlobal('idSession', 'SESSION');
$profilSession = \phpCollab\Util::returnGlobal('profilSession', 'SESSION');

if ($clientsFilter == "true" && $profilSession == "2") {
    $teams = new \phpCollab\Teams\Teams();
    $teamMember = "false";

    $memberTest = $teams->getTeamByTeamMemberAndOrgId($idSession, $memberOrganization);

    if (empty($memberTest)) {
        phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
    }
} elseif ($clientsFilter == "true" && $profilSession == "1") {
    $detailClient = $organizations->getOrganizationByIdAndOwner($idSession, $orgId);
} else {
    $detailClient = $organizations->getOrganizationById($orgId);
}

$comptDetailClient = "0";

if (empty($detailClient)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=$orgId", $detailClient['org_name'], in));
$blockPage->itemBreadcrumbs($userDetail['mem_login']);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->form = "cuserD";
$block1->openForm("../users/viewclientuser.php#" . $block1->form . "Anchor");

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["client_user"]);

$block1->openPaletteIcon();
if ($profilSession == "0" || $profilSession == "1") {
    $block1->paletteIcon(0, "remove", $strings["delete"]);
    $block1->paletteIcon(1, "edit", $strings["edit"]);
}
$block1->paletteIcon(2, "export", $strings["export"]);
$block1->closePaletteIcon();

$block1->openContent();
$block1->contentTitle($strings["user_details"]);

$block1->contentRow($strings["user_name"], $userDetail['mem_login']);
$block1->contentRow($strings["full_name"], $userDetail['mem_name']);
$block1->contentRow($strings["organization"], $userDetail['org_name']);
$block1->contentRow($strings["email"], $userDetail['mem_email_work']);
$block1->contentRow($strings["work_phone"], $userDetail['mem_phone_work']);
$block1->contentRow($strings["home_phone"], $userDetail['mem_phone_home']);
$block1->contentRow($strings["mobile_phone"], $userDetail['mem_mobile']);
$block1->contentRow($strings["fax"], $userDetail['mem_fax']);
$block1->contentRow($strings["comments"], nl2br($userDetail['mem_comments']));
$block1->contentRow($strings["account_created"], phpCollab\Util::createDate($userDetail['mem_created'], $timezoneSession));
$block1->contentRow($strings["last_page"], $userDetail['mem_last_page']);

$block1->contentTitle($strings["information"]);

// Refactor
$tmpquery = "SELECT tas.id FROM {$tableCollab["tasks"]} tas LEFT OUTER JOIN {$tableCollab["projects"]} pro ON pro.id = tas.project WHERE tas.assigned_to = '" . $userDetail['mem_id'] . "' AND tas.status IN(0,2,3) AND pro.status IN(0,2,3)";
$valueTasks = phpCollab\Util::computeTotal($tmpquery);

$block1->contentRow($strings["tasks"], $valueTasks);

$z = "(Client on project site)";
if ($userDetail['mem_log_connected'] > $dateunix - 5 * 60) {
    $connected_result = $strings["yes"] . " " . $z;
} else {
    $connected_result = $strings["no"];
}
$block1->contentRow($strings["connected"], $connected_result);

$block1->closeContent();
$block1->closeForm();

$block1->openPaletteScript();
if ($profilSession == "0" || $profilSession == "1") {
    $block1->paletteScript(0, "remove", "../users/deleteclientusers.php?id=$id&organization=$orgId", "true,true,true", $strings["delete"]);
    $block1->paletteScript(1, "edit", "../users/updateclientuser.php?id=$id&organization=$orgId", "true,true,true", $strings["edit"]);
}
$block1->paletteScript(2, "export", "../users/exportuser.php?id=$id", "true,true,true", $strings["export"]);
$block1->closePaletteScript("", "");

include '../themes/' . THEME . '/footer.php';
