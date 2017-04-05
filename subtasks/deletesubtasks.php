<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../subtasks/deletesubtasks.php
** Authors: Ceam / Fullo 
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: deletesubtasks.php
**
** DESC: Screen:  delete a sub task 
**
** HISTORY:
**	05/11/2004	-	fixed 1059973 
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

$tasks = new \phpCollab\Tasks\Tasks();
$assignments = new \phpCollab\Assignments\Assignments();
$phases = new \phpCollab\Phases\Phases();

$id = $_GET["id"];
$strings = $GLOBALS["strings"];

if ($_GET["action"] == "delete") {
    $id = str_replace("**", ",", $id);

    //find parent task
    $tmpquery = "WHERE subtas.id IN($id)";
    $listSubtasks = new phpCollab\Request();
    $listSubtasks->openSubtasks($tmpquery);

    $tasks->deleteSubTasksById($id);
    $assignments->deleteAssignmentsBySubtasks($id);

    //recompute average completion of the task
    phpCollab\Util::taskComputeCompletion(
        $listSubtasks->subtas_task[0],
        $tableCollab["tasks"]);

    if ($task != "") {
        phpCollab\Util::headerFunction("../tasks/viewtask.php?id=$task&msg=delete");
    } else {
        phpCollab\Util::headerFunction("../general/home.php?msg=delete");
    }
}


$taskDetail = $tasks->getTaskById($task);

$project = $taskDetail["tas_project"];

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new phpCollab\Request();
$projectDetail->openProjects($tmpquery);

if ($projectDetail->pro_enable_phase[0] != "0") {
    $tPhase = $taskDetail["tas_parent_phase"];
    if (!$tPhase) {
        $tPhase = '0';
    }
    $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($taskDetail["tas_project"], $tPhase);
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($task != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], "in"));

    if ($projectDetail->pro_phase_set[0] != "0") {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail->pro_id[0], $strings["phases"], "in"));
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase["pha_id"], $targetPhase["pha_name"], "in"));
    }

    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail->pro_id[0], $strings["tasks"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail["tas_id"], $taskDetail["tas_name"], "in"));
    $blockPage->itemBreadcrumbs($strings["delete_subtasks"]);
} else {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php?", $strings["home"], "in"));
    $blockPage->itemBreadcrumbs($strings["my_tasks"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../subtasks/deletesubtasks.php?task=$task&action=delete&id=$id");

$block1->heading($strings["delete_subtasks"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$tmpquery = "WHERE subtas.id IN($id) ORDER BY subtas.name";
$listSubtasks = new phpCollab\Request();
$listSubtasks->openSubtasks($tmpquery);
$comptListSubtasks = count($listSubtasks->subtas_id);

for ($i = 0; $i < $comptListSubtasks; $i++) {
    echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">#" . $listSubtasks->subtas_id[$i] . "</td><td>" . $listSubtasks->subtas_name[$i] . "</td></tr>";
}

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" 
value=\"" . $strings["delete"] . "\"> <input type=\"button\" name=\"cancel\" value=\"" . $strings["cancel"] . "\" 
onClick=\"history.back();\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
