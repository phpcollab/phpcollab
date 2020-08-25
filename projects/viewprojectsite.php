<?php
/*
** Application name: phpCollab
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewprojectsite.php
**
** DESC: screen: view client info and projects
**
** =============================================================================
*/

use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';

$projects = new Projects();
$teams = new Teams();

if ($request->query->get('action') == "publish") {
    if ($request->query->get('addToSiteTeam') == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $teams->publishToSite($project, $id);
        $msg = "addToSite";
        $id = $project;
    }

    if ($request->query->get('removeToSiteTeam') == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $teams->unPublishToSite($project, $id);
        $msg = "removeToSite";
        $id = $project;
    }
}

if ($msg == "demo") {
    $id = $project;
}

$projectDetail = $projects->getProjectById($id);

$memberTest = $teams->isTeamMember($id, $session->get("id"));

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

if (empty($projectDetail)) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

$setTitle .= " : View Project site (" . $projectDetail["pro_name"] . ")";

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=$id", $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($strings["project_site"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "uploadlogo";
$block1->form = "pdD";
$block1->openForm("../projects/viewprojectsite.php?id=$id&#" . $block1->form . "Anchor", null, $csrfHandler);
$block1->heading($strings["project_site"] . " : " . $projectDetail["pro_name"]);

if ($session->get("id") == $projectDetail["pro_owner"] || $session->get("profile") == "5") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);
    $block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["details"]);
$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=$id", $projectDetail["pro_name"] . " (#" . $projectDetail["pro_id"] . ")", "in"));

if ($projectDetail["pro_org_id"] == "1") {
    $block1->contentRow($strings["organization"], $strings["none"]);
} else {
    $block1->contentRow($strings["organization"], $blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"], $projectDetail["pro_org_name"], "in"));
}

$block1->closeContent();
$block1->closeForm();

if ($session->get("id") == $projectDetail["pro_owner"] || $session->get("profile") == "5") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../projects/deleteprojectsite.php?project=$id", "true,true,true", $strings["delete"]);
    $block1->closePaletteScript(1, $projectDetail["pro_id"]);
}

if ($projectDetail["pro_organization"] != "" && $projectDetail["pro_organization"] != "1") {
    $block2 = new phpCollab\Block();
    $block2->form = "csU";
    $block2->openForm("../projects/viewprojectsite.php?action=update&id=" . $projectDetail["pro_id"] . "#" . $block2->form . "Anchor", null, $csrfHandler);

    $block2->heading($strings["permitted_client"]);

    if ($session->get("id") == $projectDetail["pro_owner"] || $session->get("profile") == "5") {
        $block2->openPaletteIcon();
        $block2->paletteIcon(0, "add", $strings["add"]);
        $block2->paletteIcon(1, "remove", $strings["delete"]);

        if ($sitePublish == "true") {
            $block2->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
            $block2->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
        }
        $block2->closePaletteIcon();
    }

    $block2->sorting("team", $sortingUser["team"], "mem.name ASC", $sortingFields = array(0 => "mem.name", 1 => "mem.title", 2 => "mem.login", 3 => "mem.phone_work", 4 => "log.connected", 5 => "tea.published"));

    $listPermitted = $teams->getClientTeamMembersByProject($id, $block2->sortingValue);

    if ($listPermitted) {
        $block2->openResults();
        $block2->labels($labels = array(0 => $strings["full_name"], 1 => $strings["title"], 2 => $strings["user_name"], 3 => $strings["work_phone"], 4 => $strings["connected"], 5 => $strings["published"]), "true");

        foreach ($listPermitted as $permitted) {
            if ($permitted["tea_mem_phone_work"] == "") {
                $permitted["tea_mem_phone_work"] = $strings["none"];
            }

            $idPublish = $permitted["tea_published"];
            $block2->openRow();
            $block2->checkboxRow($permitted["tea_mem_id"]);
            $block2->cellRow($blockPage->buildLink("../users/viewclientuser.php?id=" . $permitted["tea_mem_id"] . "&organization=" . $projectDetail["pro_organization"], $permitted["tea_mem_name"], "in"));
            $block2->cellRow($permitted["tea_mem_title"]);
            $block2->cellRow($blockPage->buildLink($permitted["tea_mem_email_work"], $permitted["tea_mem_login"], "mail"));
            $block2->cellRow($permitted["tea_mem_phone_work"]);

            if ($permitted["tea_mem_profil"] == "3") {
                $z = "(Client on project site)";
            } else {
                $z = "";
            }

            if ($permitted["tea_log_connected"] > $dateunix - 5 * 60) {
                $block2->cellRow($strings["yes"] . " " . $z);
            } else {
                $block2->cellRow($strings["no"]);
            }

            if ($sitePublish == "true") {
                $block2->cellRow($statusPublish[$idPublish]);
            }
            $block2->closeRow();
        }

        $block2->closeResults();
    } else {
        $block2->noresults();
    }

    $block2->closeFormResults();

    if ($session->get("id") == $projectDetail["pro_owner"] || $session->get("profile") == "5") {
        $block2->openPaletteScript();
        $block2->paletteScript(0, "add", "../teams/addclientuser.php?project=$id", "true,false,false", $strings["add"]);
        $block2->paletteScript(1, "remove", "../teams/deleteclientusers.php?project=$id", "false,true,true", $strings["delete"]);

        if ($sitePublish == "true") {
            $block2->paletteScript(2, "add_projectsite", "../projects/viewprojectsite.php?addToSiteTeam=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
            $block2->paletteScript(3, "remove_projectsite", "../projects/viewprojectsite.php?removeToSiteTeam=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
        }
        $block2->closePaletteScript(count($listPermitted), array_column($listPermitted, 'tea_mem_id'));
    }
}

include APP_ROOT . '/themes/' . THEME . '/footer.php';
