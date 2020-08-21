<?php

use phpCollab\Files\Files;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;

$checkSession = "true";
include_once '../includes/library.php';

$files = new Files();

$task = $request->query->get("task");
$project = $request->query->get("project");

$sendto = $request->request->get("sendto");

$id = $request->query->get("id");

$strings = $GLOBALS["strings"];

if (empty($task)) {
    $task = "0";
}


if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "delete") {
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
                    $deleteFile = $files->deleteFile($file['fil_id']);
                }

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

$projects = new Projects();
$projectDetail = $projects->getProjectById($project);

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));

if ($task != "0") {
    $tasks = new Tasks();
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
$block1->openForm("../linkedcontent/deletefiles.php?project={$project}&task={$task}&id={$id}&sendto={$sendto}", null, $csrfHandler);

$block1->heading($strings["unlink_files"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$listFiles = $files->getFiles($id);

foreach ($listFiles as $file) {
    echo <<< HTML
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td>{$file["fil_name"]}</td></tr>
HTML;

}

echo <<<HTML
<tr class="odd">
    <td class="leftvalue">&nbsp;</td>
    <td><button type="submit" name="action" value="delete">{$strings["delete"]}</button> <input type="button" value="{$strings["cancel"]}" onClick="history.back();"></td>
</tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
