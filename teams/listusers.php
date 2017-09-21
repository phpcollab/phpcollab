<?php
/*
** Application name: phpCollab
** Last Edit page: 23/03/2004
** Path by root: ../teams/listusers.php
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
** FILE: listusers.php
**
** DESC: Screen: team member list
**
** HISTORY:
** 	23/03/2004	-	added new document info
**  23/03/2004  -	added team full palette hack by Russell E Glaue <rglaue@cait.org>
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

$tmpquery = "WHERE pro.id = '$id'";
$projectDetail = new phpCollab\Request();
$projectDetail->openProjects($tmpquery);
$comptProjectDetail = count($projectDetail->pro_id);

if ($comptProjectDetail == "0") {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blank");
}

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], in));
$blockPage->itemBreadcrumbs($strings["team_members"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->form = "saM";
$block1->openForm("../teams/listusers.php?id=$id#" . $block1->form . "Anchor");

$block1->heading($strings["team_members"]);

$block1->openPaletteIcon();

if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") {
    $block1->paletteIcon(0, "add", $strings["add"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);

    if ($sitePublish == "true") {
        $block1->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
        $block1->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
    }
}

$block1->paletteIcon(4, "info", $strings["view"]);
$block1->paletteIcon(5, "email", $strings["email"]);
$block1->closePaletteIcon();

$block1->sorting("team", $sortingUser->sor_team[0], "mem.name ASC", $sortingFields = array(0 => "mem.name", 1 => "mem.title", 2 => "mem.login", 3 => "mem.phone_work", 4 => "log.connected", 5 => "tea.published"));

$tmpquery = "WHERE tea.project = '$id' AND mem.profil != '3' ORDER BY $block1->sortingValue";
$listTeam = new phpCollab\Request();
$listTeam->openTeams($tmpquery);
$comptListTeam = count($listTeam->tea_id);

$block1->openResults();

$block1->labels($labels = array(0 => $strings["full_name"], 1 => $strings["title"], 2 => $strings["user_name"], 3 => $strings["work_phone"], 4 => $strings["connected"], 5 => $strings["published"]), "true");

for ($i = 0; $i < $comptListTeam; $i++) {
    if ($listTeam->tea_mem_phone_work[$i] == "") {
        $listTeam->tea_mem_phone_work[$i] = $strings["none"];
    }

    $idPublish = $listTeam->tea_published[$i];

    $block1->openRow();
    $block1->checkboxRow($listTeam->tea_mem_id[$i]);
    $block1->cellRow($blockPage->buildLink("../users/viewuser.php?id=" . $listTeam->tea_mem_id[$i], $listTeam->tea_mem_name[$i], in));
    $block1->cellRow($listTeam->tea_mem_title[$i]);
    $block1->cellRow($blockPage->buildLink($listTeam->tea_mem_email_work[$i], $listTeam->tea_mem_login[$i], mail));
    $block1->cellRow($listTeam->tea_mem_phone_work[$i]);

    if ($listTeam->tea_log_connected[$i] > $dateunix - 5 * 60) {
        $block1->cellRow($strings["yes"] . " " . $z);
    } else {
        $block1->cellRow($strings["no"]);
    }

    if ($sitePublish == "true") {
        $block1->cellRow($statusPublish[$idPublish]);
    }

    $block1->closeRow();
}

$block1->closeResults();

$block1->closeFormResults();

$block1->openPaletteScript();
if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") {
    $block1->paletteScript(0, "add", "../teams/adduser.php?project=" . $projectDetail->pro_id[0] . "", "true,true,true", $strings["add"]);
    $block1->paletteScript(1, "remove", "../teams/deleteusers.php?project=" . $projectDetail->pro_id[0] . "", "false,true,true", $strings["delete"]);

    if ($sitePublish == "true") {
        $block1->paletteScript(2, "add_projectsite", "../projects/viewproject.php?addToSiteTeam=true&project=" . $projectDetail->pro_id[0] . "&action=publish", "false,true,true", $strings["add_project_site"]);
        $block1->paletteScript(3, "remove_projectsite", "../projects/viewproject.php?removeToSiteTeam=true&project=" . $projectDetail->pro_id[0] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
    }
}
$block1->paletteScript(4, "info", "../users/viewuser.php?", "false,true,false", $strings["view"]);
$block1->paletteScript(5, "email", "../users/emailusers.php?", "false,true,true", $strings["email"]);
$block1->closePaletteScript($comptListTeam, $listTeam->tea_mem_id);

include '../themes/' . THEME . '/footer.php';
