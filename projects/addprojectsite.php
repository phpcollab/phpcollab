<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/addprojectsite.php

use phpCollab\Projects\Projects;

$checkSession = "true";
include_once '../includes/library.php';

$projectId = $request->query->get('id');
$strings = $GLOBALS["strings"];

$projects = new Projects();
$projectDetail = $projects->getProjectById($projectId);

if (!$projectDetail) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}
if ($session->get('idSession') != $projectDetail["pro_owner"] && $session->get('profilSession') != "5") {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=projectOwner");
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("create")) {
                $projects->publishProject($projectId, true);
                phpCollab\Util::headerFunction("../projects/viewprojectsite.php?id={$projectId}&msg=createProjectSite");
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
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=$projectId", $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($strings["create_projectsite"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->form = "csdD";
$block1->openForm("../projects/addprojectsite.php?id=" . $projectId, null, $csrfHandler);

$block1->heading($strings["create_projectsite"]);

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=$projectId", $projectDetail["pro_name"], "in"));
if ($projectDetail->pro_org_id[0] == "1") {
    $block1->contentRow($strings["organization"], $strings["none"]);
} else {
    $block1->contentRow($strings["organization"], $blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"], $projectDetail["pro_org_name"], "in"));
}

$block1->contentRow("", '<input type="SUBMIT" name="create" value="' . $strings["create"] . '">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
