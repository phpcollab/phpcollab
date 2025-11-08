<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../project_site/uploadfile.php
** Authors: Ceam / Fullo / Shaders
**
** =============================================================================
**
**               phpCollab - Project Management
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

use phpCollab\Files\SecureFileUploadValidator;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : " . $strings["upload_file"];

try {
    $projects = $container->getProjectsLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$projectDetail = $projects->getProjectById($session->get("project"));

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "add") {

                $files = $container->getFilesLoader();
                $teams = $container->getTeams();
                $notification = $container->getNotificationsManager();

                // SECURITY: Get uploaded file object
                $uploadedFile = $request->files->get('upload');

                if (!$uploadedFile) {
                    $error .= $strings["no_file"] . "<br/>";
                    $docopy = "false";
                } else {
                    // SECURITY: Use project-defined max file size (not user-controlled)
                    $maxFileSize = !empty($projectDetail["pro_upload_max"])
                        ? $projectDetail["pro_upload_max"]
                        : SecureFileUploadValidator::MAX_FILE_SIZE;

                    // SECURITY: Validate file upload with comprehensive security checks
                    $validation = SecureFileUploadValidator::validate($uploadedFile, $maxFileSize);

                    if (!$validation['valid']) {
                        // Log security validation failures
                        $logger->warning('File upload validation failed (client upload)', [
                            'file' => $uploadedFile->getClientOriginalName(),
                            'user' => $session->get("login"),
                            'project' => $session->get("project"),
                            'errors' => $validation['errors']
                        ]);

                        // Display errors to user
                        foreach ($validation['errors'] as $validationError) {
                            $error .= $validationError . "<br/>";
                        }
                        $docopy = "false";
                    } else {
                        // SECURITY: Generate secure random filename (discard user-provided name)
                        $secureFilename = SecureFileUploadValidator::generateSecureFilename(
                            $validation['extension']
                        );

                        // Store original filename for display purposes only
                        $originalFilename = $uploadedFile->getClientOriginalName();

                        // Get file size for display
                        $taille_ko = $uploadedFile->getSize() / 1024;
                        $extension = $validation['extension'];

                        $docopy = "true";
                    }
                }

                if ($docopy == "true") {
                    $commentsField = phpCollab\Util::convertData($request->request->get('commentsField'));

                    $newFileId = $files->addFile($session->get("id"), $session->get("project"), 0, 0, $commentsField, 2,
                        0.0, 0);

                    // SECURITY: Use secure random filename, not user-provided name
                    phpCollab\Util::uploadFile("files/" . $session->get("project"),
                        $uploadedFile->getPathName(),
                        $secureFilename);

                    $size = phpCollab\Util::fileInfoSize("../files/" . $session->get("project") . "/" . $secureFilename);

                    // SECURITY: Store secure filename (original filename can be stored in comments if needed for display)
                    $fileDetails = $files->updateFile($newFileId, $secureFilename, date('Y-m-d h:i'), $size, $extension);

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
                                    $files->sendFileUploadedNotification($fileDetails, $projectDetail,
                                        $userNotificationFlags, $session->get("id"), $session->get("name"),
                                        $session->get("login"));
                                }
                            }
                        } catch (Exception $e) {
                            $logger->error('Project Site (upload file)', ['Exception message' => $e->getMessage(), 'Mail ErrorInfo' => $mail->ErrorInfo]);
                            $error = $strings["action_not_allowed"];
                        }
                    }

                    phpCollab\Util::headerFunction("doclists.php");
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Project Site: Upload file',
            '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
            '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
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
        <input type="hidden" name="task_id" value="$task">
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
            <td><input name="submit" type="submit" value="{$strings["save"]}"><br/><br/>$error</td>
        </tr>
        </table>
    </form>
FORM;
include("include_footer.php");

