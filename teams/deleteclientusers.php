<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../teams/deleteclientusers.php

$checkSession = "true";
include_once '../includes/library.php';

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new phpCollab\Request();
$projectDetail->openProjects($tmpquery);
$comptProjectDetail = count($projectDetail->pro_id);

if ($comptProjectDetail == "0") {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blank");
}

if ($action == "delete") {
    $id = str_replace("**", ",", $id);
    $pieces = explode(",", $id);

    if ($htaccessAuth == "true") {
        $Htpasswd = new Htpasswd;
        $Htpasswd->initialize("../files/" . $projectDetail->pro_id[0] . "/.htpasswd");

        $tmpquery = "WHERE mem.id IN($id)";
        $listMembers = new phpCollab\Request();
        $listMembers->openMembers($tmpquery);
        $comptListMembers = count($listMembers->mem_id);

        for ($i = 0; $i < $comptListMembers; $i++) {
            $Htpasswd->deleteUser($listMembers->mem_login[$i]);
        }
    }

//if mantis bug tracker enabled	
    if ($enableMantis == "true") {
        //  include mantis library
        include '../mantis/core_API.php';
    }
    $compt = count($pieces);
    for ($i = 0; $i < $compt; $i++) {
        $tmpquery1 = "DELETE FROM " . $tableCollab["teams"] . " WHERE member = '$pieces[$i]'";
        phpCollab\Util::connectSql("$tmpquery1");
//if mantis bug tracker enabled
        if ($enableMantis == "true") {
// Unassign user from this project in mantis
            $f_project_id = $project;
            $f_user_id = $pieces[$i];
            include '../mantis/user_proj_delete.php';
        }
    }
    if ($notifications == "true") {
        $organization = "";
        include '../teams/noti_removeprojectteam.php';
    }
    phpCollab\Util::headerFunction("../projects/viewprojectsite.php?id=$project&msg=removeClientToSite");
}

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewprojectsite.php?id=" . $projectDetail->pro_id[0], $strings["project_site"], in));
$blockPage->itemBreadcrumbs($strings["remove_team_client"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "crM";
$block1->openForm("../teams/deleteclientusers.php?project=$project&action=delete&id=$id");

$block1->heading($strings["remove_team_client"]);

$block1->openContent();
$block1->contentTitle($strings["remove_team_info"]);

$id = str_replace("**", ",", $id);
$tmpquery = "WHERE mem.id IN($id) ORDER BY mem.name";
$listMembers = new phpCollab\Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);

for ($i = 0; $i < $comptListMembers; $i++) {
    $block1->contentRow("#" . $listMembers->mem_id[$i], $listMembers->mem_login[$i] . " (" . $listMembers->mem_name[$i] . ")");
}

$block1->contentRow("", "<input type=\"SUBMIT\" value=\"" . $strings["delete"] . "\">&#160;<input type=\"BUTTON\" value=\"" . $strings["remove"] . "\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
?>