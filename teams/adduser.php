<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../teams/adduser.php

$checkSession = "true";
require_once '../includes/library.php';

try {
    $projects = $container->getProjectsLoader();
    $teams = $container->getTeams();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$project = $request->query->get("project");

$projectDetail = $projects->getProjectById($project);

if (empty($projectDetail)) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blank");
}

if ($request->query->get("action") == "add") {
    $id = $request->query->get("id");
    $pieces = explode("**", $id);
    $id = str_replace("**", ",", $id);

    if ($htaccessAuth == "true") {
        $Htpasswd = $container->getHtpasswdService();
        try {
            $Htpasswd->initialize("../files/" . $projectDetail["pro_id"] . "/.htpasswd");
        } catch (Exception $e) {
            $logger->critical('Htpasswd: ' . $e->getMessage());
        }

        for ($i = 0; $i < $comptListMembers; $i++) {
            try {
                $Htpasswd->addUser($listMembers->mem_login[$i], $listMembers->mem_password[$i]);
            } catch (Exception $e) {
                $logger->error($e->getMessage());
                $error = $strings["action_not_allowed"];
            }
        }
    }

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        //  include mantis library
        include '../mantis/core_API.php';
    }

    foreach ($pieces as $piece) {
        try {
            $teams->addTeam($projectDetail["pro_id"], $piece, 1, 0);
        } catch (Exception $e) {
            $logger->error($e->getMessage());
            $error = $strings["action_not_allowed"];
        }

        //if mantis bug tracker enabled
        if ($enableMantis == "true") {
            // Assign user to this project in mantis
            $f_access_level = $team_user_level; // Developer access
            $f_project_id = $projectDetail["pro_id"];
            $f_user_id = $pieces[$i];
            include '../mantis/user_proj_add.php';
        }
    }

    if ($notifications == "true") {
        try {
            $teams->sendAddProjectTeamNotification($projectDetail, $id, $session, $logger);
        } catch (Exception $e) {
            $logger->error($e->getMessage());
            $error = $strings["action_not_allowed"];
        }

    }
    phpCollab\Util::headerFunction("../projects/viewproject.php?id=" . $projectDetail["pro_id"] . "&msg=add");
}

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
    $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../teams/listusers.php?id=" . $projectDetail["pro_id"],
    $strings["team_members"], "in"));
$blockPage->itemBreadcrumbs($strings["add_team"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->form = "atpt";
$block1->openForm("../teams/adduser.php?project=$project#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->heading($strings["add_team"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0, "add", $strings["add"]);
$block1->paletteIcon(1, "info", $strings["view"]);
$block1->paletteIcon(2, "edit", $strings["edit"]);
$block1->closePaletteIcon();

$block1->sorting("users", $sortingUser["users"], "mem.name ASC", $sortingFields = array(
    0 => "mem.name",
    1 => "mem.title",
    2 => "mem.login",
    3 => "mem.phone_work",
    4 => "log.connected"
));

$concatMembers = $teams->getTeamByProjectId($project);

$membersTeam = implode(',', array_column($concatMembers, 'tea_mem_id'));

if ($demoMode == "true") {
    $listMembers = $members->getNonClientMembersExcept($membersTeam . ', 2');
} else {
    $listMembers = $members->getNonClientMembersExcept($membersTeam);
}

if ($listMembers) {
    $block1->openResults();

    $block1->labels($labels = array(
        0 => $strings["full_name"],
        1 => $strings["title"],
        2 => $strings["user_name"],
        3 => $strings["work_phone"],
        4 => $strings["connected"]
    ), "false");

    foreach ($listMembers as $listMember) {
        if ($listMember["mem_phone_work"] == "") {
            $listMember["mem_phone_work"] = $strings["none"];
        }
        $block1->openRow();
        $block1->checkboxRow($listMember["mem_id"]);
        $block1->cellRow($blockPage->buildLink("../users/viewuser.php?id=" . $listMember["mem_id"],
            $listMember["mem_name"], "in"));
        $block1->cellRow($listMember["mem_title"]);
        $block1->cellRow($blockPage->buildLink($listMember["mem_email_work"], $listMember["mem_login"], "in"));
        $block1->cellRow($listMember["mem_phone_work"]);
        if ($listMember["mem_log_connected"] > $dateunix - 5 * 60) {
            $block1->cellRow($strings["yes"] . " " . $z);
        } else {
            $block1->cellRow($strings["no"]);
        }
        $block1->closeRow();
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0, "add", "../teams/adduser.php?project=$project&action=add", "false,true,true",
    $strings["add"]);
$block1->paletteScript(1, "info", "../users/viewuser.php?", "false,true,false", $strings["view"]);
$block1->paletteScript(2, "edit", "../users/edituser.php?", "false,true,false", $strings["edit"]);
$block1->closePaletteScript(count($listMembers), array_column($listMembers, 'mem_id'));

include APP_ROOT . '/views/layout/footer.php';
