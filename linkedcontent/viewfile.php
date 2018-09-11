<?php

$checkSession = "true";
include_once '../includes/library.php';

$action = $_GET["action"];
$id = $_GET["id"];
$addToSiteFile = $_GET["addToSiteFile"];
$removeToSiteFile = $_GET["removeToSiteFile"];
$tableCollab = $GLOBALS["tableCollab"];
$strings = $GLOBALS["strings"];
$idSession = phpCollab\Util::returnGlobal('idSession', 'SESSION');


$files = new \phpCollab\Files\Files();

if ($action == "publish") {
    $file = $_GET["file"];
    if ($addToSiteFile == "true") {
        phpCollab\Util::newConnectSql("UPDATE {$tableCollab["files"]} SET published='0' WHERE id = :file OR vc_parent = :file", ["file" => $file]);
        $msg = "addToSite";
        $id = $file;
    }

    if ($removeToSiteFile == "true") {
        phpCollab\Util::newConnectSql("UPDATE {$tableCollab["files"]} SET published='1' WHERE id = :file OR vc_parent = :file", ["file" => $file]);
        $msg = "removeToSite";
        $id = $file;
    }
}

$fileDetail = $files->getFileById($id);



$teamMember = "false";

$teams = new \phpCollab\Teams\Teams();

$memberTest = $teams->getTeamByProjectIdAndTeamMember($fileDetail["fil_project"], $idSession);

if ($memberTest) {
    $teamMember = "true";
} else {
    $teamMember = "false";
}

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

$projects = new \phpCollab\Projects\Projects();
$projectDetail = $projects->getProjectById($fileDetail["fil_project"]);

if ($fileDetail["fil_task"] != "0") {
    $tasks = new \phpCollab\Tasks\Tasks();
    $taskDetail = $tasks->getTaskById($fileDetail["fil_task"]);
}

if ($projectDetail["pro_phase_set"] != "0") {
    $phases = new \phpCollab\Phases\Phases();
    $phaseDetail = $phases->getPhasesById($fileDetail["fil_phase"]);
}

$fileHandler = new phpCollab\FileHandler();
$type = $fileHandler->fileInfoType($fileDetail["fil_extension"]);
$displayname = $fileDetail["fil_name"];

//---------------------------------------------------------------------------------------------------
//Update file code
if ($action == "update") {
    if ($maxCustom != "") {
        $maxFileSize = $maxCustom;
    }

    if ($_FILES['upload']['size'] != 0) {
        $taille_ko = $_FILES['upload']['size'] / 1024;
    } else {
        $taille_ko = 0;
    }

    if ($_FILES['upload']['name'] == "") {
        $error4 .= $strings["no_file"] . "<br/>";
    }

    if ($_FILES['upload']['size'] > $maxFileSize) {
        if ($maxFileSize != 0) {
            $taille_max_ko = $maxFileSize / 1024;
        }
        $error4 .= $strings["exceed_size"] . " ($taille_max_ko $byteUnits[1])<br/>";
    }

    $upload_name = $fileDetail["fil_name"];
    $extension = strtolower(substr(strrchr($upload_name, "."), 1));

    //Add version number to the old copy's file name.
    $changename = str_replace(".", "_v" . $fileDetail["fil_vc_version"] . ".", $fileDetail["fil_name"]);

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
        $copy_comments_approval = $fileDetail["fil_comments_approval"];
        $copy_approver = $fileDetail["fil_approver"];
        $copy_date_approval = $fileDetail["fil_date_approval"];
        $copy_upload = $fileDetail["fil_upload"];
        $copy_pusblished = $fileDetail["fil_published"];
        $copy_vc_parent = $fileDetail["fil_vc_parent"];
        $copy_id = $fileDetail["fil_id"];
        $copy_vc_version = $fileDetail["fil_vc_version"];

        //Insert a new row for the copied file
        $copy_comments = phpCollab\Util::convertData($copy_comments);

        $tmpquery = "INSERT INTO {$tableCollab["files"]} (owner,project,task,name,date,size,extension,comments,comments_approval,approver,date_approval,upload,published,status,vc_status,vc_version,vc_parent) VALUES (:owner,:project,:task,:name,:date,:size,:extension,:comments,:comments_approval,:approver,:date_approval,:upload,:published,:status,:vc_status,:vc_version,:vc_parent)";

        $dbParams = [];
        $dbParams["owner"] = $idSession;
        $dbParams["project"] = $copy_project;
        $dbParams["task"] = $copy_task;
        $dbParams["name"] = $changename;
        $dbParams["date"] = $copy_date;
        $dbParams["size"] = $copy_size;
        $dbParams["extension"] = $copy_extension;
        $dbParams["comments"] = $copy_comments;
        $dbParams["comments_approval"] = $copy_comments_approval;
        $dbParams["approver"] = (isset($copy_approver)) ? $copy_approver : null;
        $dbParams["date_approval"] = $copy_date_approval;
        $dbParams["upload"] = $copy_upload;
        $dbParams["published"] = $copy_pusblished;
        $dbParams["status"] = 2;
        $dbParams["vc_status"] = 3;
        $dbParams["vc_version"] = $copy_vc_version;
        $dbParams["vc_parent"] = $copy_id;
        
        
        
        $num = phpCollab\Util::newConnectSql($tmpquery, $dbParams);
        unset($dbParams);
    }

    //Insert details into Database
    if ($docopy == "true") {
        phpCollab\Util::uploadFile(".", $_FILES['upload']['tmp_name'], $path);
        $chaine = strrev("$path");
        $tab = explode(".", $chaine);
        $extension = strtolower(strrev($tab[0]));
    }

    $newversion = $fileDetail["fil_vc_version"] + $change_file_version;
    if ($docopy == "true") {
        $name = $upload_name;
        $tmpquery = "UPDATE {$tableCollab["files"]} SET date=:date,size=:size,comments=:comments,comments_approval=null,approver=null,date_approval=null,status=:status,vc_version=:vc_version WHERE id = :file_id";

        phpCollab\Util::newConnectSql($tmpquery, ["date" => $dateheure, "size" => $size,"comments"=>$c,"status"=>$status,"vc_version"=>$vc_version,"file_id"=>$id]);
        phpCollab\Util::headerFunction("../linkedcontent/viewfile.php?id=" . $fileDetail["fil_id"] . "&msg=addFile");
    }
}

# 03/06/2005, MOD, PS (dracono) - approval action

if ($action == "approve") {
    $commentField = phpCollab\Util::convertData($c);
    $tmpquery1 = "UPDATE {$tableCollab["files"]} SET comments_approval=:comments_approval,date_approval=:date_approval,approver=:approver,status=:status WHERE id = :file_id";
    phpCollab\Util::newConnectSql($tmpquery1,["comments_approval" => $commentField, "date_approval" => $dateheure, "approver" => $idSession, "status" => $statusField, "file_id" => $id ]);
    phpCollab\Util::headerFunction("../linkedcontent/viewfile.php?id=" . $fileDetail["fil_id"] . "&msg=addFile");
}

# end MOD

if ($action == "add") {
    if ($maxCustom != "") {
        $maxFileSize = $maxCustom;
    }

    if ($_FILES['upload']['size'] != 0) {
        $taille_ko = $_FILES['upload']['size'] / 1024;
    } else {
        $taille_ko = 0;
    }
    if ($_FILES['upload']['name'] == "") {
        $error3 .= $strings["no_file"] . "<br/>";
    }

    if ($_FILES['upload']['size'] > $maxFileSize) {
        if ($maxFileSize != 0) {
            $taille_max_ko = $maxFileSize / 1024;
        }
        $error3 .= $strings["exceed_size"] . " ($taille_max_ko $byteUnits[1])<br/>";
    }

    $upload_name = $filename;
    //Add version and revision at the end of a file name but before the extension.
    $upload_name = str_replace(".", "_v$oldversion_r$revision.", $upload_name);
    $extension = strtolower(substr(strrchr($upload_name, "."), 1));

    if ($allowPhp == "false") {
        $send = "";
        if ($_FILES['upload']['name'] != "" && ($extension == "php" || $extension == "php3" || $extension == "phtml")) {
            $error3 .= $strings["no_php"] . "<br/>";
            $send = "false";
        }
    }

    if ($_FILES['upload']['name'] != "" && $_FILES['upload']['size'] < $maxFileSize && $_FILES['upload']['size'] != 0 && $send != "false") {
        $docopy = "true";
    }

    //Insert details into Database
    if ($docopy == "true") {
        $c = phpCollab\Util::convertData($c);
        $tmpquery = "INSERT INTO {$tableCollab["files"]} (owner,project,task,comments,upload,published,status,vc_status,vc_parent) VALUES (:owner,:project,:task,:comments,:upload,:published,:status,:vc_status,:vc_parent)";
        $dbParams = [];
        $dbParams["owner"] = $idSession;
        $dbParams["project"] = $project;
        $dbParams["task"] = $task;
        $dbParams["comments"] = $c;
        $dbParams["upload"] = $dateheure;
        $dbParams["published"] = $published;
        $dbParams["status"] = 2;
        $dbParams["vc_status"] = 0;
        $dbParams["vc_parent"] = $parent;

        $num = phpCollab\Util::newConnectSql($tmpquery, $dbParams);
        unset($dbParams);
    }

    if ($task != "0") {
        if ($docopy == "true") {
            phpCollab\Util::uploadFile("files/$project/$task", $_FILES['upload']['tmp_name'], $upload_name);
            $size = phpCollab\Util::fileInfoSize("../files/$project/$task/$upload_name");
            $chaine = strrev("../files/$project/$task/$upload_name");
            $tab = explode(".", $chaine);
            $extension = strtolower(strrev($tab[0]));
        }
    } else {
        if ($docopy == "true") {
            phpCollab\Util::uploadFile("files/$project", $_FILES['upload']['tmp_name'], $upload_name);
            $size = phpCollab\Util::fileInfoSize("../files/$project/$upload_name");

            $chaine = strrev("../files/$project/$upload_name");
            $tab = explode(".", $chaine);
            $extension = strtolower(strrev($tab[0]));
        }
    }

    if ($docopy == "true") {
        $name = $upload_name;
        $tmpquery = "UPDATE {$tableCollab["files"]} SET name=:name,date=:date,size=:size,extension=:extension,vc_version=:vc_version WHERE id = :file_id";
        phpCollab\Util::newConnectSql($tmpquery, ["name" => $name,"date" => $dateheure,"size" => $size,"extension" => $extension,"vc_version" => $oldversion,"file_id" => $num]);
        phpCollab\Util::headerFunction("../linkedcontent/viewfile.php?id=$sendto&msg=addFile");
    }
}

//---------------------------------------------------------------------------------------------------

include '../themes/' . THEME . '/header.php';

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

if ($fileDetail["fil_owner"] == $idSession) {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["ifc_delete_version"]);
    $block1->paletteIcon(1, "add_projectsite", $strings["add_project_site"]);
    $block1->paletteIcon(2, "remove_projectsite", $strings["remove_project_site"]);
    $block1->closePaletteIcon();
}
if ($error1 != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error1);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);


echo <<<DETAILS
<tr class="odd">
	<td valign="top" class="leftvalue">{$strings["type"]} :</td>
	<td><img src="../interface/icones/{$type}" border="0" alt=""></td>
</tr>
<tr class="odd">
	<td valign="top" class="leftvalue">{$strings["name"]} :</td>
	<td>{$fileDetail["fil_name"]}</td>
</tr>
<tr class="odd">
	<td valign="top" class="leftvalue">{$strings["vc_version"]} :</td>
	<td>{$fileDetail["fil_vc_version"]}</td>
</tr>
<tr class="odd">
	<td valign="top" class="leftvalue">{$strings["ifc_last_date"]} :</td>
	<td>{$fileDetail["fil_date"]}</td>
</tr>
<tr class="odd">
	<td valign="top" class="leftvalue">{$strings["size"]} :</td>
DETAILS;
echo "<td>".phpCollab\Util::convertSize($fileDetail["fil_size"])."</td>";
echo <<<DETAILS
</tr>
<tr class="odd">
	<td valign="top" class="leftvalue">{$strings["owner"]} :</td>
DETAILS;
	echo "<td>" . $blockPage->buildLink("../users/viewuser.php?id=" . $fileDetail["fil_mem_id"], $fileDetail["fil_mem_name"], "in") . " (" . $blockPage->buildLink($fileDetail["fil_mem_email_work"], $fileDetail["fil_mem_login"], "mail") . ")</td></tr>";

if ($fileDetail["fil_comments"] != "") {
    echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["comments"] . " :</td><td>" . nl2br($fileDetail["fil_comments"]) . "&nbsp;</td></tr>";
}

$idPublish = $fileDetail["fil_published"];
echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["published"] . " :</td><td>$statusPublish[$idPublish]</td></tr>";

$idStatus = $fileDetail["fil_status"];
echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["approval_tracking"] . " :</td><td>$statusFile[$idStatus]</td></tr>";

if ($fileDetail["fil_mem2_id"] != "") {
    echo "
	<tr class='odd'>
		<td valign='top' class='leftvalue'>" . $strings["approver"] . " :</td>
		<td>" . $blockPage->buildLink("../users/viewuser.php?id=" . $fileDetail["fil_mem2_id"], $fileDetail["fil_mem2_name"], "in") . " (" . $blockPage->buildLink($fileDetail["fil_mem2_email_work"], $fileDetail["fil_mem2_login"], "mail") . ")&nbsp;</td>
	</tr>
	<tr class='odd'>
		<td valign='top' class='leftvalue'>" . $strings["approval_date"] . " :</td>
		<td>" . $fileDetail["fil_date_approval"] . "&nbsp;</td>
	</tr>";
}

if ($fileDetail["fil_comments_approval"] != "") {
    echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["approval_comments"] . " :</td><td>" . nl2br($fileDetail["fil_comments_approval"]) . "&nbsp;</td></tr>";
}

//------------------------------------------------------------------

$listVersions = $files->getFileVersions($id);
$comptListVersions = count($listVersions);

echo <<<VERSIONS_ROW
<tr class="odd">
	<td valign="top" class="leftvalue">{$strings["ifc_version_history"]} :</td>
	<td>
		<table width="600" cellpadding="0" cellspacing="0" class="tableRevision">
VERSIONS_ROW;

$count = 0;
foreach ($listVersions as $version) {
$existFile = false;

    echo '<tr class="'.($count%2 ? "new" : "old").'"><td>';

    if ($fileDetail["fil_owner"] == $idSession && $version["fil_id"] != $fileDetail["fil_id"]) {
        echo "<a href=\"javascript:MM_toggleItem(document." . $block1->form . "Form, '" . $version["fil_id"] . "', '" . $block1->form . "cb" . $version["fil_id"] . "','" . THEME . "')\"><img name=\"" . $block1->form . "cb" . $version["fil_id"] . "\" border=\"0\" src=\"../themes/" . THEME . "/images/checkbox_off_16.gif\" alt=\"\" vspace=\"0\"></a>";
    }
    echo <<<VC_VERSION
        &nbsp;</td>
		<td>{$strings["vc_version"]} : {$version["fil_vc_version"]}</td>
		<td colspan=\"3\">{$displayname}&nbsp;&nbsp;
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
    if ($existFile == "true") {
        echo " " . $blockPage->buildLink("../linkedcontent/accessfile.php?mode=download&id=" . $version["fil_id"], $strings["save"], "in");
    } else {
        echo $strings["missing_file"];
    }

    echo "</td><td>" . $strings["date"] . " : " . $version["fil_date"] . "</td></tr>";

    if ($version["fil_mem2_id"] != "" || $version["fil_comments_approval"] != "") {
        $idStatus = $version["fil_status"];
        echo "<tr class='$class'><td>&nbsp;</td><td colspan='5'>";

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
echo "</table></td></tr>";

//------------------------------------------------------------------
$block1->closeResults();
$block1->closeFormResults();


if ($fileDetail["fil_owner"] == $idSession) {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../linkedcontent/deletefiles.php?project=" . $fileDetail["fil_project"] . "&task=" . $fileDetail["fil_task"] . "&sendto=filedetails", "false,true,true", $strings["ifc_delete_version"]);
    $block1->paletteScript(1, "add_projectsite", "../linkedcontent/viewfile.php?addToSiteFile=true&file=" . $fileDetail["fil_id"] . "&action=publish", "true,true,true", $strings["add_project_site"]);
    $block1->paletteScript(2, "remove_projectsite", "../linkedcontent/viewfile.php?removeToSiteFile=true&file=" . $fileDetail["fil_id"] . "&action=publish", "true,true,true", $strings["remove_project_site"]);
    $block1->closePaletteScript($comptFileDetail, $fileDetail["fil_id"]);
}

if ($peerReview == "true") {
    //Revision list block
    $block2 = new phpCollab\Block();
    $block2->form = "tdC";
    $block2->openForm("../files/viewfile.php?&id=$id#" . $block2->form . "Anchor");
    $block2->heading($strings["ifc_revisions"]);

    if ($fileDetail["fil_owner"] == $idSession) {
        $block2->openPaletteIcon();
        $block2->paletteIcon(0, "remove", $strings["ifc_delete_review"]);
        $block2->closePaletteIcon();
    }

    if (!empty($error2)) {
        $block2->headingError($strings["errors"]);
        $block2->contentError($error2);
    }

    $block2->openContent();
    $block2->contentTitle($strings["details"]);
    echo '<tr class="odd"><td valign="top" class="leftvalue"></td><td><br/>';

    $listReviews = $files->getFilePeerReviews($id);

    $count = 0;
    foreach ($listReviews as $review) {
        //Sort odds and evens for bg color
        if ($count%2) {
            $class = "odd";
            $highlightOff = $oddColor;
        } else {
            $class = "even";
            $highlightOff = $evenColor;
        }

        //Calculate a revision number for display for each listing
        $displayrev = $count + 1;

        echo "	<table width='600' cellpadding='0' cellspacing='0' class='tableRevision' onmouseover='this.style.backgroundColor=\"" . $block2->getHighlightOn() . "\"' onmouseout='this.style.backgroundColor=\"" . $block2->getHighlightOff() . "\"'>
					<tr bgcolor='" . $block2->getFgColor() . "'><td>";
        if ($fileDetail["fil_owner"] == $idSession) {
            echo "<a href=\"javascript:MM_toggleItem(document." . $block2->form . "Form, '" . $review["fil_id"] . "', '" . $block2->form . "cb" . $review["fil_id"] . "','" . THEME . "')\"><img name='" . $block2->form . "cb" . $review["fil_id"] . "' border='0' src='../themes/" . THEME . "/images/checkbox_off_16.gif' alt='' vspace='0'></a>";
        }

        echo '&nbsp;</td><td colspan="3">'.$displayname.'&nbsp;&nbsp;';

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
            echo $strings["missing_file"];
        }

        echo <<<REVISION
        </td><td align="right">Revision: {$displayrev}&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td width="30%">{$strings["ifc_revision_of"]} : {$review["fil_vc_version"]}</td>
                    <td width="40%">{$strings["owner"]} : {$review["fil_mem_name"]}</td>
                    <td colspan="2" align="left" width="30%">{$strings["date"]} : {$review["fil_date"]}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="4">{$strings["comments"]} : {$review["fil_comments"]}</td>
                </tr>
            </table><br/>
REVISION;
        ++$count;
    }

    if ($i == 0) {
        echo "<tr class='odd'><td></td><td>" . $strings["ifc_no_revisions"] . "</td></tr>";
    }
    echo "</table></td></tr>";

    $block2->closeResults();
    $block2->closeFormResults();

    if ($fileDetail["fil_owner"] == $idSession) {
        $block2->openPaletteScript();
        $block2->paletteScript(0, "remove", "../linkedcontent/deletefiles.php?project=" . $fileDetail["fil_project"] . "&task=" . $fileDetail["fil_task"] . "&sendto=filedetails", "false,true,true", $strings["ifc_delete_review"]);
        $block2->closePaletteScript($comptListReviews, $listReviews->fil_id);
    }

    if ($teamMember == "true" || $profilSession == "5") {
        //Add new revision Block
        $block3 = new phpCollab\Block();
        $block3->form = "filedetails";

        echo <<<FORM_START
			<a name="filedetailsAnchor"></a>
			<form accept-charset="UNKNOWN" method="POST" action="../linkedcontent/viewfile.php?action=add&id={$fileDetail["fil_id"]}&#filedetailsAnchor" name="filedetailsForm" enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
				<input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}" />
FORM_START;

        if (!empty($error3)) {
            $block3->headingError($strings["errors"]);
            $block3->contentError($error3);
        }

        $block3->heading($strings["ifc_add_revision"]);
        $block3->openContent();
        $block3->contentTitle($strings["details"]);

        //Add one to the number of current revisions
        $revision = $displayrev + 1;

        echo "
		<input value='" . $fileDetail["fil_id"] . "' name='sendto' type='hidden' />
		<input value='" . $fileDetail["fil_id"] . "' name='parent' type='hidden' />
		<input value='$revision' name='revision' type='hidden' />
		<input value='" . $fileDetail["fil_vc_version"] . "' name='oldversion' type='hidden' />
		<input value='" . $fileDetail["fil_project"] . "' name='project' type='hidden' />
		<input value='" . $fileDetail["fil_task"] . "' name='task' type='hidden' />
		<input value='" . $fileDetail["fil_published"] . "' name='published' type='hidden' />
		<input value='" . $fileDetail["fil_name"] . "' name='filename' type='hidden' />

		<tr class='odd'>
			<td valign='top' class='leftvalue'>* " . $strings["upload"] . " :</td>
			<td><input size='44' style='width: 400px' name='upload' type='FILE'></td>
		</tr>
		
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["comments"] . " :</td>
			<td><textarea rows='3' style='width: 400px; height: 50px;' name='c' cols='43'>$c</textarea></td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>&nbsp;</td>
			<td><input type='SUBMIT' value='" . $strings["save"] . "' /></td>
		</tr>";

        $block3->closeContent();
        $block3->closeForm();
    }
}


# 2005.06.01, MOD, PS (dracono) - approval filed

if ($fileDetail["fil_owner"] == $idSession || $projectDetail["pro_owner"] == $idSession || $profilSession == "5") {
    $block5 = new phpCollab\Block();
    $block5->form = "filedetails";

    echo <<<FILE_DETAIL_FORM_START
		<a name="filedetailsAnchor"></a>
		<form accept-charset="UNKNOWN" method="POST" action="../linkedcontent/viewfile.php?action=approve&amp;id={$fileDetail["fil_id"]}"&amp;#filedetailsAnchor" name="filedetailsForm" enctype="multipart/form-data">
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
				<td valign="top" class="leftvalue">{$strings["status"]} :</td>
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

    echo "</select></td></tr>";
    
    echo <<<COMMENTS
		<tr class="odd">
			<td valign="top" class="leftvalue">{$strings["comments"]} :</td>
			<td><textarea rows="3" style="width: 400px; height: 50px;" name="c" cols="43">
COMMENTS;
if (!empty($c)) {
    echo $c;
}
    echo <<<COMMENTS
            </textarea></td>
		</tr>
		<tr class="odd">
			<td valign="top" class="leftvalue">&nbsp;</td>
			<td><input type="SUBMIT" value="{$strings["save"]}" /></td>
		</tr>
COMMENTS;

    $block5->closeContent();
    $block5->closeForm();
}


# end MOD ---------------------

//Update file Block
if ($fileDetail["fil_owner"] == $idSession) {
    $block4 = new phpCollab\Block();
    $block4->form = "filedetails";

    echo <<<UPDATE_FILE
		<a name="filedetailsAnchor"></a>
		<form accept-charset="UNKNOWN" method="POST" action="../linkedcontent/viewfile.php?action=update&id={$fileDetail["fil_id"]}"&#filedetailsAnchor" name="filedetailsForm" enctype="multipart/form-data">
			<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
			<input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}" />

UPDATE_FILE;

    if (isset($error4) && $error4 != "") {
        $block4->headingError($strings["errors"]);
        $block4->contentError($error4);
    }

    $block4->heading($strings["ifc_update_file"]);
    $block4->openContent();
    $block4->contentTitle($strings["details"]);

    echo <<<UPDATE_FILE_VER
		<tr class="odd">
			<td valign="top" class="leftvalue"></td>
			<td class="odd">{$strings["version_increm"]}<br/>
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="right">0.01</td>
						<td width="30" align="right"><input name="change_file_version" type="radio" value="0.01" /></td>
					</tr>
					<tr>
						<td align="right">0.1</td>
						<td width="30" align="right"><input name="change_file_version" type="radio" value="0.1" checked /></td></tr>
					<tr>
						<td align="right">1.0</td>
						<td width="30" align="right"><input name="change_file_version" type="radio" value="1.0" /></td></tr>
				</table>
			</td>
		</tr>
UPDATE_FILE_VER;


    echo '<tr class="odd"><td valign="top" class="leftvalue">'. $strings["status"] . ' :</td><td><select name="statusField">';

    $comptSta = count($statusFile);

    for ($i = 0; $i < $comptSta; $i++) {
        if ($i == "2") {
            echo "<option value='$i' selected>$statusFile[$i]</option>";
        } else {
            echo "<option value='$i'>$statusFile[$i]</option>";
        }
    }

    echo "</select>";
    $c = isset($c) ? $c : '';
    echo <<<UPDATE_FILE
    </td></tr>
	<tr class="odd"><td valign="top" class="leftvalue">* {$strings["upload"]} :</td><td><input size="44" style="width: 400px" name="upload" type="FILE" /></td></tr>
	<tr class="odd"><td valign="top" class="leftvalue">" {$strings["comments"]} :</td><td><textarea rows="3" style="width: 400px; height: 50px;" name="c" cols="43">{$c}</textarea></td></tr>
	<tr class="odd"><td valign="top" class="leftvalue">&nbsp;</td><td><input type="SUBMIT" value="{$strings["ifc_update_file"]}" /></td></tr>
UPDATE_FILE;


    $block4->closeContent();
    $block4->closeForm();
}
include '../themes/' . THEME . '/footer.php';
