<?php

use phpCollab\Block;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$projectId = $request->query->get("project");
$taskId = $request->query->get("task");

if (empty($projectId) || empty($taskId)) {
    phpCollab\Util::headerFunction("/projects/listprojects.php");
}

try {
    $teams = $container->getTeams();
    $projects = $container->getProjectsLoader();
    $tasks = $container->getTasksLoader();
    $phases = $container->getPhasesLoader();
    $notification = $container->getNotificationsManager();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$teamMember = "false";
$teamMember = $teams->isTeamMember($projectId, $session->get("id"));
if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

$projectDetail = $projects->getProjectById($projectId);

if ($projectDetail["pro_phase_set"] != "0") {
    $phase = $projectDetail["pro_phase_set"];

    $phaseDetail = $phases->getPhasesById($phase);
}

if ($taskId != "0") {
    $taskDetail = $tasks->getTaskById($taskId);
}

/**
 * Review and refactor as needed
 */
if ($request->isMethod('post')) {

    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {

            $files = $container->getFilesLoader();

            // Clean the filename of spaces, slashes, etc
            $filename1 = phpCollab\Util::checkFileName($_FILES['upload']['name']);

            $filename = $request->files->get('upload')->getClientOriginalName();


            // Check to see if the custom maximum file size is set, and if so use it.
            if (!empty($request->request->get("maxCustom"))) {
                $maxFileSize = $request->request->get("maxCustom");
            }

            if (!empty($request->files->get('upload')->getSize())) {
                $taille_ko = $request->files->get('upload')->getSize() / 1024;
            } else {
                $taille_ko = 0;
            }

            if (empty($filename)) {
                $error .= $strings["no_file"] . "<br/>";
            }

            if ($request->files->get('upload')->getSize() > $maxFileSize) {
                if ($maxFileSize != 0) {
                    $taille_max_ko = $maxFileSize / 1024;
                }
                $error .= $strings["exceed_size"] . " ($taille_max_ko $byteUnits[1])<br/>";
            }

            $extension = strtolower(substr(strrchr($filename, "."), 1));

            if ($allowPhp == "false") {
                $send = "";
                if (!empty($filename) && ($extension == "php" || $extension == "php3" || $extension == "phtml")) {
                    $error .= $strings["no_php"] . "<br/>";
                    $send = "false";
                }
            }

            if (!empty($filename)
                && $request->files->get('upload')->getSize() < $maxFileSize
                && $request->files->get('upload')->getSize() != 0
                && $send != "false") {
                $docopy = "true";
            }

            if ($docopy == "true") {

                $versionFile = filter_var($request->request->get("versionFile"), FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION);

                $match = strstr($versionFile, ".");


                if (empty($match)) {
                    $versionFile = $versionFile . ".0";
                }

                if (empty($versionFile)) {
                    $versionFile = "0.0";
                }

                $phase = phpCollab\Util::fixInt($phase);

                $num = $files->addFile($session->get("id"), $projectId, $phase, $taskId,
                    $request->request->get("comments"),
                    $request->request->get("statusField"), $versionFile);

                $fileDetails = $files->getFileById($num);
            }

            if ($taskId != "0") {

                if ($docopy == "true") {
                    phpCollab\Util::uploadFile("files/$projectId/$taskId",
                        $request->files->get('upload')->getPathName(), "$num--" . $filename);
                    $size = phpCollab\Util::fileInfoSize("../files/" . $projectId . "/" . $taskId . "/" . $num . "--" . $filename);
                    $filePath = strrev("../files/" . $projectId . "/" . $taskId . "/" . $num . "--" . $filename);
                    $tab = explode(".", $filePath);
                    $extension = strtolower(strrev($tab[0]));
                }
            } else {
                if ($docopy == "true") {
                    phpCollab\Util::uploadFile("files/$projectId", $request->files->get('upload')->getPathName(),
                        "$num--" . $filename);
                    $size = phpCollab\Util::fileInfoSize("../files/" . $projectId . "/" . $num . "--" . $filename);
                    $filePath = strrev("../files/" . $projectId . "/" . $num . "--" . $filename);
                    $tab = explode(".", $filePath);
                    $extension = strtolower(strrev($tab[0]));
                }
            }

            if ($docopy == "true") {
                $fileDetails = $files->updateFile($num, "$num--$filename", date('Y-m-d h:i'), $size, $extension);

                if ($notifications == "true") {
                    try {
                        // Get a list of notification team members
                        $teamList = $teams->getTeamByProjectId($projectId);

                        $key = array_search($session->get("id"), array_column($teamList, 'tea_mem_id'));

                        // Remove the current user from the TeamList
                        unset($teamList[$key]);

                        foreach ($teamList as $item) {
                            $userNotificationFlags = $notification->getMemberNotifications($item['tea_mem_id']);

                            if ($userNotificationFlags) {
                                $files->sendFileUploadedNotification($fileDetails, $projectDetail,
                                    $userNotificationFlags, $session->get("id"), $session->get("name"),
                                    $session->get("login"));
                            }
                        }
                    } catch (Exception $e) {
                        echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
                    }
                }
                phpCollab\Util::headerFunction("../linkedcontent/viewfile.php?id=$num&msg=addFile");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Linked Content: Add File',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Linked Content Error ' . $e->getMessage(), []);
        $msg = 'permissiondenied';
    }
}

include APP_ROOT . '/views/layout/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=$projectId",
    $projectDetail["pro_name"], "in"));

if ($projectDetail["pro_phase_set"] != "0" && $phase != 0) {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $phaseDetail["pha_id"],
        $phaseDetail["pha_name"], "in"));
}

if ($taskId != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=$projectId", $strings["tasks"],
        "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=$taskId", $taskDetail["tas_name"],
        "in"));
}

$blockPage->itemBreadcrumbs($strings["add_file"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new Block();


$block1->form = "filedetails";

echo '<a id="filedetailsAnchor"></a>';
echo <<<FORM
<form accept-charset="UNKNOWN" method="POST" action="../linkedcontent/addfile.php?project=$projectId&task=$taskId&phase=$phase&" name="filedetailsForm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
    <input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
FORM;


if (isset($error) && !empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["add_file"]);

$block1->openContent();
$block1->contentTitle($strings["details"]);

echo <<<SELECT
<tr class="odd">
    <td style="vertical-align:top" class="leftvalue">{$strings["status"]} :</td>
    <td><select name="statusField">
SELECT;

$comptSta = count($statusFile);

for ($i = 0; $i < $comptSta; $i++) {
    if ($i == "2") {
        echo '<option value="' . $i . '" selected>' . $statusFile[$i] . '</option>';
    } else {
        echo '<option value="' . $i . '">' . $statusFile[$i] . '</option>';
    }
}

echo <<<TABLE
        </select></td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">* {$strings["upload"]} :</td>
        <td><input size="44" style="width: 400px" name="upload" type="file"></td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">{$strings["comments"]} :</td>
        <td><textarea rows="3" style="width: 400px; height: 50px;" name="comments" cols="43">$comments</textarea></td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">{$strings["vc_version"]} :</td>
        <td><input size="44" style="width: 400px" name="versionFile" type="text" value="0.0"></td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td><input type="submit" value="{$strings["save"]}"></td>
    </tr>
TABLE;


$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
