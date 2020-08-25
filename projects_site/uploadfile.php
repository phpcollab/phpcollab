<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../project_site/uploadfile.php
** Authors: Ceam / Fullo / Shaders
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: uploadfile.php
**
** DESC: Screen: notification class
**
** HISTORY:
** 	26/01/2004	-	added file notification
**  18/02/2005	-	added fix for php 4.3.11 and removed spaces from name
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

use phpCollab\Files\Files;
use phpCollab\Notifications\Notifications;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;

$checkSession = "true";
include '../includes/library.php';

$projects = new Projects();

$projectDetail = $projects->getProjectById($session->get("project"));

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "add") {

                $files = new Files();
                $teams = new Teams();
                $notification = new Notifications();

                // Clean the filename of spaces, slashes, etc
                $filename = phpCollab\Util::checkFileName($_FILES['upload']['name']);

                // Check to see if the custom maximum file size is set, and if so use it.
                if (!empty($request->request->get('maxCustom'))) {
                    $maxFileSize = $request->request->get('maxCustom');
                }

                if ($_FILES['upload']['size'] != 0) {
                    $taille_ko = $_FILES['upload']['size'] / 1024;
                } else {
                    $taille_ko = 0;
                }

                if (empty($filename)) {
                    $error .= $strings["no_file"] . "<br/>";
                }


                if ($_FILES['upload']['size'] > $maxFileSize) {
                    if ($maxFileSize != 0) {
                        $taille_max_ko = $maxFileSize / 1024;
                    }
                    $error .= $strings["exceed_size"] . " ($taille_max_ko $byteUnits[1])<br/>";
                }

                $extension = strtolower(substr(strrchr($filename, "."), 1));

                if ($allowPhp == "false") {
                    $send = "";
                    if ($filename != "" && ($extension == "php" || $extension == "php3" || $extension == "phtml")) {
                        $error .= $strings["no_php"] . "<br/>";
                        $send = "false";
                    }
                }

                if ($filename != "" && $_FILES['upload']['size'] < $maxFileSize && $_FILES['upload']['size'] != 0 && $send != "false") {
                    $docopy = "true";
                }

                if ($docopy == "true") {
                    $commentsField = phpCollab\Util::convertData($request->request->get('commentsField'));

                    $newFileId = $files->addFile($session->get("id"), $session->get("project"), 0, 0, $commentsField, 2, 0.0, 0);

                    phpCollab\Util::uploadFile("files/" . $session->get("project"), $_FILES['upload']['tmp_name'], "$newFileId--" . $filename);

                    $size = phpCollab\Util::fileInfoSize("../files/" . $session->get("project") . "/" . $newFileId . "--" . $filename);

                    $chaine = strrev("../files/" . $session->get("project") . "/" . $newFileId . "--" . $filename);
                    $tab = explode(".", $chaine);

                    $size = phpCollab\Util::fileInfoSize("../files/" . $session->get("project") . "/" . $newFileId . "--" . $filename);

                    $newFileName = $newFileId . "--" . $filename;

                    $fileDetails = $files->updateFile($newFileId, $newFileName, date('Y-m-d h:i'), $size, $extension);

                    if ($notifications == "true") {
                        try {
                            // Get a list of notification team members
                            $teamList = $teams->getTeamByProjectId($session->get("project"));

                            $key = array_search($session->get("id"), array_column($teamList, 'tea_mem_id'));

                            // Remove the current user from the TeamList
                            unset($teamList[$key]);

                            foreach ($teamList as $item) {
                                $userNotificationFlags = $notification->getMemberNotifications($item['tea_mem_id']);

                                if ($userNotificationFlags) {
                                    $files->sendFileUploadedNotification($fileDetails, $projectDetail, $userNotificationFlags, $session->get("id"), $session->get("name"), $session->get("login"));
                                }
                            }
                        } catch (Exception $e) {
                            echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
                        }
                    }

                    phpCollab\Util::headerFunction("doclists.php");
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

$bouton[4] = "over";
$titlePage = $strings["upload_file"];
include 'include_header.php';

echo <<<FORM
    <form method="POST" action="../projects_site/uploadfile.php" name="feedback" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="project_id" value="{$session->get("project")}">
        <input type="hidden" name="task_id" value="{$task}">
        <input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}">
        <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
    
        <table class="nonStriped">
        <tr>
            <th colspan="2">{$strings["upload_form"]}</th>
        </tr>

        <tr>
            <th style="vertical-align: top">{$strings["comments"]} :</th>
            <td><textarea cols="60" name="commentsField" rows="6">{$request->request->get('commentsField')}</textarea></td>
        </tr>

        <tr>
            <th>{$strings["upload"]} :</th>
            <td><input size="35" value="" name="upload" type="file"></td>
        </tr>

        <tr>
            <th>&nbsp;</th>
            <td><input name="submit" type="submit" value="{$strings["save"]}"><br/><br/>{$error}</td>
        </tr>
        </table>
    </form>
FORM;
include("include_footer.php");

