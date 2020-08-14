<?php

use phpCollab\Files\ApprovalTracking;
use phpCollab\Files\Files;
use phpCollab\Files\PeerReview;
use phpCollab\Files\UpdateFile;
use phpCollab\Phases\Phases;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use phpCollab\Notifications\Notifications;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

$action = $request->query->get("action");
$id = $request->query->get("id");
$addToSiteFile = $request->query->get("addToSiteFile");
$removeToSiteFile = $request->query->get("removeToSiteFile");
$strings = $GLOBALS["strings"];

$files = new Files();

if ($action == "publish") {
    $file = $request->query->get("file");
    if ($addToSiteFile == "true") {
        $files->publishFileByIdOrVcParent($file);
        $msg = "addToSite";
        $id = $file;
    }

    if ($removeToSiteFile == "true") {
        $files->unPublishFileByIdOrVcParent($file);
        $msg = "removeToSite";
        $id = $file;
    }
}

$fileDetail = $files->getFileById($id);

$teamMember = "false";

$teams = new Teams();

$notification = new Notifications();

$teamMember = $teams->isTeamMember($fileDetail["fil_project"], $session->get("idSession"));

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

$projects = new Projects();
$projectDetail = $projects->getProjectById($fileDetail["fil_project"]);

if ($fileDetail["fil_task"] != "0") {
    $tasks = new Tasks();
    $taskDetail = $tasks->getTaskById($fileDetail["fil_task"]);
}

if ($projectDetail["pro_phase_set"] != "0") {
    $phases = new Phases();
    $phaseDetail = $phases->getPhasesById($fileDetail["fil_phase"]);
}

$fileHandler = new phpCollab\FileHandler();
$type = $fileHandler->fileInfoType($fileDetail["fil_extension"]);
$displayname = $fileDetail["fil_name"];


if ($request->isMethod('post')) {

    if (!empty($request->request->get("maxCustom"))) {
        $maxFileSize = $request->request->get("maxCustom");
    }

    switch ($action) {
        // Add Peer Review
        case "add":
            if ($request->files->get('upload')
                && !empty($request->files->get('upload')->getClientOriginalName())) {
                $peerReviewClass = new PeerReview();

                $filename = $request->files->get('upload')->getClientOriginalName();

                if (!empty($request->files->get('upload')->getSize())) {
                    $size_ko = $request->files->get('upload')->getSize() / 1024;
                } else {
                    $size_ko = 0;
                }

                if ($size_ko > $maxFileSize) {
                    if ($maxFileSize != 0) {
                        $size_max_ko = $maxFileSize / 1024;
                    }
                    $peerReviewErrors .= $strings["exceed_size"] . " ($size_max_ko $byteUnits[1])<br/>";
                    break;
                }

                $originalFileName = $fileDetail["fil_name"];

                //Add version and revision at the end of a file name but before the extension.
                $originalFileName = str_replace(".", "_v" . $request->request->get("oldversion") . $request->request->get("version") . ".", $originalFileName);

                /*
                 * Check to make sure the new uploaded file is not a PHP file
                 */
                $extension = $request->files->get('upload')->getClientOriginalExtension();

                if ($allowPhp == "false") {
                    if (!empty($filename) && ($extension == "php" || $extension == "php3" || $extension == "phtml")) {
                        $peerReviewErrors .= $strings["no_php"] . "<br/>";
                        break;
                    }
                }


                if (!empty($filename)
                    && $request->files->get('upload')->getSize() < $maxFileSize
                    && $request->files->get('upload')->getSize() != 0) {
                    $docopy = "true";
                }

                //Insert details into Database
                if ($docopy == "true") {
                    $num = $files->addFile($session->get("idSession"), $project, 0, $task, $comments, 2, 0, $parent);
                }

                if ($task != "0") {
                    if ($docopy == "true") {
                        Util::uploadFile("files/$project/$task", $_FILES['upload']['tmp_name'], $originalFileName);
                        $size = Util::fileInfoSize("../files/$project/$task/$originalFileName");
                        $chaine = strrev("../files/$project/$task/$originalFileName");
                        $tab = explode(".", $chaine);
                        $extension = strtolower(strrev($tab[0]));
                    }
                } else {
                    if ($docopy == "true") {
                        Util::uploadFile("files/$project", $_FILES['upload']['tmp_name'], $originalFileName);
                        $size = Util::fileInfoSize("../files/$project/$originalFileName");

                        $chaine = strrev("../files/$project/$originalFileName");
                        $tab = explode(".", $chaine);
                        $extension = strtolower(strrev($tab[0]));
                    }
                }

                if ($docopy == "true") {
                    $name = $originalFileName;

                    $fileDetails = $files->updateFile($num, $name, date('Y-m-d h:i'), $size, $extension, $request->request->get("oldversion"));

                    /**
                     * Send a notification that a new file has been added
                     */

                    if ($notifications == "true") {
                        /**
                         * Set these flags and values since they are the same for each notification
                         */
                        $peerReviewClass->setNotifications(true);
                        $peerReviewClass->setFileDetails($fileDetail);
                        $peerReviewClass->setProjectDetails($projectDetail);

                        try {
                            // Get a list of notification team members
                            $teamList = $teams->getTeamByProjectId($fileDetail["fil_project"]);

                            $key = array_search($session->get("idSession"), array_column($teamList, 'tea_mem_id'));

                            // Remove the current user from the teamList so we don't spam them
                            unset($teamList[$key]);

                            foreach ($teamList as $item) {
                                $userNotificationFlags = $notification->getMemberNotifications($item['tea_mem_id']);

                                if ($item["tea_mem_profile"] != 4 && $userNotificationFlags && $userNotificationFlags["uploadFile"] == "0") {
                                    $peerReviewClass->sendEmail($userNotificationFlags, $comments);
                                }
                            }
                        } catch (Exception $e) {
                            echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
                        }
                    }

                    unset($comments);
                }
            } else {
                $peerReviewErrors .= $strings["no_file"] . "<br/>";
                break;
            }
            break;
        // Approval Tracking
        case "approve":
            /**
             * Approval tracking functionality
             */
            $approvalTracking = new ApprovalTracking();

            $commentField = Util::convertData($request->request->get("approval_comment"));
            $statusField = $request->request->get("statusField");

            try {
                $approvalTracking->addApproval($session->get("idSession"), $commentField, $id, $statusField);

                if ($notifications == "true") {
                    /**
                     * Set these flags and values since they are the same for each notification
                     */
                    $approvalTracking->setNotifications(true);
                    $approvalTracking->setFileDetails($fileDetail);
                    $approvalTracking->setProjectDetails($projectDetail);

                    try {
                        // Get a list of notification team members
                        $teamList = $teams->getTeamByProjectId($fileDetail["fil_project"]);

                        $key = array_search($session->get("idSession"), array_column($teamList, 'tea_mem_id'));

                        // Remove the current user from the TeamList so we don't spam them
                        unset($teamList[$key]);

                        foreach ($teamList as $item) {
                            $userNotificationFlags = $notification->getMemberNotifications($item['tea_mem_id']);

                            if ($userNotificationFlags && $userNotificationFlags["uploadFile"] == "0") {
                                $approvalTracking->sendEmail($item, $commentField, $statusFile[$statusField], $session->get('loginSession'), $session->get('nameSession'));
                            }
                        }
                    } catch (Exception $e) {
                        echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
                    }
                }
            } catch (Exception $exception) {
                error_log("Error adding file approval: " . $exception->getMessage(), 3, APP_ROOT . "logs/phpcollab.log");
            }

            Util::headerFunction("../linkedcontent/viewfile.php?id=" . $fileDetail["fil_id"] . "&msg=addFile");
            break;
        // Update File
        case "update":
            /*
             * Check to see if the file Obj exists and the name and size is not empty, set error if they are
             */
            if ($request->files->get('upload')
                && !empty($request->files->get('upload')->getClientOriginalName())
                && !empty($request->files->get('upload')->getSize())
            ) {

                try {
                    $filename = $request->files->get('upload')->getClientOriginalName();

                    $newVersion2 = $request->request->get("currentVersion") + $request->request->get("change_file_version");

                    /*
                     * Check to make sure the new uploaded file is not a PHP file
                     * note: $allowPhp comes from settings.php
                     */
                    $extension = $request->files->get('upload')->getClientOriginalExtension();
                    if ($allowPhp == "false") {
                        if (($extension == "php" || $extension == "php3" || $extension == "phtml")) {
                            $updateError .= $strings["no_php"] . "<br/>";
                            break;
                        }
                    }

                    $updateFile = new UpdateFile();
                    $uploadedFile = "{$fileDetail["fil_id"]}--{$filename}";
                    $commentField = Util::convertData($request->request->get("update_comments"));
                    $statusField = filter_var($request->request->get("update_statusField"), FILTER_VALIDATE_INT);

                    $tmpFile = $request->files->get("upload");
                    $tmpPath = $request->files->get("upload")->getPathName();

                    $tmp_name = $_FILES['upload']['tmp_name'];

                    // Convert size to KB
                    $size_ko = $request->files->get('upload')->getSize() / 1024;

                    // Check to see if the file size exceeds the maxFileSize
                    // Note: $maxFileSize is defined in settings.php
                    if ($size_ko > $maxFileSize) {
                        if ($maxFileSize != 0) {
                            $size_max_ko = $maxFileSize / 1024;
                        }
                        $updateError .= $strings["exceed_size"] . " ($size_max_ko $byteUnits[1])<br/>";
                        break;
                    }

                    $originalFileName = $fileDetail["fil_name"];

                    //Add version number to the old copy's file name.
                    $pos = strrpos($fileDetail["fil_name"], ".");
                    if ($pos !== false) {
                        $changedName = substr_replace($fileDetail["fil_name"], "_v{$fileDetail["fil_vc_version"]}.", $pos, 1);
                    }

                    // If the parent file, with _v0.0, do not exist, then move it.
                    if (!file_exists(APP_ROOT . "files/{$fileDetail["fil_project"]}/{$changedName}")
                        && !file_exists(APP_ROOT . "files/{$fileDetail["fil_project"]}/{$fileDetail["fil_task"]}/{$changedName}")
                    ) {
                        if ($fileDetail["fil_task"] != "0") {
                            $path = "files/{$fileDetail["fil_project"]}/{$fileDetail["fil_task"]}/{$originalFileName}";
                            $path_source = "files/{$fileDetail["fil_project"]}/{$fileDetail["fil_task"]}/{$fileDetail["fil_name"]}";
                            $path_destination = "files/{$fileDetail["fil_project"]}/{$fileDetail["fil_task"]}/{$changedName}";
                        } else {
                            $path = "files/{$fileDetail["fil_project"]}/{$originalFileName}";
                            $path_source = "files/{$fileDetail["fil_project"]}/{$fileDetail["fil_name"]}";
                            $path_destination = "files/{$fileDetail["fil_project"]}/{$changedName}";
                        }

                        //Rename the old file with the new name, created above
                        Util::moveFile($path_source, $path_destination);

                    }

                    $approver = (isset($fileDetail["fil_approver"])) ? $fileDetail["fil_approver"] : 0;

                    // Check if the status is anything but "needs approval", if so then set the approval date to today
                    if ($statusField != "2") {
                        $approver = $session->get("idSession");
                        $approvalDate = date('Y-m-d h:i');
                    } else {
                        $approvalDate = null;
                    }

                    // Get the parent file version and increment if for this file
                    $updatedVersion = $request->request->get("currentVersion") + $request->request->get("change_file_version");
                    $updatedVersion = Util::formatFloat($updatedVersion);

                    $num = $updateFile->add(
                        $session->get("idSession"), // owner
                        $fileDetail["fil_project"], // projectId (inherited from the parent file)
                        $fileDetail["fil_task"], // taskId (inherited from the parent file)
                        $uploadedFile, // name of the uploaded file with parent file ID prepended
                        date('Y-m-d h:i'), // Todays date, since this is the date the file is added
                        $request->files->get('upload')->getSize(), // Size of updated file
                        $extension,
                        $commentField, // Comments from the update form
                        $approver,  // who is approving
                        Util::convertData($fileDetail["fil_comments_approval"]), // original file comments
                        $approvalDate,
                        date('Y-m-d h:i'), // uploaded date
                        $fileDetail["fil_published"], // published
                        $updatedVersion,
                        $fileDetail["fil_id"] // parentFileId
                    );

                    // Add the original file ID to the beginning of the name, so it should be: xx--uploaded_file_name.ext
                    $pos = strrpos($fileDetail["fil_name"], ".");
                    if ($pos !== false) {
                        $uploadedFile = substr_replace($uploadedFile, "_v{$updatedVersion}.", $pos, 1);
                    }

                    if ($fileDetail["fil_task"] != "0") {
                        Util::uploadFile("files/{$fileDetail["fil_project"]}/{$fileDetail["fil_task"]}", $request->files->get("upload")->getPathName(), $uploadedFile);
                    } else {
                        Util::uploadFile("files/{$fileDetail["fil_project"]}", $request->files->get("upload")->getPathName(), $uploadedFile);
                    }

                    $newVersion = $fileDetail["fil_vc_version"] + $request->request->get("change_file_version");

                    /**
                     * Send a notification that a new file has been added
                     */
                    if ($notifications == "true") {
                        /**
                         * Set these flags and values since they are the same for each notification
                         */
                        $updateFile->setNotifications(true);
                        $updateFile->setFileDetails($fileDetail);
                        $updateFile->setProjectDetails($projectDetail);

                        try {
                            // Get a list of notification team members
                            $teamList = $teams->getTeamByProjectId($fileDetail["fil_project"]);

                            $key = array_search($session->get("idSession"), array_column($teamList, 'tea_mem_id'));

                            // Remove the current user from the teamList so we don't spam them
                            unset($teamList[$key]);

                            foreach ($teamList as $item) {
                                $userNotificationFlags = $notification->getMemberNotifications($item['tea_mem_id']);

                                if ($item["tea_mem_profile"] != 4 && $userNotificationFlags && $userNotificationFlags["uploadFile"] == "0") {
                                    $updateFile->sendEmail($userNotificationFlags, $commentField);
                                }
                            }
                        } catch (Exception $e) {
                            echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
                        }
                    }


                    /*
                     * Redirect back to viewfile w/message
                     */
                    Util::headerFunction("../linkedcontent/viewfile.php?id=" . $fileDetail["fil_id"] . "&msg=addFile");
                } catch (Exception $exception) {
                    error_log("Error adding file approval: " . $exception->getMessage(), 3, APP_ROOT . "logs/phpcollab.log");
                }
                break;
            } else {
                $updateError .= $strings["no_file"] . "<br/>";
                break;
            }

        default:
            break;

    }
}

$listVersions = $files->getFileVersions($id);
$currentVersion = max(array_column($listVersions, 'fil_vc_version'));


$setTitle .= " : View File : " . $fileDetail["fil_name"];

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $fileDetail["fil_project"], $projectDetail["pro_name"], "in"));

if ($fileDetail["fil_phase"] != "0" && $projectDetail["pro_phase_set"] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $phaseDetail["pha_id"], $phaseDetail["pha_name"], "in"));
}

if ($fileDetail["fil_task"] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $fileDetail["fil_project"], $strings["tasks"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail["tas_id"], $taskDetail["tas_name"], "in"));
}

$blockPage->itemBreadcrumbs($fileDetail["fil_name"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

//------------------------------------------------------------------------------------------------
//Begining of Display code

//File details block
$block1 = new phpCollab\Block();
$block1->form = "vdC";
$block1->openForm("../files/viewfile.php?&id=$id#" . $block1->form . "Anchor");

$block1->heading($strings["document"]);

if ($fileDetail["fil_owner"] == $session->get("idSession")) {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["ifc_delete_version"]);
    $block1->paletteIcon(1, "add_projectsite", $strings["add_project_site"]);
    $block1->paletteIcon(2, "remove_projectsite", $strings["remove_project_site"]);
    $block1->closePaletteIcon();
}
if (isset($error1) && $error1 != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error1);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

echo <<<DETAILS
<tr class="odd">
	<td style="vertical-align: top"  class="leftvalue">{$strings["type"]} :</td>
	<td><img src="../interface/icones/{$type}" style="border:none;" alt=""></td>
</tr>
<tr class="odd">
	<td style="vertical-align: top"  class="leftvalue">{$strings["name"]} :</td>
	<td>{$fileDetail["fil_name"]}</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top"  class="leftvalue">{$strings["vc_version"]} :</td>
	<td>{$currentVersion}</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top"  class="leftvalue">{$strings["ifc_last_date"]} :</td>
	<td>{$fileDetail["fil_date"]}</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top"  class="leftvalue">{$strings["size"]} :</td>
DETAILS;
echo "<td>" . Util::convertSize($fileDetail["fil_size"]) . "</td>";
echo <<<DETAILS
</tr>
<tr class="odd">
	<td style="vertical-align: top"  class="leftvalue">{$strings["owner"]} :</td>
DETAILS;
echo "<td>" . $blockPage->buildLink("../users/viewuser.php?id=" . $fileDetail["fil_mem_id"], $fileDetail["fil_mem_name"], "in") . " (" . $blockPage->buildLink($fileDetail["fil_mem_email_work"], $fileDetail["fil_mem_login"], "mail") . ")</td></tr>";

if ($fileDetail["fil_comments"] != "") {
    echo "<tr class='odd'><td style='vertical-align: top;' class='leftvalue'>" . $strings["comments"] . " :</td><td>" . nl2br($fileDetail["fil_comments"]) . "&nbsp;</td></tr>";
}

$idPublish = $fileDetail["fil_published"];
echo <<<PUBLISHED_ROW
    <tr class="odd">
        <td class="leftvalue">{$strings["published"]} :</td>
        <td>{$statusPublish[$idPublish]}</td>
    </tr>
PUBLISHED_ROW;

$idStatus = $fileDetail["fil_status"];
echo <<<STATUS_ROW
    <tr class="odd">
        <td class="leftvalue">{$strings["approval_tracking"]} :</td>
        <td>{$statusFile[$idStatus]}</td>
    </tr>
STATUS_ROW;

if ($fileDetail["fil_mem2_id"] != "") {
    echo "
	<tr class='odd'>
		<td style='vertical-align: top;' class='leftvalue'>{$strings["approver"]} :</td>
		<td>" . $blockPage->buildLink("../users/viewuser.php?id=" . $fileDetail["fil_mem2_id"], $fileDetail["fil_mem2_name"], "in") . " (" . $blockPage->buildLink($fileDetail["fil_mem2_email_work"], $fileDetail["fil_mem2_login"], "mail") . ")&nbsp;</td>
	</tr>
	<tr class='odd'>
		<td style='vertical-align: top;' class='leftvalue'>{$strings["approval_date"]} :</td>
		<td>{$fileDetail["fil_date_approval"]}&nbsp;</td>
	</tr>";
}

if ($fileDetail["fil_comments_approval"] != "") {
    echo <<<COMMENTS_APPROVAL
    <tr class='odd'>
        <td style='vertical-align: top;' class='leftvalue'>{$strings["approval_comments"]} :</td>
        <td>
COMMENTS_APPROVAL;
    echo nl2br($fileDetail["fil_comments_approval"]);

    echo <<<COMMENTS_APPROVAL
        &nbsp;</td>
    </tr>
COMMENTS_APPROVAL;
}

//------------------------------------------------------------------

echo <<< VERSIONS_ROW
<tr class="odd">
	<td class="leftvalue">{$strings["ifc_version_history"]} :</td>
	<td>
		<table style="width: 600px;" class="tableRevision">
VERSIONS_ROW;

$count = 0;
$rowClass = '';
    foreach ($listVersions as $version) {
        $existFile = false;

        echo "<tr><td>";

        if ($fileDetail["fil_owner"] == $session->get("idSession") && $version["fil_id"] != $fileDetail["fil_id"]) {
            $theme = THEME;
            echo <<<LINK
                <a href="javascript:MM_toggleItem(document.{$block1->form}Form, '{$version["fil_id"]}', '{$block1->form}cb{$version["fil_id"]}','{$theme}')"><img id="{$block1->form}cb{$version["fil_id"]}" src="../themes/{$theme}/images/checkbox_off_16.gif" alt="checkbox" style="border: none; margin-top: 0;" ></a>
LINK;

        }
        echo <<<VC_VERSION
            &nbsp;</td>
            <td>{$strings["vc_version"]} : {$version["fil_vc_version"]}</td>
            <td colspan="3">{$version["fil_name"]}&nbsp;&nbsp;
VC_VERSION;

        if ($version["fil_task"] != "0") {
            if (file_exists("../files/" . $version["fil_project"] . "/" . $version["fil_task"] . "/" . $version["fil_name"])) {
                echo $blockPage->buildLink("../linkedcontent/accessfile.php?mode=view&id=" . $version["fil_id"], $strings["view"], "inblank");
                $folder = $version["fil_project"] . "/" . $version["fil_task"];
                $existFile = "true";
            }
        } else {
            if (file_exists("../files/" . $version["fil_project"] . "/" . $version["fil_name"])) {
                echo $blockPage->buildLink("../linkedcontent/accessfile.php?mode=view&id=" . $version["fil_id"], $strings["view"], "inblank");
                $folder = $version["fil_project"];
                $existFile = "true";
            }
        }
        if ($existFile) {
            echo " " . $blockPage->buildLink("../linkedcontent/accessfile.php?mode=download&id=" . $version["fil_id"], $strings["save"], "in");
        } else {
            echo <<<HTML
            <span style="color: #f00000;">{$strings["missing_file"]}</span>
HTML;
        }

        echo <<<HTML
                </td>
                <td>{$strings["date"]} : {$version["fil_date"]}</td>
            </tr>
HTML;

        if ($version["fil_mem2_id"] != "" || $version["fil_comments_approval"] != "") {
            $idStatus = $version["fil_status"];
            echo <<<HTML
            <tr>
                <td>&nbsp;</td>
                <td colspan="5">
HTML;

            if ($version["fil_mem2_id"] != "") {
                echo $strings["approver"] . " : " . $blockPage->buildLink("../users/viewuser.php?id=" . $version["fil_mem2_id"], $version["fil_mem2_name"], "in") . " (" . $blockPage->buildLink($version["fil_mem2_email_work"], $version["fil_mem2_login"], "mail") . ")
                    <br/>" . $strings["approval_tracking"] . " :$statusFile[$idStatus]<br/>" . $strings["approval_date"] . " : " . $version["fil_date_approval"] . "&nbsp;
                ";
            }
            if ($version["fil_comments_approval"] != "") {
                echo "<br/>" . $strings["approval_comments"] . " : " . nl2br($version["fil_comments_approval"]) . "&nbsp;";
            }
            echo "</td></tr>";
        }
        ++$count;
    }
echo <<<HTML
        </table></td>
    </tr>
HTML;

//------------------------------------------------------------------
$block1->closeResults();
$block1->closeFormResults();


if ($fileDetail["fil_owner"] == $session->get("idSession")) {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../linkedcontent/deletefiles.php?project=" . $fileDetail["fil_project"] . "&task=" . $fileDetail["fil_task"] . "&sendto=filedetails", "false,true,true", $strings["ifc_delete_version"]);
    $block1->paletteScript(1, "add_projectsite", "../linkedcontent/viewfile.php?addToSiteFile=true&file=" . $fileDetail["fil_id"] . "&action=publish", "true,true,true", $strings["add_project_site"]);
    $block1->paletteScript(2, "remove_projectsite", "../linkedcontent/viewfile.php?removeToSiteFile=true&file=" . $fileDetail["fil_id"] . "&action=publish", "true,true,true", $strings["remove_project_site"]);
    $block1->closePaletteScript(count($fileDetail), array_column($fileDetail, 'fil_id'));
}

/**
 * Peer Review block
 */
if ($peerReview == "true") {
    $peerReviews = $files->getFilePeerReviews($id);

        //Revision list block
        $peerReviewBlock = new phpCollab\Block();
        $peerReviewBlock->form = "tdC";
        $peerReviewBlock->openForm("../files/viewfile.php?&id={$id}#" . $peerReviewBlock->form . "Anchor");
        $peerReviewBlock->heading($strings["ifc_revisions"]);

        if ($fileDetail["fil_owner"] == $session->get("idSession")) {
            $peerReviewBlock->openPaletteIcon();
            $peerReviewBlock->paletteIcon(0, "remove", $strings["ifc_delete_review"]);
            $peerReviewBlock->closePaletteIcon();
        }

        if (!empty($error2)) {
            $peerReviewBlock->headingError($strings["errors"]);
            $peerReviewBlock->contentError($error2);
        }

        $peerReviewBlock->openContent();
        $peerReviewBlock->contentTitle($strings["details"]);

        echo <<<TR
        <tr class="odd">
            <td class="leftvalue"></td>
            <td><br/>
TR;


        $count = 0;
        foreach ($peerReviews as $review) {
            //Sort odds and evens for bg color
            if ($count % 2) {
                $rowClass = "odd";
                $highlightOff = $blockPage->getOddColor();
            } else {
                $rowClass = "even";
                $highlightOff = $blockPage->getEvenColor();
            }

            //Calculate a revision number for display for each listing
            $displayrev = $count + 1;
            echo <<<TABLE
            <table style="width: 600px;" class="tableRevision" 
                onmouseover="this.style.backgroundColor='{$peerReviewBlock->getHighlightOn()}'" 
                onmouseout="this.style.backgroundColor='{$peerReviewBlock->getHighlightOff()}'">
                <tr style="background-color: {$peerReviewBlock->getFgColor()};">
                    <td>
TABLE;


            if ($fileDetail["fil_owner"] == $session->get("idSession")) {
                echo <<<LINK
                    <a href="javascript:MM_toggleItem(document.{$peerReviewBlock->form}Form, '{$review["fil_id"]}', '{$peerReviewBlock->form}cb{$review["fil_id"]}','{$theme}')">
                        <img id="{$peerReviewBlock->form}cb{$review["fil_id"]}" src="../themes/{$theme}/images/checkbox_off_16.gif" alt="" style="border: none; margin-top: 0;">
                    </a>
LINK;

            }

            echo <<< HTML
            &nbsp;</td>
            <td colspan="3">{$displayname}&nbsp;&nbsp;
HTML;

            if ($review["fil_task"] != "0") {
                if (file_exists("../files/" . $review["fil_project"] . "/" . $review["fil_task"] . "/" . $review["fil_name"])) {
                    echo $blockPage->buildLink("../linkedcontent/accessfile.php?mode=view&id=" . $review["fil_id"], $strings["view"], "in");
                    $folder = $review["fil_project"] . "/" . $review["fil_task"];
                    $existFile = "true";
                }
            } else {
                if (file_exists("../files/" . $review["fil_project"] . "/" . $review["fil_name"])) {
                    echo $blockPage->buildLink("../linkedcontent/accessfile.php?mode=view&id=" . $review["fil_id"], $strings["view"], "inblank");
                    $folder = $review["fil_project"];
                    $existFile = "true";
                }
            }

            if ($existFile == "true") {
                echo " " . $blockPage->buildLink("../linkedcontent/accessfile.php?mode=download&id=" . $review["fil_id"], $strings["save"], "inblank");
            } else {
                echo <<<HTML
            <span style="color: #f00000;">{$strings["missing_file"]}</span>
HTML;
            }

            echo <<<REVISION
            </td>
            <td style="text-align: right">Revision: {$displayrev}&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td style="width: 30%;">{$strings["ifc_revision_of"]} : {$review["fil_vc_version"]}</td>
                        <td style="width: 40%;">{$strings["owner"]} : {$review["fil_mem_name"]}</td>
                        <td colspan="2" style="width: 30%; text-align: left">{$strings["date"]} : {$review["fil_date"]}</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td colspan="4">{$strings["comments"]} : {$review["fil_comments"]}</td>
                    </tr>
                </table>
                <br/>
REVISION;
            ++$count;
        }

        if (empty($peerReviews)) {
            echo <<<HTML
            <tr class="odd">
                <td></td>
                <td>{$strings["ifc_no_revisions"]}</td>
            </tr>
HTML;

        }
        echo <<<HTML
            </table></td>
        </tr>
HTML;

    $peerReviewBlock->closeResults();
    $peerReviewBlock->closeFormResults();

    if ($fileDetail["fil_owner"] == $session->get("idSession")) {
        $peerReviewBlock->openPaletteScript();
        $peerReviewBlock->paletteScript(0, "remove", "../linkedcontent/deletefiles.php?project=" . $fileDetail["fil_project"] . "&task=" . $fileDetail["fil_task"] . "&sendto=filedetails", "false,true,true", $strings["ifc_delete_review"]);
        $peerReviewBlock->closePaletteScript(count($fileDetail), array_column($peerReviews, 'fil_id'));
    }

    if ($teamMember == "true" || $session->get("profilSession") == "5") {
        //Add new revision Block
        $block3 = new phpCollab\Block();
        $block3->form = "filedetails";

        echo <<<FORM_START
			<a id="filedetailsAnchor"></a>
			<form accept-charset="UNKNOWN" 
			    method="POST" 
			    action="../linkedcontent/viewfile.php?action=add&id={$fileDetail["fil_id"]}&#filedetailsAnchor" 
			    name="filedetailsForm" 
			    enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
				<input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}" />
FORM_START;

        if (!empty($peerReviewErrors)) {
            $block3->headingError($strings["errors"]);
            $block3->contentError($peerReviewErrors);
        }

        $block3->heading($strings["ifc_add_revision"]);
        $block3->openContent();
        $block3->contentTitle($strings["details"]);

        //Add one to the number of current revisions
        $revision = $displayrev + 1;

        echo <<<HTML
		<input value="{$fileDetail["fil_id"]}" name="sendto" type="hidden" />
		<input value="{$fileDetail["fil_id"]}" name="parent" type="hidden" />
		<input value="{$revision}" name="revision" type="hidden" />
		<input value="{$fileDetail["fil_vc_version"]}" name="oldversion" type="hidden" />
		<input value="{$fileDetail["fil_project"]}" name="project" type="hidden" />
		<input value="{$fileDetail["fil_task"]}" name="task" type="hidden" />
		<input value="{$fileDetail["fil_published"]}" name="published" type="hidden" />
		<input value="{$fileDetail["fil_name"]}" name="filename" type="hidden" />

		<tr class="odd">
			<td style="vertical-align: top;" class="leftvalue">* {$strings["upload"]} :</td>
			<td><input size="44" style="width: 400px" name="upload" type="file" required></td>
		</tr>
		
		<tr class="odd">
			<td style="vertical-align: top;" class="leftvalue">{$strings["comments"]} :</td>
			<td><textarea rows="3" style="width: 400px; height: 50px;" name="comments" cols="43">$comments</textarea></td>
		</tr>
		<tr class="odd">
			<td style="vertical-align: top;" class="leftvalue">&nbsp;</td>
			<td><input type="submit" value="{$strings["save"]}" /></td>
		</tr>
HTML;

        $block3->closeContent();
        $block3->closeForm();
    }
}
/**
 * End Peer Review block
 */


/**
 * Approval Tracking
 */
if ($fileDetail["fil_owner"] == $session->get("idSession") || $projectDetail["pro_owner"] == $session->get("idSession") || $session->get("profilSession") == "5") {
    $block5 = new phpCollab\Block();
    $block5->form = "filedetails";

    echo <<<FILE_DETAIL_FORM_START
		<a id="filedetailsAnchor"></a>
		<form accept-charset="UNKNOWN" method="POST" action="../linkedcontent/viewfile.php?action=approve&amp;id={$fileDetail["fil_id"]}&amp;#filedetailsAnchor" name="filedetailsForm" enctype="multipart/form-data">
			<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
			<input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}" />
FILE_DETAIL_FORM_START;

    if (!empty($error5)) {
        $block5->headingError($strings["errors"]);
        $block5->contentError($error5);
    }

    $block5->heading($strings["approval_tracking"]);

    $block5->openContent();
    $block5->contentTitle($strings["details"]);


    echo <<<STATUS
			<tr class="odd">
				<td style="vertical-align: top"  class="leftvalue">{$strings["status"]} :</td>
				<td><select name="statusField">
STATUS;

    $comptSta = count($statusFile);

    for ($i = 0; $i < $comptSta; $i++) {
        if ($i == "2") {
            echo "<option value='$i' selected>$statusFile[$i]</option>";
        } else {
            echo "<option value='$i'>$statusFile[$i]</option>";
        }
    }

    echo <<< HTML
        </select></td>
    </tr>
HTML;

    echo <<<COMMENTS
		<tr class="odd">
			<td style="vertical-align: top"  class="leftvalue">{$strings["comments"]} :</td>
			<td><textarea rows="3" style="width: 400px; height: 50px;" name="approval_comment" cols="43">
COMMENTS;
    if (!empty($comments)) {
        echo $comments;
    }
    echo <<<COMMENTS
</textarea></td>
		</tr>
		<tr class="odd">
			<td style="vertical-align: top"  class="leftvalue">&nbsp;</td>
			<td><input type="SUBMIT" value="{$strings["save"]}" /></td>
		</tr>
COMMENTS;

    $block5->closeContent();
    $block5->closeForm();
}
/**
 * End Approval
 */

/**
 * Update file block
 */
if ($fileDetail["fil_owner"] == $session->get("idSession")) {
    $block4 = new phpCollab\Block();
    $block4->form = "filedetails";

    echo <<<UPDATE_FILE
		<a id="filedetailsAnchor"></a>
		<form accept-charset="UNKNOWN" method="POST" action="../linkedcontent/viewfile.php?action=update&id={$fileDetail["fil_id"]}&#filedetailsAnchor" name="filedetailsForm" enctype="multipart/form-data">
			<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
			<input type="hidden" name="currentVersion" value="{$currentVersion}" />
			<input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}" />
UPDATE_FILE;

    if (isset($updateError) && $updateError != "") {
        $block4->headingError($strings["errors"]);
        $block4->contentError($updateError);
    }

    $block4->heading($strings["ifc_update_file"]);
    $block4->openContent();
    $block4->contentTitle($strings["details"]);

    echo <<<UPDATE_FILE_VER
		<tr class="odd">
			<td style="vertical-align: top"  class="leftvalue"></td>
			<td class="odd">{$strings["version_increm"]}<br/>
				<table style="border: none; border-collapse: collapse;">
					<tr>
						<td style="text-align: right;">0.01</td>
						<td style="text-align: right; width: 30px;"><input name="change_file_version" type="radio" value="0.01" /></td>
					</tr>
					<tr>
						<td style="text-align: right;">0.1</td>
						<td style="text-align: right; width: 30px;"><input name="change_file_version" type="radio" value="0.1" checked /></td></tr>
					<tr>
						<td style="text-align: right;">1.0</td>
						<td style="text-align: right; width: 30px;"><input name="change_file_version" type="radio" value="1.0" /></td></tr>
				</table>
			</td>
		</tr>
UPDATE_FILE_VER;


    echo <<< HTML
    <tr class="odd">
        <td style="vertical-align: top" class="leftvalue">{$strings["status"]} :</td>
        <td><select name="update_statusField">
HTML;

    $comptSta = count($statusFile);

    for ($i = 0; $i < $comptSta; $i++) {
        if ($i == "2") {
            echo "<option value='$i' selected>$statusFile[$i]</option>";
        } else {
            echo "<option value='$i'>$statusFile[$i]</option>";
        }
    }

    echo "</select>";

    $comments = isset($comments) ? $comments : '';
    echo <<<UPDATE_FILE
        </td>
    </tr>
	<tr class="odd">
	    <td style="vertical-align: top"  class="leftvalue">* {$strings["upload"]} :</td>
	    <td><input size="44" style="width: 400px" name="upload" type="file" /></td>
    </tr>
	<tr class="odd">
	    <td style="vertical-align: top"  class="leftvalue">" {$strings["comments"]} :</td>
	    <td><textarea rows="3" style="width: 400px; height: 50px;" name="update_comments" cols="43">{$comments}</textarea></td>
    </tr>
	<tr class="odd">
	    <td style="vertical-align: top"  class="leftvalue">&nbsp;</td>
	    <td><input type="submit" value="{$strings["ifc_update_file"]}" /></td>
    </tr>
UPDATE_FILE;


    $block4->closeContent();
    $block4->closeForm();
}
/**
 * End update file block
 */

include APP_ROOT . '/themes/' . THEME . '/footer.php';
