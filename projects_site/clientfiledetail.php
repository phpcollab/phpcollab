<?php
#Application name: PhpCollab
#Status page: 0

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include '../includes/library.php';

$files = $container->getFileUpdateService();

$fileId = $request->query->get("id");

if (empty($fileId)) {
    phpCollab\Util::headerFunction("doclists.php");
}

$fileDetail = $files->getFileById($fileId);

if ($fileDetail["fil_published"] == "1" || $fileDetail["fil_project"] != $session->get("project")) {
    phpCollab\Util::headerFunction("index.php");
}

$fileHandler = new phpCollab\FileHandler();
$type = $fileHandler->fileInfoType($fileDetail["fil_extension"]);

$displayName = $fileDetail["fil_name"];

//---------------------------------------------------------------------------------------------------
//Update file code
if ($request->isMethod('post')) {

    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            $fileDetail["fil_size"] = (!empty($fileDetail["fil_size"])) ? $fileDetail["fil_size"] : 0;

            if ($request->request->get("action") == "update") {
                if (!empty($request->request->get("maxCustom"))) {
                    $maxFileSize = $request->request->get('maxCustom');
                }

                if ($_FILES['upload']['size'] != 0) {
                    $size_kb = $_FILES['upload']['size'] / 1024;
                } else {
                    $size_kb = 0;
                }

                if ($_FILES['upload']['name'] == "") {
                    $error4 .= $strings["no_file"] . "<br/>";
                }
                if ($_FILES['upload']['size'] > $maxFileSize) {
                    if ($maxFileSize != 0) {
                        $size_max_kb = $maxFileSize / 1024;
                    }
                    $error4 .= $strings["exceed_size"] . " ($size_max_kb $byteUnits[1])<br/>";
                }

                $upload_name = $fileDetail["fil_name"];

                $extension = strtolower(substr(strrchr($upload_name, "."), 1));

                //Add version number to the old copy's file name.
                $changename = str_replace(".", " v" . $fileDetail["fil_vc_version"] . ".", $fileDetail["fil_name"]);

                //Generate paths for use further down.
                if ($fileDetail["fil_task"] != "0") {
                    $path = "files/" . $fileDetail["fil_project"] . "/" . $fileDetail["fil_task"] . "/$upload_name";
                    $path_source = "files/" . $fileDetail["fil_project"] . "/" . $fileDetail["fil_task"] . "/" . $fileDetail["fil_name"];
                    $path_destination = "files/" . $fileDetail["fil_project"] . "/" . $fileDetail["fil_task"] . "/$changename";
                } else {
                    $path = "files/" . $fileDetail["fil_project"] . "/$upload_name";
                    $path_source = "files/" . $fileDetail["fil_project"] . "/" . $fileDetail["fil_name"];
                    $path_destination = "files/" . $fileDetail["fil_project"] . "/$changename";
                }

                if ($allowPhp == "false") {
                    $send = "";
                    if ($_FILES['upload']['name'] != "" && ($extension == "php" || $extension == "php3" || $extension == "phtml")) {
                        $error4 .= $strings["no_php"] . "<br/>";
                        $send = "false";
                    }
                }

                if ($_FILES['upload']['name'] != "" && $_FILES['upload']['size'] < $maxFileSize && $_FILES['upload']['size'] != 0 && $send != "false") {
                    $docopy = "true";
                }


                if ($docopy == "true") {

                    //Copy old file with a new file name
                    phpCollab\Util::moveFile($path_source, $path_destination);

                    //Set variables from original files details.
                    $copy_project = $fileDetail["fil_project"];
                    $copy_task = $fileDetail["fil_task"];
                    $copy_date = $fileDetail["fil_date"];
                    $copy_size = $fileDetail["fil_size"];
                    $copy_extension = $fileDetail["fil_extension"];
                    $copy_comments = $fileDetail["fil_comments"];
                    $copy_upload = $fileDetail["fil_upload"];
                    $copy_pusblished = $fileDetail["fil_published"];
                    $copy_vc_parent = $fileDetail["fil_vc_parent"];
                    $copy_id = $fileDetail["fil_id"];
                    $copy_vc_version = $fileDetail["fil_vc_version"];

                    //Insert a new row for the copied file
                    $comments = phpCollab\Util::convertData($request->request->get('comments'));

                    try {
                        $num = $files->add($session->get("id"), $copy_project, $copy_task, $changename, $copy_date,
                            $copy_size,
                            $copy_extension, $copy_comments, null, null, null, $copy_upload, 0, $copy_vc_version,
                            $copy_vc_parent);
                    } catch (Exception $exception) {
                        $logger->error('Project Site (add file)', ['Exception message', $e->getMessage()]);
                        $error1 = $strings["error_file_add"];
                    }
                }

                //Insert details into Database
                if ($docopy == "true") {
                    phpCollab\Util::uploadFile(".", $_FILES['upload']['tmp_name'], $path);
                    $chaine = strrev("$path");
                    $tab = explode(".", $chaine);
                    $extension = strtolower(strrev($tab[0]));
                }

                $newVersion = $fileDetail["fil_vc_version"] + $request->request->get('change_file_version');

                if ($docopy == "true") {
                    $name = "$upload_name";

                    $files->update($request->request->get("comments"), $request->request->get("statusField"),
                        $newVersion, $fileId);

                    $files->setFileSize($fileId, $fileDetail["fil_size"]);

                    phpCollab\Util::headerFunction("clientfiledetail.php?id=" . $fileDetail["fil_id"] . "&msg=addFile");
                }
            }
            //---------------------------------------------------------------------------------------------------


            //---------------------------------------------------------------------------------------------------
            //Add peer review / new revision code
            //---------------------------------------------------------------------------------------------------
            if ($request->request->get("action") == "add") {
                if (!empty($request->request->get('maxCustom'))) {
                    $maxFileSize = $request->request->get('maxCustom');
                }
                if ($_FILES['upload']['size'] != 0) {
                    $size_kb = $_FILES['upload']['size'] / 1024;
                } else {
                    $size_kb = 0;
                }
                if ($_FILES['upload']['name'] == "") {
                    $error3 .= $strings["no_file"] . "<br/>";
                }
                if ($_FILES['upload']['size'] > $maxFileSize) {
                    if ($maxFileSize != 0) {
                        $size_max_kb = $maxFileSize / 1024;
                    }
                    $error3 .= $strings["exceed_size"] . " ($size_max_kb $byteUnits[1])<br/>";
                }

                $upload_name = $request->request->get('filename');

                //Add version and revision at the end of a file name but before the extension.
                $upload_name = str_replace(".",
                    " v" . $request->request->get('oldversion') . " r" . $request->request->get('revision') . ".",
                    $upload_name);

                $extension = strtolower(substr(strrchr($upload_name, "."), 1));

                if ($allowPhp == "false") {
                    $send = "";
                    if ($_FILES['upload']['name'] != "" && ($extension == "php" || $extension == "php3" || $extension == "phtml")) {
                        $error3 .= $strings["no_php"] . "<br/>";
                        $send = "false";
                    }
                }

                if ($_FILES['upload']['name'] != "" && $_FILES['upload']['size'] < $maxFileSize && $_FILES['upload']['size'] != 0 && $send != "false") {
                    //Insert details into Database

                    $comments = phpCollab\Util::convertData($request->request->get("comments"));

                    try {
                        $num = $files->add($session->get("id"), $project, $task, null, null, null, null, $comments,
                            null, null, null, $dateheure, 0, 0, $request->request->get('parent'), 2, 0);

                        if (!empty($num)) {
                            if ($task != "0") {
                                phpCollab\Util::uploadFile("files/$project/$task", $_FILES['upload']['tmp_name'],
                                    $upload_name);
                                $size = phpCollab\Util::fileInfoSize("../files/$project/$task/$upload_name");
                                $chaine = strrev("../files/$project/$task/$upload_name");
                                $tab = explode(".", $chaine);
                                $extension = strtolower(strrev($tab[0]));
                            } else {
                                phpCollab\Util::uploadFile("files/$project", $_FILES['upload']['tmp_name'],
                                    $upload_name);
                                $size = phpCollab\Util::fileInfoSize("../files/$project/$upload_name");
                                $chaine = strrev("../files/$project/$upload_name");
                                $tab = explode(".", $chaine);
                                $extension = strtolower(strrev($tab[0]));
                            }

                            $name = $upload_name;

                            $files->updateFile($num, $name, $dateheure, $size, $extension,
                                $request->request->get('oldversion'));

                            phpCollab\Util::headerFunction("clientfiledetail.php?id={$request->request->get('parent')}&msg=addFile");
                        }
                    } catch (Exception $exception) {
                        $logger->error('Project Site (add file)', ['Exception message', $e->getMessage()]);
                        $error2 = $strings["error_file_add"];
                    }
                }

            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Project Site: Client file detail' => $fileId,
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }

}
//---------------------------------------------------------------------------------------------------

$bouton[4] = "over";
$titlePage = $strings["document"];
include 'include_header.php';

// TABLE 1 - FILE DETAILS TABLE.
$fileSize = phpCollab\Util::convertSize($fileDetail["fil_size"]);

echo <<<FILE_DETAILS
 <table style="width: 100%;">
     <tr>
        <td>
            <h1 class="heading">{$strings["document"]}</h1>
            <table style="width: 90%;">
                <tr>
                    <td style="width: 40%">
                        <table style="width: 100%" class="nonStriped">
                            <tr class="odd">
                                <td class="leftvalue">{$strings["type"]} : </td>
                                <td><img src="../interface/icones/{$type}" alt=""></td>
                            </tr>
                            <tr class="odd">
                                <td class="leftvalue">{$strings["name"]} : </td>
                                <td>{$fileDetail["fil_name"]}</td>
                            </tr>
                            <tr class="odd">
                                <td class="leftvalue">{$strings["vc_version"]} :</td>
                                <td>{$fileDetail["fil_vc_version"]}</td>
                            </tr>
                            <tr class="odd">
                                <td class="leftvalue">{$strings["ifc_last_date"]} :</td>
                                <td>{$fileDetail["fil_date"]}</td>
                            </tr>
                            <tr class="odd">
                                <td class="leftvalue">{$strings["size"]}:</td>
                                <td>{$fileSize}</td>
                            </tr>
                            <tr class="odd">
                                <td class="leftvalue">{$strings["owner"]} :</td>
                                <td><a href="contactdetail.php?id={$fileDetail["fil_mem_id"]}">{$fileDetail["fil_mem_name"]}</a> (<a href="mailto:{$fileDetail["fil_mem_email_work"]}">{$fileDetail["fil_mem_login"]}</a>)</td>
                            </tr>
FILE_DETAILS;

if (!empty($fileDetail["fil_comments"])) {
    $fileComments = nl2br($fileDetail["fil_comments"]);
    echo <<<TR
                            <tr class="odd">
                                <td class="leftvalue">{$strings["comments"]} :</td>
                                <td>{$fileComments}</td>
                            </tr>
TR;
}


$idStatus = $fileDetail["fil_status"];
echo <<<TR
                            <tr class="odd">
                                <td class="leftvalue">{$strings["approval_tracking"]} :</td>
                                <td><a href="docitemapproval.php?id={$fileDetail["fil_id"]}">$statusFile[$idStatus]</a></td>
                            </tr>
TR;

if ($fileDetail["fil_mem2_id"] != "") {
    echo <<<TR
                            <tr class="odd">
                                <td class="leftvalue">{$strings["approver"]} :</td>
                                <td><a href="userdetail.php?id={$fileDetail["fil_mem2_id"]}">{$fileDetail["fil_mem2_name"]}</a> (<a href="mailto:{$fileDetail["fil_mem2_email_work"]}">{$fileDetail["fil_mem2_login"]}</a>)&nbsp;</td>
                            </tr>
                            <tr class="odd">
                                <td class="leftvalue">{$strings["approval_date"]} :</td>
                                <td>{$fileDetail["fil_date_approval"]}&nbsp;</td>
                            </tr>
TR;
}

if ($fileDetail["fil_comments_approval"] != "") {
    $fileApprovalComments = nl2br($fileDetail["fil_comments_approval"]);
    echo <<<TR
                            <tr class="odd">
                                <td class="leftvalue">{$strings["approval_comments"]} :</td>
                                <td>{$fileApprovalComments}&nbsp;</td>
                            </tr>
TR;
}

// Versions Table
//------------------------------------------------------------------
$listVersions = $files->getFileVersions($fileId);
$theme = THEME;
echo <<<TR
                            <tr class="odd">
                                <td class="leftvalue">{$strings["ifc_version_history"]} :</td>
                                <td></td>
                            </tr>
                            <tr class="odd">
                                <td class="leftvalue"></td>
                                <td class="spacerReplacement"></td>
                            </tr>
                            <tr class="odd">
                                <td style="vertical-align: top; text-align: center" colspan="2">
                                    <table style="width: 550px;" class="tableRevision">
TR;

foreach ($listVersions as $version) {
    echo <<<TR
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>{$strings["vc_version"]} : {$version["fil_vc_version"]}</td>
                                            <td>{$displayName}&nbsp;&nbsp;
TR;

    if (!empty($version["fil_task"])) {
        if (file_exists("../files/" . $version["fil_project"] . "/" . $version["fil_task"] . "/" . $version["fil_name"])) {
            echo ' <a href="clientaccessfile.php?mode=view&id=' . $version["fil_id"] . '">' . $strings["view"] . '</a>';
            $folder = $version["fil_project"] . "/" . $version["fil_task"];
            $existFile = "true";
        }
    } else {
        if (file_exists("../files/" . $version["fil_project"] . "/" . $version["fil_name"])) {
            echo ' <a href="clientaccessfile.php?mode=view&id=' . $version["fil_id"] . '">' . $strings["view"] . '</a>';
            $folder = $version["fil_project"];
            $existFile = "true";
        }
    }

    if ($existFile == "true") {
        echo ' <a href="clientaccessfile.php?mode=download&id=' . $version["fil_id"] . '">' . $strings["save"] . '</a>';
    } else {
        echo '<span class="error">' . $strings["missing_file"] . "</span>";
    }

    echo <<<TD
                                            </td>
                                            <td>{$strings["date"]} : {$version["fil_date"]}</td>
                                        </tr>
TD;
}
echo <<<TABLE
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>					  
        </td>
    </tr>
</table>
TABLE;

if ($peerReview == "true") {
    // Table 2 - LIST OF REVIEWS TABLE.
    // --------------------------------
    $listReviews = $files->getFilePeerReviews($fileId);

    echo <<<TABLE
<table style="width: 100%;">
    <tr>
        <td>
            <h1 class="heading">{$strings["ifc_revisions"]}</h1>
TABLE;

    if ($listReviews) {
        echo <<<TABLE
            <table style="width: 90%;">
                <tr>
                    <th class="ModuleColumnHeaderSort"></th>
                </tr>
                <tr>
                    <td style="width: 40%;">
                        <table style="width: 100%;">
                            <tr class="odd">
                                <td style="text-align: center">
TABLE;
        $displayRevision = 0;
        foreach ($listReviews as $review) {
            //Calculate a revision number for display for each listing
            $displayRevision++;


            // Highlight table when mouse over
            echo <<<TABLE
                                    <table style="width: 550px;" class="tableRevision">
                                        <tr class="reviewHeader">
                                            <td>&nbsp;</td>
                                            <td colspan="3">{$displayName} 
TABLE;

            if ($review["fil_task"] != "0") {
                if (file_exists("../files/" . $review["fil_project"] . "/" . $review["fil_task"] . "/" . $review["fil_name"])) {
                    echo '<a href="clientaccessfile.php?mode=view&id=' . $review["fil_id"] . '">' . $strings["view"] . '</a>';
                    $folder = $review["fil_project"] . '/' . $review["fil_task"];
                    $existFile = "true";
                }
            } else {
                if (file_exists("../files/{$review["fil_project"]}/{$review["fil_name"]}")) {
                    echo '<a href="clientaccessfile.php?mode=view&id=' . $review["fil_id"] . '">' . $strings["view"] . '</a>';
                    $folder = $review["fil_project"];
                    $existFile = "true";
                }
            }
            if ($existFile == "true") {
                echo ' <a href="clientaccessfile.php?mode=download&id=' . $review["fil_id"] . '">' . $strings["save"] . '</a>';
            } else {
                echo $strings["missing_file"];
            }

            echo <<<TD
                                            </td>
                                            <td style="text-align: right;">Revision: $displayRevision</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>{$strings["ifc_revision_of"]} : {$review["fil_vc_version"]}</td>
                                            <td style="width: 150px;">{$strings["owner"]} : {$review["fil_mem_name"]}</td>
                                            <td colspan="2">{$strings["date"]} : {$review["fil_date"]}</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td colspan="4">{$strings["comments"]} : <p>{$review["fil_comments"]}</p></td>
                                        </tr>
                                    </table>
TD;
        }

        echo <<<TABLE
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
TABLE;
    } else {
        echo '<div class="no-records">' . $strings["no_items"] . '</div>';
    }

    // Table 3 - ADD REVIEW TABLE.
    //Add one to the number of current revisions
    $revision = $displayRevision + 1;
    echo <<<PEER_REVIEW_FORM
            <form method="POST" 
                action="../projects_site/clientfiledetail.php?id={$fileDetail["fil_id"]}#filedetailsAnchor" 
                name="peerReviewForm"
                enctype="multipart/form-data"
                class="noBorder">
                <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
                <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                <input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}">
                <input value="{$fileDetail["fil_id"]}" name="sendto" type="hidden">
                <input value="{$fileDetail["fil_id"]}" name="parent" type="hidden">
                <input value="{$revision}" name="revision" type="hidden">
                <input value="{$fileDetail["fil_vc_version"]}" name="oldversion" type="hidden">
                <input value="{$fileDetail["fil_project"]}" name="project" type="hidden">
                <input value="{$fileDetail["fil_task"]}" name="task" type="hidden">
                <input value="{$fileDetail["fil_name"]}" name="filename" type="hidden">
                <table style="width: 100%;" class="nonStriped">
                    <tr>
                        <td>
                            <h1 class="heading">{$strings["ifc_add_revision"]} ({$revision})</h1>
                            <table style="width: 90%;" class="nonStriped">
                                <tr>
                                    <th class="ModuleColumnHeaderSort"></th>
                                </tr>
                                <tr>
                                    <td style="width: 40%;">
                                        <a id="filedetailsAnchor"></a>
                                        <table style="width: 100%;" class="nonStriped">
                                            <tr class="odd">
                                                <td class="leftvalue">* {$strings["upload"]} :</td>
                                                <td><input size="44" style="width: 400px" name="upload" type="FILE" required></td>
                                            </tr>
                                            <tr class="odd">
                                                <td class="leftvalue">{$strings["comments"]} :</td>
                                                <td><textarea rows="3" style="width: 400px; height: 50px;" name="comments" cols="43" required>{$comments}</textarea></td>
                                            </tr>
                                            <tr class="odd">
                                                <td class="leftvalue">&nbsp;</td>
                                                <td><button type="submit" name="action" value="add">{$strings["save"]}</button><br/><br/>{$error3}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>					  
                        </td>
                    </tr>
                </table>
            </form>
PEER_REVIEW_FORM;
    echo <<<CLOSE_TABLE
        </td>
    </tr>
</table>
CLOSE_TABLE;

}

// Table 4
// File Update Form
if ($fileDetail["fil_owner"] == $session->get("id")) {
    echo <<<FILE_UPDATE_FORM
<table id="fileVersionUpdate" style="width: 100%" class="nonStriped">
    <tr>
    <td>
        <h1 class="heading">{$strings["ifc_update_file"]}</h1>
        <table style="width: 90%;" class="nonStriped">
            <tr>
                <th style="width: 100%;" class="ModuleColumnHeaderSort"></th>
            </tr>
            <tr>
                <td style="width: 40%;" class="odd">
                    <form method="POST" 
                        action="../projects_site/clientfiledetail.php?id={$fileDetail["fil_id"]}#filedetailsAnchor" 
                        name="filedetailsForm" 
                        enctype="multipart/form-data"
                        class="noBorder">
                        <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
                        <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                        <input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}">
                        <table style="width: 100%" class="nonStriped">
                            <tr class="odd">
                                <td class="leftvalue"></td>
                                <td class="odd">{$strings["version_increm"]}
                                    <table class="nonStriped">
                                        <tr>
                                            <td style="text-align: right;">0.01</td>
                                            <td style="width: 30px; text-align: right;"><input name="change_file_version" type="radio" value="0.01"></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;">0.1</td>
                                            <td style="width: 30px; text-align: right;"><input name="change_file_version" type="radio" value="0.1" checked></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;">1.0</td>
                                            <td style="width: 30px; text-align: right;"><input name="change_file_version" type="radio" value="1.0"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="odd">
                                <td class="leftvalue">{$strings["status"]} :</td>
                                <td>
                                    <select name="statusField">
FILE_UPDATE_FORM;
    $comptSta = count($statusFile);

    for ($i = 0; $i < $comptSta; $i++) {
        if ($fileDetail["fil_status"] == $i) {
            echo "<option value=\"$i\" selected>$statusFile[$i]</option>";
        } else {
            echo "<option value=\"$i\">$statusFile[$i]</option>";
        }
    }
    echo <<< FILE_UPDATE_FORM
                                    </select></td>
                                </tr>
                                <tr class="odd">
                                    <td style="vertical-align: top;" class="leftvalue">* {$strings["upload"]} :</td>
                                    <td><input size="44" style="width: 400px" name="upload" type="FILE"></td>
                                </tr>
                                <tr class="odd">
                                    <td style="vertical-align: top;" class="leftvalue">{$strings["comments"]} :</td>
                                    <td><textarea rows="3" style="width: 400px; height: 50px;" name="comments" cols="43">$comments</textarea></td>
                                </tr>
                                <tr class="odd">
                                    <td style="vertical-align: top;" class="leftvalue">&nbsp;</td>
                                    <td><button type="submit" name="action" value="update">{$strings["ifc_update_file"]}</button><br/><br/>$error4</td>
                                </tr>
                            </form>
                        </table>
                    </td>
                </tr>
            </table>					  
        </td>
    </tr>
</table>
FILE_UPDATE_FORM;
}

include APP_ROOT . "/projects_site/include_footer.php";
