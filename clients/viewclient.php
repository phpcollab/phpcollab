<?php
/*
** Application name: phpCollab
** Path by root: ../clients/viewclient.php
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
** =============================================================================
*/

use phpCollab\Organizations\Organizations;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

$teams = new Teams();
$orgs = new Organizations();
$projects = new Projects();

if ($clientsFilter == "true" && $session->get("profile") == "2") {
    $teamMember = "false";

    $memberTest = $teams->getTeamByTeamMemberAndOrgId($session->get("idSession"), $request->query->get("id"));

    if (count($memberTest) == "0") {
        phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
    } else {
        $clientDetail = $orgs->getOrganizationById($request->query->get("id"));
    }
} elseif ($clientsFilter == "true" && $session->get("profile") == "1") {
    $clientDetail = $orgs->getOrganizationByIdAndOwner($request->query->get("id"), $session->get("idSession"));
} else {
    $clientDetail = $orgs->getOrganizationById($request->query->get("id"));
}

if (empty($clientDetail)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

$setTitle .= " : View Client (" . $clientDetail['org_name'] . ")";

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php", $strings["clients"], 'in'));
$blockPage->itemBreadcrumbs($clientDetail['org_name']);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "ecD";
$block1->openForm("../projects/listprojects.php#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->heading($strings["organization"] . " : " . $clientDetail['org_name']);

if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);
    $block1->paletteIcon(1, "edit", $strings["edit"]);
    $block1->paletteIcon(2, "invoicing", $strings["invoicing"]);
    $block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

if ($clientsFilter == "true") {
    $block1->contentRow($strings["owner"], $blockPage->buildLink("../users/viewuser.php?id=" . $clientDetail['mem_id'], $clientDetail['mem_name'], "in") . " (" . $blockPage->buildLink($clientDetail['mem_email_work'], $clientDetail['mem_login'], 'mail') . ")");
}
$block1->contentRow(
    $strings["name"],
    !empty($clientDetail['org_name']) ? $clientDetail['org_name'] : Util::doubleDash()
);
$block1->contentRow($strings["address"],
    !empty($clientDetail['org_address1']) ? $clientDetail['org_address1'] : Util::doubleDash()
);
$block1->contentRow(
    $strings["phone"],
    !empty($clientDetail['org_phone']) ? $clientDetail['org_phone'] : Util::doubleDash()
);
$block1->contentRow($strings["url"], $blockPage->buildLink($clientDetail['org_url'], $clientDetail['org_url'], 'out'));
$block1->contentRow($strings["email"], $blockPage->buildLink($clientDetail['org_email'], $clientDetail['org_email'], 'mail'));
$block1->contentRow(
    $strings["comments"],
    !empty($clientDetail['org_comments']) ? nl2br($clientDetail['org_comments']) : Util::doubleDash()
);
if ($enableInvoicing == "true" && ($session->get("profile") == "1" || $session->get("profile") == "0" || $session->get("profile") == "5")) {
    $block1->contentRow($strings["hourly_rate"], $clientDetail['org_hourly_rate']);
}
$block1->contentRow($strings["created"], phpCollab\Util::createDate($clientDetail['org_created'], $session->get("timezoneSession")));
if (file_exists("../logos_clients/" . $request->query->get("id") . "." . $clientDetail['org_extension_logo'])) {
    $block1->contentRow($strings["logo"], '<div class="logoContainer"><img alt="' . $clientDetail['org_name'] . '" src="../logos_clients/' . $request->query->get("id") . '.' . $clientDetail['org_extension_logo'] . '"></div>');
}

$block1->closeContent();
$block1->closeForm();

if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../clients/deleteclients.php?id=" . $clientDetail['org_id'] . "", "true,true,false", $strings["delete"]);
    $block1->paletteScript(1, "edit", "../clients/editclient.php?id=" . $clientDetail['org_id'] . "", "true,true,false", $strings["edit"]);
    $block1->paletteScript(2, "invoicing", "../invoicing/listinvoices.php?client=" . $clientDetail['org_id'] . "", "true,true,false", $strings["invoicing"]);
    $block1->closePaletteScript("", []);
}

$block2 = new phpCollab\Block();

$block2->form = "clPr";
$block2->openForm("../clients/viewclient.php?id=" . $request->query->get("id") . "#" . $block2->form . "Anchor", null, $csrfHandler);

$block2->headingToggle($strings["client_projects"]);

$block2->openPaletteIcon();
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block2->paletteIcon(0, "add", $strings["add"]);
    $block2->paletteIcon(1, "remove", $strings["delete"]);
}
$block2->paletteIcon(2, "info", $strings["view"]);
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block2->paletteIcon(3, "edit", $strings["edit"]);
}
//if mantis bug tracker enabled
if ($enableMantis == "true") {
    $block2->paletteIcon(4, "bug", $strings["bug"]);
}
$block2->closePaletteIcon();

$block2->sorting("organization_projects", $sortingUser["organization_projects"], "pro.name ASC", $sortingFields = array(0 => "pro.id", 1 => "pro.name", 2 => "pro.priority", 3 => "pro.status", 4 => "mem.login", 5 => "pro.published"));

if ($projectsFilter == "true") {
    $listProjects = $projects->getFilteredProjectsByOrganization($clientDetail['org_id'], $session->get("idSession"), $block2->sortingValue);
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
                    if ($project['pro_owner'] == $session->get("idSession")) {
                        $block2->cellRow("&lt;" . $blockPage->buildLink("../projects/addprojectsite.php?id=" . $project['pro_id'], $strings["create"] . "...", 'in') . "&gt;");
                    } else {
                            $block2->cellRow(Util::doubleDash());
                    }
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
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block2->paletteScript(0, "add", "../projects/editproject.php?organization=" . $clientDetail['org_id'] . "", "true,false,false", $strings["add"]);
    $block2->paletteScript(1, "remove", "../projects/deleteproject.php?", "false,true,false", $strings["delete"]);
}
$block2->paletteScript(2, "info", "../projects/viewproject.php?", "false,true,false", $strings["view"]);
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block2->paletteScript(3, "edit", "../projects/editproject.php?", "false,true,false", $strings["edit"]);
}
//if mantis bug tracker enabled
if ($enableMantis == "true") {
    $block2->paletteScript(4, "bug", $pathMantis . "login.php?url=http://{$request->server->get("HTTP_HOST")}{$request->server->get("REQUEST_URI")}&username={$session->get("loginSession")}&password={$session->get("passwordSession")}", "false,true,false", $strings["bug"]);
}
$block2->closePaletteScript(count($listProjects), array_column($listProjects, 'pro_id'));

$block3 = new phpCollab\Block();

$block3->form = "clU";
$block3->openForm("../clients/viewclient.php?id=" . $request->query->get("id") . "#" . $block3->form . "Anchor", null, $csrfHandler);

$block3->headingToggle($strings["client_users"]);

$block3->openPaletteIcon();
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block3->paletteIcon(0, "add", $strings["add"]);
    $block3->paletteIcon(1, "remove", $strings["delete"]);
}
$block3->paletteIcon(2, "info", $strings["view"]);
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block3->paletteIcon(3, "edit", $strings["edit"]);
}
$block3->closePaletteIcon();

$block3->sorting("users", $sortingUser["users"], "mem.name ASC", $sortingFields = array(0 => "mem.name", 1 => "mem.login", 2 => "mem.email_work", 3 => "mem.phone_work", 4 => "connected"));

$listMembers = $members->getMembersByOrg($request->query->get("id"), $block3->sortingValue);

if ($listMembers) {
    $block3->openResults();

    $block3->labels($labels = array(0 => $strings["full_name"], 1 => $strings["user_name"], 2 => $strings["email"], 3 => $strings["work_phone"], 4 => $strings["connected"]), "false");

    foreach ($listMembers as $member) {
        $block3->openRow();
        $block3->checkboxRow($member['mem_id']);
        $block3->cellRow($blockPage->buildLink("../users/viewclientuser.php?id=" . $member['mem_id'] . "&organization=" . $request->query->get("id"), $member['mem_name'], 'in'));
        $block3->cellRow($member['mem_login']);
        $block3->cellRow($blockPage->buildLink($member['mem_email_work'], $member['mem_email_work'], 'mail'));
        $block3->cellRow(!empty($clientDetail['mem_phone_work']) ? $clientDetail['mem_phone_work'] : Util::doubleDash());

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
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block3->paletteScript(0, "add", "../users/addclientuser.php?organization=" . $request->query->get("id"), "true,true,true", $strings["add"]);
    $block3->paletteScript(1, "remove", "../users/deleteclientusers.php?orgid=" . $request->query->get("id"), "false,true,true", $strings["delete"]);
}
$block3->paletteScript(2, "info", "../users/viewclientuser.php?organization=" . $request->query->get("id"), "false,true,false", $strings["view"]);
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block3->paletteScript(3, "edit", "../users/updateclientuser.php?orgid=" . $request->query->get("id"), "false,true,false", $strings["edit"]);
}
$block3->closePaletteScript(count($listMembers), array_column($listMembers, 'mem_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
