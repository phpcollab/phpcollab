<?php
$checkSession = "true";
include_once '../includes/library.php';

$files = new \phpCollab\Files\Files();

$action = $_GET["action"];
$task = $_GET["task"];
$project = $_GET["project"];
$sendto = $_POST["sendto"];
$id = $_GET["id"];
$strings = $GLOBALS["strings"];

if ($task == "") {
    $task = "0";
}

if ($action == "delete") {
    $id = str_replace("**", ",", $id);

    $listFiles = $files->getFiles($id);

    foreach ($listFiles as $file) {
        if ($task != "0") {
            if (file_exists("../files/" . $project . "/" . $task . "/" . $file["fil_name"])) {
                phpCollab\Util::deleteFile("files/" . $project . "/" . $task . "/" . $file["fil_name"]);
            }
        } else {
            if (file_exists("../files/" . $project . "/" . $file["fil_name"])) {
                phpCollab\Util::deleteFile("files/" . $project . "/" . $file["fil_name"]);
            }
        }
    }

    $deleteFile = $files->deleteFile($id);

    if ($sendto == "filedetails") {
        phpCollab\Util::headerFunction("../linkedcontent/viewfile.php?id=" . $listFiles["fil_vc_parent"] . "&msg=deleteFile");
    } else {
        if ($task != "0") {
            phpCollab\Util::headerFunction("../tasks/viewtask.php?id=$task&msg=deleteFile");
        } else {
            phpCollab\Util::headerFunction("../projects/viewproject.php?id=$project&msg=deleteFile");
        }
    }
}

$projects = new \phpCollab\Projects\Projects();
$projectDetail = $projects->getProjectById($project);

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));

if ($task != "0") {
    $tasks = new \phpCollab\Tasks\Tasks();
    $taskDetail = $tasks->getTaskById($task);
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail["pro_id"], $strings["tasks"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail["tas_id"], $taskDetail["tas_name"], "in"));
}

$blockPage->itemBreadcrumbs($strings["unlink_files"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "saC";
$block1->openForm("../linkedcontent/deletefiles.php?project=$project&task=$task&action=delete&id=$id&sendto=$sendto");

$block1->heading($strings["unlink_files"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$listFiles = $files->getFiles($id);

foreach ($listFiles as $file) {
    echo '<tr class="odd"><td valign="top" class="leftvalue">&nbsp;</td>';
    echo '<td>' . $file["fil_name"] . '</td></tr>';
}

echo <<<HTML
<tr class="odd">
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td><input type="SUBMIT" value="{$strings["delete"]}">&#160;<input type="BUTTON" value="{$strings["cancel"]}" onClick="history.back();"></td>
</tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
