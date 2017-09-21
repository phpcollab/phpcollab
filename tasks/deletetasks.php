<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../tasks/deletetasks.php

$checkSession = "true";
include_once '../includes/library.php';


if (!isset($_GET["id"]) || $_GET["id"] == "") {
    phpCollab\Util::headerFunction($_SERVER['HTTP_REFERER']);
}
$id = $_GET["id"];

$tasks = new \phpCollab\Tasks\Tasks();
$assignments = new \phpCollab\Assignments\Assignments();
$projects = new \phpCollab\Projects\Projects();

$strings = $GLOBALS["strings"];

if ($_GET["action"] == "delete") {
    $id = str_replace("**", ",", $id);

    $tmpquery = "WHERE tas.id IN($id)";
    $listTasks = new phpCollab\Request();
    $listTasks->openTasks($tmpquery);
    $comptListTasks = count($listTasks->tas_id);

    for ($i = 0; $i < $comptListTasks; $i++) {
        if ($fileManagement == "true") {
            phpCollab\Util::deleteDirectory("../files/" . $listTasks->tas_project[$i] . "/" . $listTasks->tas_id[$i]);
        }
    }
    $tasks->deleteTasks($id);
    $assignments->deleteAssignments($id);
    $tasks->deleteSubTasks($id);

    //recompute number of completed tasks of the project
    $projectDetail = $projects->getProjectById($listTasks->tas_project[0]);

    phpCollab\Util::projectComputeCompletion(
        $listTasks->tas_project[$i],
        $tableCollab["projects"]
    );

    if ($project != "") {
        phpCollab\Util::headerFunction("../projects/viewproject.php?id=$project&msg=delete");
    } else {
        phpCollab\Util::headerFunction("../general/home.php?msg=delete");
    }
}

$projectDetail = $projects->getProjectById($project);

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($project != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
    $blockPage->itemBreadcrumbs($strings["delete_tasks"]);
} else {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php?", $strings["home"], "in"));
    $blockPage->itemBreadcrumbs($strings["my_tasks"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../tasks/deletetasks.php?project=$project&action=delete&id=$id");

$block1->heading($strings["delete_tasks"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$tmpquery = "WHERE tas.id IN($id) ORDER BY tas.name";
$listTasks = new phpCollab\Request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

for ($i = 0; $i < $comptListTasks; $i++) {
    echo '<tr class="odd"><td valign="top" class="leftvalue">#' . $listTasks->tas_id[$i] . '</td><td>' . $listTasks->tas_name[$i] . '</td></tr>';
}

echo <<< TR
<tr class="odd">
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
</tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
