<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/deleteproject.php

use phpCollab\Assignments\Assignments;
use phpCollab\Files\Files;
use phpCollab\Notes\Notes;
use phpCollab\Phases\Phases;
use phpCollab\Projects\Projects;
use phpCollab\Support\Support;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use phpCollab\Topics\Topics;

$checkSession = "true";
include_once '../includes/library.php';

$tasks = new Tasks();
$teams = new Teams();
$topics = new Topics();
$files = new Files();
$assignments = new Assignments();
$notes = new Notes();
$support = new Support($logger);
$phases = new Phases();
$projects = new Projects();

$id = ($request->query->get('id')) ? str_replace("**", ",", $request->query->get('id')) : null;

if (empty($id)) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

$listProjects = $projects->getProjectByIdIn($id, 'pro.name');

if (!$listProjects) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

foreach($listProjects as $proj) {
    if ($session->get("idSession") != $proj['pro_owner'] && $session->get("profilSession") != "5") {
        phpCollab\Util::headerFunction("../projects/listprojects.php?msg=projectOwner");
    }
}
unset($proj);

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
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
        }
    } catch (Exception $e) {
        $logger->critical('CSRF Token Error', [
            'edit bookmark' => $request->request->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
        $msg = 'permissiondenied';
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
$block1->openForm("../projects/deleteproject.php?id=" . $id, null, $csrfHandler);

$block1->heading($strings["delete_projects"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

foreach ($listProjects as $proj) {
    $block1->contentRow("#" . $proj['pro_id'], $proj['pro_name']);
}
unset($proj);

$block1->contentRow("", '<button type="submit" name="action" value="delete">' . $strings["delete"] . '</button> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
