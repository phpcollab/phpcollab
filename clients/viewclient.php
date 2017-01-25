<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../clients/viewclient.php
** Authors: Ceam / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewclient.php
**
** DESC: screen: view client info and projects
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**	
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

$teams = new \phpCollab\Teams\Teams();
$orgs = new \phpCollab\Organizations\Organizations();
$projects = new \phpCollab\Projects\Projects();
$members = new \phpCollab\Members\Members();

if ($clientsFilter == "true" && $profilSession == "2") {
    $teamMember = "false";

    $memberTest = $teams->getTeamByTeamMemberAndOrgId($idSession, $id);

    if (count($memberTest) == "0") {
        phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
    } else {
        $clientDetail = $orgs->getOrganizationById($id);
    }
} else if ($clientsFilter == "true" && $profilSession == "1") {
    $clientDetail = $orgs->getOrganizationByIdAndOwner($id, $idSession);
} else {
    $clientDetail = $orgs->getOrganizationById($id);
}

if (empty($clientDetail)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

$setTitle .= " : View Client (" . $clientDetail['org_name'] . ")";

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], 'in'));
$blockPage->itemBreadcrumbs($clientDetail['org_name']);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "ecD";
$block1->openForm("../projects/listprojects.php#" . $block1->form . "Anchor");

$block1->heading($strings["organization"] . " : " . $clientDetail['org_name']);

if ($profilSession == "0" || $profilSession == "1") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);
    $block1->paletteIcon(1, "edit", $strings["edit"]);
    $block1->paletteIcon(2, "invoicing", $strings["invoicing"]);
    $block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

if ($clientsFilter == "true") {
    $block1->contentRow($strings["owner"], $blockPage->buildLink("../users/viewuser.php?id=" . $clientDetail['org_mem_id'], $clientDetail['org_mem_name'], in) . " (" . $blockPage->buildLink($clientDetail['org_mem_email_work'], $clientDetail['org_mem_login'], 'mail') . ")");
}
$block1->contentRow($strings["name"], $clientDetail['org_name']);
$block1->contentRow($strings["address"], $clientDetail['org_address1']);
$block1->contentRow($strings["phone"], $clientDetail['org_phone']);
$block1->contentRow($strings["url"], $blockPage->buildLink($clientDetail['org_url'], $clientDetail['org_url'], 'out'));
$block1->contentRow($strings["email"], $blockPage->buildLink($clientDetail['org_email'], $clientDetail['org_email'], 'mail'));
$block1->contentRow($strings["comments"], nl2br($clientDetail['org_comments']));
if ($enableInvoicing == "true" && ($profilSession == "1" || $profilSession == "0" || $profilSession == "5")) {
    $block1->contentRow($strings["hourly_rate"], $clientDetail['org_hourly_rate']);
}
$block1->contentRow($strings["created"], phpCollab\Util::createDate($clientDetail['org_created'], $timezoneSession));
if (file_exists("../logos_clients/" . $id . "." . $clientDetail['org_extension_logo'])) {
    $block1->contentRow($strings["logo"], "<img src=\"../logos_clients/" . $id . "." . $clientDetail['org_extension_logo'] . "\">");
}

$block1->closeContent();
$block1->closeForm();

if ($profilSession == "0" || $profilSession == "1") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../clients/deleteclients.php?id=" . $clientDetail['org_id'] . "", "true,true,false", $strings["delete"]);
    $block1->paletteScript(1, "edit", "../clients/editclient.php?id=" . $clientDetail['org_id'] . "", "true,true,false", $strings["edit"]);
    $block1->paletteScript(2, "invoicing", "../invoicing/listinvoices.php?client=" . $clientDetail['org_id'] . "", "true,true,false", $strings["invoicing"]);
    $block1->closePaletteScript("", "");
}

$block2 = new phpCollab\Block();

$block2->form = "clPr";
$block2->openForm("../clients/viewclient.php?id=$id#" . $block2->form . "Anchor");

$block2->headingToggle($strings["client_projects"]);

$block2->openPaletteIcon();
if ($profilSession == "0" || $profilSession == "1") {
    $block2->paletteIcon(0, "add", $strings["add"]);
    $block2->paletteIcon(1, "remove", $strings["delete"]);
}
$block2->paletteIcon(2, "info", $strings["view"]);
if ($profilSession == "0" || $profilSession == "1") {
    $block2->paletteIcon(3, "edit", $strings["edit"]);
}
//if mantis bug tracker enabled
if ($enableMantis == "true") {
    $block2->paletteIcon(4, "bug", $strings["bug"]);
}
$block2->closePaletteIcon();

$block2->sorting("organization_projects", $sortingUser->sor_organization_projects[0], "pro.name ASC", $sortingFields = array(0 => "pro.id", 1 => "pro.name", 2 => "pro.priority", 3 => "pro.status", 4 => "mem.login", 5 => "pro.published"));

if ($projectsFilter == "true") {
    $listProjects = $projects->getFilteredProjectsByOrganization($clientDetail['org_id'], $idSession, $block2->sortingValue);
} else {
    $listProjects = $projects->getProjectsByOrganization($clientDetail['org_id'], $block2->sortingValue);
}

if ($listProjects) {
    $block2->openResults();

    $block2->labels($labels = array(0 => $strings["id"], 1 => $strings["project"], 2 => $strings["priority"], 3 => $strings["status"], 4 => $strings["owner"], 5 => $strings["project_site"]), "true");

    foreach ($listProjects as $project) {
        $idStatus = $project['pro_status'];
        $idPriority = $project['pro_priority'];
        $block2->openRow();
        $block2->checkboxRow($project['pro_id']);
        $block2->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $project['pro_id'], $project['pro_id'], 'in'));
        $block2->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $project['pro_id'], $project['pro_name'], 'in'));
        $block1->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
        $block2->cellRow($status[$idStatus]);
        $block2->cellRow($blockPage->buildLink($project['pro_mem_email_work'], $project['pro_mem_login'], 'mail'));
        if ($sitePublish == "true") {
            if ($project['pro_published'] == "1") {
                $block2->cellRow("&lt;" . $blockPage->buildLink("../projects/addprojectsite.php?id=" . $project['pro_id'], $strings["create"] . "...", 'in') . "&gt;");
            } else {
                $block2->cellRow("&lt;" . $blockPage->buildLink("../projects/viewprojectsite.php?id=" . $project['pro_id'], $strings["details"], 'in') . "&gt;");
            }
        }
    }
    $block2->closeResults();
} else {
    $block2->noresults();
}
$block2->closeToggle();
$block2->closeFormResults();

$block2->openPaletteScript();
if ($profilSession == "0" || $profilSession == "1") {
    $block2->paletteScript(0, "add", "../projects/editproject.php?organization=" . $clientDetail['org_id'] . "", "true,false,false", $strings["add"]);
    $block2->paletteScript(1, "remove", "../projects/deleteproject.php?", "false,true,false", $strings["delete"]);
}
$block2->paletteScript(2, "info", "../projects/viewproject.php?", "false,true,false", $strings["view"]);
if ($profilSession == "0" || $profilSession == "1") {
    $block2->paletteScript(3, "edit", "../projects/editproject.php?", "false,true,false", $strings["edit"]);
}
//if mantis bug tracker enabled
if ($enableMantis == "true") {
    $block2->paletteScript(4, "bug", $pathMantis . "login.php?url=http://{$HTTP_HOST}{$REQUEST_URI}&username=$loginSession&password=$passwordSession", "false,true,false", $strings["bug"]);
}
$block2->closePaletteScript($comptListProjects, $listProjects['pro_id']);

$block3 = new phpCollab\Block();

$block3->form = "clU";
$block3->openForm("../clients/viewclient.php?id=$id#" . $block3->form . "Anchor");

$block3->headingToggle($strings["client_users"]);

$block3->openPaletteIcon();
if ($profilSession == "0" || $profilSession == "1") {
    $block3->paletteIcon(0, "add", $strings["add"]);
    $block3->paletteIcon(1, "remove", $strings["delete"]);
}
$block3->paletteIcon(2, "info", $strings["view"]);
if ($profilSession == "0" || $profilSession == "1") {
    $block3->paletteIcon(3, "edit", $strings["edit"]);
}
$block3->closePaletteIcon();

$block3->sorting("users", $sortingUser->sor_users[0], "mem.name ASC", $sortingFields = array(0 => "mem.name", 1 => "mem.login", 2 => "mem.email_work", 3 => "mem.phone_work", 4 => "connected"));

$listMembers = $members->getMembersByOrg($id, $block3->sortingValue);

if ($listMembers) {
    $block3->openResults();

    $block3->labels($labels = array(0 => $strings["full_name"], 1 => $strings["user_name"], 2 => $strings["email"], 3 => $strings["work_phone"], 4 => $strings["connected"]), "false");

    foreach ($listMembers as $member) {
        $block3->openRow();
        $block3->checkboxRow($member['mem_id']);
        $block3->cellRow($blockPage->buildLink("../users/viewclientuser.php?id=" . $member['mem_id'] . "&organization=$id", $member['mem_name'], 'in'));
        $block3->cellRow($member['mem_login']);
        $block3->cellRow($blockPage->buildLink($member['mem_email_work'], $member['mem_email_work'], 'mail'));
        $block3->cellRow($member['mem_phone_work']);

        $z = "(Client on project site)";
        if ($member['mem_log_connected'] > $dateunix - 5 * 60) {
            $block3->cellRow($strings["yes"] . " " . $z);
        } else {
            $block3->cellRow($strings["no"]);
        }

    }
    $block3->closeResults();
} else {
    $block3->noresults();
}
$block3->closeToggle();
$block3->closeFormResults();

$block3->openPaletteScript();
if ($profilSession == "0" || $profilSession == "1") {
    $block3->paletteScript(0, "add", "../users/addclientuser.php?organization=$id", "true,true,true", $strings["add"]);
    $block3->paletteScript(1, "remove", "../users/deleteclientusers.php?organization=$id", "false,true,true", $strings["delete"]);
}
$block3->paletteScript(2, "info", "../users/viewclientuser.php?organization=$id", "false,true,false", $strings["view"]);
if ($profilSession == "0" || $profilSession == "1") {
    $block3->paletteScript(3, "edit", "../users/updateclientuser.php?organization=$id", "false,true,false", $strings["edit"]);
}
$block3->closePaletteScript($comptListMembers, $listMembers['mem_id']);

include '../themes/' . THEME . '/footer.php';
