<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../teams/deleteusers.php

use phpCollab\Members\Members;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';

$projects = new Projects();
$members = new Members();
$teams = new Teams();

$id = $request->query->get('id');
$project = $request->query->get('project');
$action = $request->query->get('action');

$projectDetail = $projects->getProjectById($project);

//test exists selected project, redirect to list if not
if (empty($projectDetail)) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blank");
}

$id = str_replace("**", ",", $id);

$listMembers = $members->getMembersByIdIn($id);

if ($action == "delete") {
    if (!empty($listMembers)) {
        if ($htaccessAuth == "true") {
            $Htpasswd = new Htpasswd;
            $Htpasswd->initialize("../files/" . $projectDetail["pro_id"] . "/.htpasswd");

            foreach ($listMembers as $listMember) {
                try {
                    $Htpasswd->deleteUser($listMembers["mem_login"]);
                } catch (Exception $e) {
                    // Log exception
                }
            }
        }
        //if mantis bug tracker enabled
        if ($enableMantis == "true") {
            //  include mantis library
            include '../mantis/core_API.php';
        }

        $multi = strstr($id, ",");
        if ($multi != "") {
            $pieces = explode(",", $id);
            foreach ($pieces as $piece) {

                if ($projectDetail["pro_owner"] != $piece) {
                    $teams->deleteFromTeamsByProjectIdAndMemberId($project, $piece);

                    //if mantis bug tracker enabled
                    if ($enableMantis == "true") {
                        // Unassign multiple user from this project in mantis
                        $f_project_id = $project;
                        $f_user_id = $piece;
                        include '../mantis/user_proj_delete.php';
                    }
                }
                if ($projectDetail["pro_owner"] == $piece) {
                    $foundOwner = "true";
                }
            }
            if ($foundOwner == "true") {
                $msg = "deleteTeamOwnerMix";
            } else {
                $msg = "delete";
            }
        } else {
            if ($projectDetail["pro_owner"] == $id) {
                $msg = "deleteTeamOwner";
            } else {
                $teams->deleteFromTeamsByProjectIdAndMemberId($project, $id);
                $msg = "delete";

                //if mantis bug tracker enabled
                if ($enableMantis == "true") {
                    // Unassign single user from this project in mantis
                    $f_project_id = $project;
                    $f_user_id = $id;
                    include '../mantis/user_proj_delete.php';
                }
            }
        }

        if ($notifications == "true") {
            try {
                $teams->sendRemoveProjectTeamNotification($projectDetail, $id);
            } catch (Exception $e) {
                // log exception
            }
        }

        phpCollab\Util::headerFunction("../projects/viewproject.php?id=$project&msg=$msg");
    }

}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($strings["remove_team"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "crM";
$block1->openForm("../teams/deleteusers.php?project=$project&action=delete&id=$id");

$block1->heading($strings["remove_team"]);

$block1->openContent();
$block1->contentTitle($strings["remove_team_info"]);

foreach ($listMembers as $listMember) {
    $block1->contentRow("#" . $listMember["mem_id"], $listMember["mem_login"] . " (" . $listMember["mem_name"] . ")");
}

$block1->contentRow("", '<input type="SUBMIT" value="' . $strings["remove"] . '">&#160;<input type="BUTTON" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
