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

$id = isset($_GET["id"]) ? str_replace("**", ",", $_GET["id"]) : null;

if (empty($id)) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

$listProjects = $projects->getProjectByIdIn($id, 'pro.name');

if (!$listProjects) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

foreach($listProjects as $proj) {
    if ($idSession != $proj['pro_owner'] && $profilSession != "5") {
        phpCollab\Util::headerFunction("../projects/listprojects.php?msg=projectOwner");
    }
}
unset($proj);

if ($action == "delete") {

    if ($listProjects) {
        // Loop through the projects and perform the clean-up functionality

        foreach ($listProjects as $proj) {
            // Get tasks for each project
            $listTasks = $tasks->getTasksByProjectId($proj['pro_id']);
            foreach ($listTasks as $task) {
                if ($fileManagement == "true") {
                    phpCollab\Util::deleteDirectory("../files/" . $proj['pro_id'] . "/" . $task['tas_id']);
                    $assignments->deleteAssignmentsByProjectId($proj['pro_id']);
                    $tasks->deleteSubtasksByProjectId($proj['pro_id']);
                }
            }
            unset($task);

            // Get topics for each project and delete posts
            $listTopics = $topics->getTopicsByProjectId($proj['pro_id']);
            if ($listTopics) {
                $topics->deletePostsByProjectId($proj['pro_id']);
            }

            $tasks->deleteTasksByProjectId($proj['pro_id']);
            $teams->deleteFromTeamsByProjectId($proj['pro_id']);
            $topics->deleteTopicWhereProjectIdIn($proj['pro_id']);
            $files->deleteFilesByProjectId($proj['pro_id']);
            $projects->deleteProject($proj['pro_id']);

            $notes->deleteNotesByProjectId($proj['pro_id']);
            $support->deleteSupportPostsByProjectId($proj['pro_id']);
            $support->deleteSupportPostsByProjectId($proj['pro_id']);
            $phases->deletePhasesByProjectId($proj['pro_id']);

            // Delete files
            if ($fileManagement == "true") {
                phpCollab\Util::deleteDirectory("../files/" . $proj['pro_id']);
            }

            if ($sitePublish == "true") {
                phpCollab\Util::deleteDirectory("project_sites/" . $proj['pro_id']);
            }

            //if mantis bug tracker enabled
            if ($enableMantis == "true") {
                // call mantis function to delete project
                include '../mantis/proj_delete.php';
            }

        }
        unset($proj);
        phpCollab\Util::headerFunction("../projects/listprojects.php?msg=delete");
    } else {
        phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], 'in'));
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

foreach ($listProjects as $proj) {
    $block1->contentRow("#" . $proj['pro_id'], $proj['pro_name']);
}
unset($proj);

$block1->contentRow("", '<input type="submit" name="delete" value="' . $strings["delete"] . '"> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
