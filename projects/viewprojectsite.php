<?php
/*
** Application name: phpCollab
** Last Edit page: 23/05/2005
** Path by root: ../projects/viewprojectsite.php
** Authors: Ceam / Fullo 
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
** HISTORY:
** 	23/05/2005	-	added new document info
**  23/05/2005	-	fix for http://www.php-collab.org/community/viewtopic.php?p=7124
** -----------------------------------------------------------------------------
** TO-DO:
**	
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';

if ($action == "publish") {
    if ($addToSiteTeam == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
            $tmpquery1 = "UPDATE " . $tableCollab["teams"] . " SET published='0' WHERE member IN($id) AND project = '$project'";
        } else {
            $tmpquery1 = "UPDATE " . $tableCollab["teams"] . " SET published='0' WHERE member = '$id' AND project = '$project'";
        }
        phpCollab\Util::connectSql("$tmpquery1");
        $msg = "addToSite";
        $id = $project;
    }

    if ($removeToSiteTeam == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
            $tmpquery1 = "UPDATE " . $tableCollab["teams"] . " SET published='1' WHERE member IN($id) AND project = '$project'";
        } else {
            $tmpquery1 = "UPDATE " . $tableCollab["teams"] . " SET published='1' WHERE member = '$id' AND project = '$project'";
        }

        phpCollab\Util::connectSql("$tmpquery1");
        $msg = "removeToSite";
        $id = $project;
    }
}

if ($msg == "demo") {
    $id = $project;
}

$tmpquery = "WHERE pro.id = '$id'";
$projectDetail = new phpCollab\Request();
$projectDetail->openProjects($tmpquery);
$comptProjectDetail = count($projectDetail->pro_id);

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

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

if ($comptPro == "0") {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=$id", $projectDetail->pro_name[0], in));
$blockPage->itemBreadcrumbs($strings["project_site"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "uploadlogo";
$block1->form = "pdD";
$block1->openForm("../projects/viewprojectsite.php?action=update&id=$id&#" . $block1->form . "Anchor");
$block1->heading($strings["project_site"] . " : " . $projectDetail->pro_name[0]);

if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);
    //$block1->paletteIcon(1,"template",$strings["template"]);
    //$block1->paletteIcon(2,"edit",$strings["edit"]);
    $block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["details"]);
$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=$id", $projectDetail->pro_name[0] . " (#" . $projectDetail->pro_id[0] . ")", in));

if ($projectDetail->pro_org_id[0] == "1") {
    $block1->contentRow($strings["organization"], $strings["none"]);
} else {
    $block1->contentRow($strings["organization"], $blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail->pro_org_id[0], $projectDetail->pro_org_name[0], in));
}

$block1->closeContent();
$block1->closeForm();

if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../projects/deleteprojectsite.php?project=$id", "true,true,true", $strings["delete"]);
    $block1->closePaletteScript("", "");
}

if ($projectDetail->pro_organization[0] != "" && $projectDetail->pro_organization[0] != "1") {

    $block2 = new phpCollab\Block();
    $block2->form = "csU";
    $block2->openForm("../projects/viewprojectsite.php?&id=" . $projectDetail->pro_id[0] . "#" . $block2->form . "Anchor");
    $block2->heading($strings["permitted_client"]);

    if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") {
        $block2->openPaletteIcon();
        $block2->paletteIcon(0, "add", $strings["add"]);
        $block2->paletteIcon(1, "remove", $strings["delete"]);

        if ($sitePublish == "true") {
            $block2->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
            $block2->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
        }
        $block2->closePaletteIcon();
    }

    $block2->sorting("team", $sortingUser->sor_team[0], "mem.name ASC", $sortingFields = array(0 => "mem.name", 1 => "mem.title", 2 => "mem.login", 3 => "mem.phone_work", 4 => "log.connected", 5 => "tea.published"));

    $tmpquery = "WHERE tea.project = '$id' AND mem.profil = '3' ORDER BY $block2->sortingValue";
    $listPermitted = new phpCollab\Request();
    $listPermitted->openTeams($tmpquery);
    $comptListPermitted = count($listPermitted->tea_id);

    if ($comptListPermitted != "0") {
        $block2->openResults();
        $block2->labels($labels = array(0 => $strings["full_name"], 1 => $strings["title"], 2 => $strings["user_name"], 3 => $strings["work_phone"], 4 => $strings["connected"], 5 => $strings["published"]), "true");

        for ($i = 0; $i < $comptListPermitted; $i++) {
            if ($listPermitted->tea_mem_phone_work[$i] == "") {
                $listPermitted->tea_mem_phone_work[$i] = $strings["none"];
            }

            $idPublish = $listPermitted->tea_published[$i];
            $block2->openRow();
            $block2->checkboxRow($listPermitted->tea_mem_id[$i]);
            $block2->cellRow($blockPage->buildLink("../users/viewclientuser.php?id=" . $listPermitted->tea_mem_id[$i] . "&organization=" . $projectDetail->pro_organization[0], $listPermitted->tea_mem_name[$i], in));
            $block2->cellRow($listPermitted->tea_mem_title[$i]);
            $block2->cellRow($blockPage->buildLink($listPermitted->tea_mem_email_work[$i], $listPermitted->tea_mem_login[$i], mail));
            $block2->cellRow($listPermitted->tea_mem_phone_work[$i]);

            if ($listPermitted->tea_mem_profil[$i] == "3") {
                $z = "(Client on project site)";
            } else {
                $z = "";
            }

            if ($listPermitted->tea_log_connected[$i] > $dateunix - 5 * 60) {
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

    if ($idSession == $projectDetail->pro_owner[0] || $profilSession == "5") {
        $block2->openPaletteScript();
        $block2->paletteScript(0, "add", "../teams/addclientuser.php?project=$id", "true,false,false", $strings["add"]);
        $block2->paletteScript(1, "remove", "../teams/deleteclientusers.php?project=$id", "false,true,true", $strings["delete"]);

        if ($sitePublish == "true") {
            $block2->paletteScript(2, "add_projectsite", "../projects/viewprojectsite.php?addToSiteTeam=true&project=" . $projectDetail->pro_id[0] . "&action=publish", "false,true,true", $strings["add_project_site"]);
            $block2->paletteScript(3, "remove_projectsite", "../projects/viewprojectsite.php?removeToSiteTeam=true&project=" . $projectDetail->pro_id[0] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
        }
        $block2->closePaletteScript($comptListPermitted, $listPermitted->tea_mem_id);
    }
}

include '../themes/' . THEME . '/footer.php';
?>