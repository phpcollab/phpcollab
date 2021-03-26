<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../tasks/assignmentcomment.php

$checkSession = "true";
require_once '../includes/library.php';

$assignmentId = $request->query->get('id');
$taskId = $request->query->get('task');
$strings = $GLOBALS["strings"];

$assignments = $container->getAssignmentsManager();

if ($request->isMethod('post')) {
    if ($request->request->get("action") == "update") {
        $assignments->addAssignmentComment($assignmentId, $request->request->get('comment'));
        phpCollab\Util::headerFunction("../tasks/viewtask.php?id=$taskId&msg=update");

    }
}

$bodyCommand = 'onLoad="document.assignment_commentForm.acomm.focus();"';
include APP_ROOT . '/views/layout/header.php';

$tasks = $container->getTasksLoader();
$taskDetail = $tasks->getTaskById($taskId);

$projects = $container->getProjectsLoader();
$projectDetail = $projects->getProjectById($taskDetail["tas_project"]);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
    $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail["pro_id"],
    $strings["tasks"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail["tas_id"],
    $taskDetail["tas_name"], "in"));
$blockPage->itemBreadcrumbs($strings["assignment_comment"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->form = "assignment_comment";
$block1->openForm("../tasks/assignmentcomment.php?id=$assignmentId&task=" . $taskId, null, $csrfHandler);

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["assignment_comment"]);

$block1->openContent();
$block1->contentTitle($strings["assignment_comment_info"]);

echo <<<FORM
<tr class="odd"><td class="leftvalue">{$strings["task"]} :</td><td>{$taskDetail["tas_name"]}</td></tr>
<tr class="odd"><td class="leftvalue">{$strings["comments"]} :</td><td><input style="width: 400px;" maxlength="128" size="44" name="comment"/></td></tr>
<tr class="odd"><td class="leftvalue">&nbsp;</td><td><button type="submit" name="action" value="update">{$strings["save"]}</button></td></tr>
FORM;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
