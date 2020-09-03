<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/deleteprojectsite.php

$checkSession = "true";
include_once '../includes/library.php';

$projectId = $request->query->get('project');
$strings = $GLOBALS["strings"];
$projects = $container->getProjectsLoader();

if ($request->isMethod('post')) {

    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "delete") {
                $projects->publishProject($projectId, false);
                phpCollab\Util::headerFunction("../projects/viewprojectsite.php?id={$projectId}&msg=removeProjectSite");
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

$projectDetail = $projects->getProjectById($projectId);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
    $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewprojectsite.php?id=" . $projectDetail["pro_id"],
    $strings["project_site"], "in"));
$blockPage->itemBreadcrumbs($strings["delete_projectsite"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "projectsite_delete";
$block1->openForm("../projects/deleteprojectsite.php?project=$projectId", null, $csrfHandler);

$block1->heading($strings["delete_projectsite"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$block1->contentRow("", $projectDetail["pro_name"]);

$block1->contentRow("",
    '<button type="submit" name="action" value="delete">' . $strings["delete"] . '</button> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
