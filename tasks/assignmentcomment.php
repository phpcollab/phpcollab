<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../tasks/assignmentcomment.php

$checkSession = "true";
include_once '../includes/library.php';

$id = $_GET["id"];
$action = $_GET["action"];
$task = $_GET["task"];
$tableCollab = $GLOBALS["tableCollab"];
$strings = $GLOBALS["strings"];

if ($action == "update") {
    phpCollab\Util::newConnectSql("UPDATE {$tableCollab["assignments"]} SET comments=:comments WHERE id = :assignment_id", ["comments" => phpCollab\Util::convertData($_POST["acomm"]), "assignment_id" => $id]);
    phpCollab\Util::headerFunction("../tasks/viewtask.php?id=$task&msg=update");
}

$bodyCommand = "onLoad=\"document.assignment_commentForm.acomm.focus();\"";
include '../themes/' . THEME . '/header.php';

$tasks = new \phpCollab\Tasks\Tasks();
$taskDetail = $tasks->getTaskById($task);

$projects = new \phpCollab\Projects\Projects();
$projectDetail = $projects->getProjectById($taskDetail["tas_project"]);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail["pro_id"], $strings["tasks"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail["tas_id"], $taskDetail["tas_name"], "in"));
$blockPage->itemBreadcrumbs($strings["assignment_comment"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->form = "assignment_comment";
$block1->openForm("../tasks/assignmentcomment.php?action=update&id=$id&task=$task");

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["assignment_comment"]);

$block1->openContent();
$block1->contentTitle($strings["assignment_comment_info"]);

echo <<<FORM
<tr class="odd"><td valign="top" class="leftvalue">{$strings["task"]} :</td><td>{$taskDetail["tas_name"]}</td></tr>
<tr class="odd"><td valign="top" class="leftvalue">{$strings["comments"]} :</td><td><input style="width: 400px;" maxlength="128" size="44" name="acomm"></input></td></tr>
<tr class="odd"><td valign="top" class="leftvalue">&nbsp;</td><td><input type="submit" name="Save" value="{$strings["save"]}"></td></tr>
FORM;

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
