<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/deleteprojectsite.php

$checkSession = "true";
include_once '../includes/library.php';

$project = $_GET["project"];
$tableCollab = $GLOBALS["tableCollab"];
$strings = $GLOBALS["strings"];

if ($_GET["action"] == "delete") {
    $tmpquery = "UPDATE {$tableCollab["projects"]} SET published='1' WHERE id = :project_id";
    phpCollab\Util::newConnectSql($tmpquery, ["project_id" => $project]);
    phpCollab\Util::headerFunction("../projects/viewproject.php?id=$project&msg=removeProjectSite");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$projects = new \phpCollab\Projects\Projects();
$projectDetail = $projects->getProjectById($project);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewprojectsite.php?id=" . $projectDetail["pro_id"], $strings["project_site"], "in"));
$blockPage->itemBreadcrumbs($strings["delete_projectsite"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "projectsite_delete";
$block1->openForm("../projects/deleteprojectsite.php?action=delete&project=$project");

$block1->heading($strings["delete_projectsite"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$block1->contentRow("", $projectDetail["pro_name"]);

$block1->contentRow("", "<input type=\"submit\" name=\"delete\" value=\"" . $strings["delete"] . "\"> <input type=\"button\" name=\"cancel\" value=\"" . $strings["cancel"] . "\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
