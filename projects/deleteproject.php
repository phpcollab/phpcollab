<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/deleteproject.php

$checkSession = "true";
include_once '../includes/library.php';

$tasks = new \phpCollab\Tasks\Tasks();
$teams = new \phpCollab\Teams\Teams();
$topics = new \phpCollab\Topics\Topics();
$files = new \phpCollab\Files\Files();
$assignments = new \phpCollab\Assignments\Assignments();
$notes = new \phpCollab\Notes\Notes();
$support = new \phpCollab\Support\Support();
$phases = new \phpCollab\Phases\Phases();
$projects = new \phpCollab\Projects\Projects();

if ($enable_cvs == "true") {
    include '../includes/cvslib.php';
}

$id = isset($_GET["id"]) ? str_replace("**", ",", $_GET["id"]) : null;

if (empty($id)) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

$tmpquery = "WHERE pro.id IN($id) ORDER BY pro.name";
$listProjects = new phpCollab\Request();
$listProjects->openProjects($tmpquery);
$comptListProjects = count($listProjects->pro_id);

if ($comptListProjects == "0") {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

if ($idSession != $listProjects->pro_owner[0] && $profilSession != "5") {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=projectOwner");
}

if ($action == "delete") {
    $id = str_replace("**", ",", $id);
    $pieces = explode(",", $id);
    $comptPro = count($pieces);
    for ($i = 0; $i < $comptPro; $i++) {
        if ($fileManagement == "true") {
            phpCollab\Util::deleteDirectory("../files/$pieces[$i]");
        }
        if ($sitePublish == "true") {
            phpCollab\Util::deleteDirectory("project_sites/$pieces[$i]");
        }

        //if CVS repository enabled
        if ($enable_cvs == "true") {
            cvs_delete_repository($pieces[$i]);
        }
    }

    $tmpquery = "WHERE tas.project IN($id)";
    $listTasks = new phpCollab\Request();
    $listTasks->openTasks($tmpquery);
    $comptListTasks = count($listTasks->tas_id);
    for ($i = 0; $i < $comptListTasks; $i++) {
        if ($fileManagement == "true") {
            phpCollab\Util::deleteDirectory("../files/$id/" . $listTasks->tas_id[$i]);
        }
        $tasks .= $listTasks->tas_id[$i];
        if ($i != $comptListTasks - 1) {
            $tasks .= ",";
        }
    }

    $tmpquery = "WHERE topic.project IN($id)";
    $listTopics = new phpCollab\Request();
    $listTopics->openTopics($tmpquery);
    $comptListTopics = count($listTopics->top_id);
    for ($i = 0; $i < $comptListTopics; $i++) {
        $topics .= $listTopics->top_id[$i];
        if ($i != $comptListTopics - 1) {
            $topics .= ",";
        }
    }


    $projects->deleteProject($id);
    $tasks->deleteTasksByProjectId($id);
    $teams->deleteFromTeamsByProjectId($id);
    $topics->deleteTopicWhereProjectIdIn($id);
    $files->deleteFilesByProjectId($id);

    if ($tasks != "") {
        $assignments->deleteAssignmentsByProjectId($id);
        $tasks->deleteSubtasksByProjectId($id);
    }
    if ($topics != "") {
        $topics->deletePostsByProjectId($id);
    }

    $notes->deleteNotesByProjectId($id);
    $support->deleteSupportPostsByProjectId($id);
    $support->deleteSupportPostsByProjectId($id);
    $phases->deletePhasesByProjectId($id);

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        // call mantis function to delete project
        include '../mantis/proj_delete.php';
    }
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=delete");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
$blockPage->itemBreadcrumbs($strings["delete_projects"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../projects/deleteproject.php?action=delete&id=$id");

$block1->heading($strings["delete_projects"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

for ($i = 0; $i < $comptListProjects; $i++) {
    $block1->contentRow("#" . $listProjects->pro_id[$i], $listProjects->pro_name[$i]);
}

$block1->contentRow("", "<input type=\"submit\" name=\"delete\" value=\"" . $strings["delete"] . "\"> <input type=\"button\" name=\"cancel\" value=\"" . $strings["cancel"] . "\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();


include APP_ROOT . '/themes/' . THEME . '/footer.php';
