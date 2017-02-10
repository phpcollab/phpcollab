<?php


$checkSession = "true";
include_once '../includes/library.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 0;
$addToSite = isset($_GET['addToSite']) ? $_GET['addToSite'] : 0;
$removeToSite = isset($_GET['removeToSite']) ? $_GET['removeToSite'] : 0;
$addToSiteFile = isset($_GET['addToSiteFile']) ? $_GET['addToSiteFile'] : 0;
$removeToSiteFile = isset($_GET['removeToSiteFile']) ? $_GET['removeToSiteFile'] : 0;
$phase = isset($_GET['phase']) ? $_GET['phase'] : 0;

$tableCollab = $GLOBALS["tableCollab"];
$phaseStatus = $GLOBALS["phaseStatus"];
$statusFile = $GLOBALS["statusFile"];
$statusPublish = $GLOBALS["statusPublish"];
$strings = $GLOBALS["strings"];
$msgLabel = $GLOBALS["msgLabel"];
$listPhases = $GLOBALS["listPhases"];
$idSession = $_SESSION["idSession"];

$tasks = new \phpCollab\Tasks\Tasks();
$files = new \phpCollab\Files\Files();
$phases = new \phpCollab\Phases\Phases();
$projects = new \phpCollab\Projects\Projects();
$teams = new \phpCollab\Teams\Teams();

if ($action == "publish") {
    if ($addToSite == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $tasks->publishTasks($id);

        $msg = "addToSite";
        $id = $phase;
    }

    if ($removeToSite == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $tasks->unPublishTasks($id);
        $msg = "removeToSite";
        $id = $phase;
    }

    if ($addToSiteFile == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }

        $files->publishFile($id);

        $msg = "addToSite";
        $id = $phase;
    }

    if ($removeToSiteFile == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $files->unPublishFile($id);
        $msg = "removeToSite";
        $id = $phase;
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

//$tmpquery = "WHERE pha.id = '$id'";
//$phaseDetail = new phpCollab\Request();
//$phaseDetail->openPhases($tmpquery);
$phaseDetail = $phases->getPhasesById($id);

$project = $phaseDetail["pha_project_id"];
$phase = $phaseDetail["pha_id"];

//$tmpquery = "WHERE pro.id = '$project'";
//$projectDetail = new phpCollab\Request();
//$projectDetail->openProjects($tmpquery);
$projectDetail = $projects->getProjectById($project);

$teamMember = "false";
//$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
//$memberTest = new phpCollab\Request();
//$memberTest->openTeams($tmpquery);
//$comptMemberTest = count($memberTest->tea_id);
//if ($comptMemberTest == "0") {
//    $teamMember = "false";
//} else {
//    $teamMember = "true";
//}
$teamMember = $teams->isTeamMember($project, $idSession);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail["pro_id"], $strings["phases"], "in"));
$blockPage->itemBreadcrumbs($phaseDetail["pha_name"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "pppD";
$block1->openForm("../projects/listprojects.php#" . $block1->form . "Anchor");
$block1->headingToggle($strings["phase"] . " : " . $phaseDetail["pha_name"]);

if ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "edit", $strings["edit"]);
    $block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"], $phaseDetail["pha_name"]);
$block1->contentRow($strings["phase_id"], $phaseDetail["pha_id"]);
$block1->contentRow($strings["status"], $phaseStatus[$phaseDetail["pha_status"]]);

$parentPhase = $phaseDetail["pha_order_num"];
//$tmpquery = "WHERE tas.project = '$project' AND tas.parent_phase = '$parentPhase'";
//$countPhaseTasks = new phpCollab\Request();
//$countPhaseTasks->openTasks($tmpquery);
$countPhaseTasks = $tasks->getTasksByProjectIdAndParentPhase($project, $parentPhase);
$comptlistTasks = count($countPhaseTasks);

$comptlistTasksRow = 0;
$comptUncompleteTasks = 0;

foreach ($countPhaseTasks as $task) {
//for ($k = 0; $k < $comptlistTasks; $k++) {
    if (in_array($task["tas_status"], [2,3,4])) {
        $comptUncompleteTasks = $comptUncompleteTasks + 1;
    }
}

$block1->contentRow($strings["total_tasks"], $comptlistTasks);
$block1->contentRow($strings["uncomplete_tasks"], $comptUncompleteTasks);
$block1->contentRow($strings["date_start"], $phaseDetail["pha_date_start"]);
$block1->contentRow($strings["date_end"], $phaseDetail["pha_date_end"]);
$block1->contentRow($strings["comments"], nl2br($phaseDetail["pha_comments"]));

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "edit", "../phases/editphase.php?id=$id", "true,true,true", $strings["edit"]);
    $block1->closePaletteScript($comptlistTasks, $listPhases->pha_id);

}

$block2 = new phpCollab\Block();

$block2->form = "saP";
$block2->openForm("../phases/viewphase.php?&id=$id#" . $block2->form . "Anchor");

$block2->headingToggle($strings["tasks"]);

$block2->openPaletteIcon();
if ($teamMember == "true" || $profilSession == "5") {
    $block2->paletteIcon(0, "add", $strings["add"]);
    $block2->paletteIcon(1, "remove", $strings["delete"]);
    $block2->paletteIcon(2, "copy", $strings["copy"]);
    if ($sitePublish == "true") {
        $block2->paletteIcon(4, "add_projectsite", $strings["add_project_site"]);
        $block2->paletteIcon(5, "remove_projectsite", $strings["remove_project_site"]);
    }
}

$block2->paletteIcon(6, "info", $strings["view"]);
if ($teamMember == "true" || $profilSession == "5") {
    $block2->paletteIcon(7, "edit", $strings["edit"]);
}
$block2->closePaletteIcon();

$block2->sorting("tasks", $sortingUser->sor_tasks[0], "tas.name ASC", $sortingFields = [0 => "tas.name", 1 => "tas.priority", 2 => "tas.status", 3 => "tas.completion", 4 => "tas.due_date", 5 => "mem.login", 6 => "tas.published"]);

//$tmpquery = "WHERE tas.project = '$project' AND tas.parent_phase = '$parentPhase' ORDER BY $block2->sortingValue";
//$listTasks = new phpCollab\Request();
//$listTasks->openTasks($tmpquery);
//$comptListTasks = count($listTasks->tas_id);
$listTasks = $tasks->getTasksByProjectIdAndParentPhase($project, $parentPhase, $block2->sortingValue);

if ($listTasks) {
//if ($comptListTasks != "0") {
    $block2->openResults();
    $block2->labels($labels = [0 => $strings["task"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["completion"], 4 => $strings["due_date"], 5 => $strings["assigned_to"], 6 => $strings["published"]], "true");

//    for ($i = 0; $i < $comptListTasks; $i++) {
    foreach ($listTasks as $task) {
        if ($task["tas_due_date"] == "") {
            $task["tas_due_date"] = $strings["none"];
        }
        $idStatus = $task["tas_status"];
        $idPriority = $task["tas_priority"];
        $idPublish = $task["tas_published"];
        $complValue = ($task["tas_completion"] > 0) ? $task["tas_completion"] . "0 %" : $task["tas_completion"] . " %";
        $block2->openRow();
        $block2->checkboxRow($task["tas_id"]);
        $block2->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $task["tas_id"], $task["tas_name"], "in"));
        $block2->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
        $block2->cellRow($status[$idStatus]);
        $block2->cellRow($complValue);
        if ($task["tas_due_date"] <= $date) {
            $block2->cellRow("<b>" . $task["tas_due_date"] . "</b>");
        } else {
            $block2->cellRow($task["tas_due_date"]);
        }
        if ($task["tas_start_date"] != "--" && $task["tas_due_date"] != "--") {
            $gantt = "true";
        }
        if ($task["tas_assigned_to"] == "0") {
            $block2->cellRow($strings["unassigned"]);
        } else {
            $block2->cellRow($blockPage->buildLink($task["tas_mem_email_work"], $task["tas_mem_login"], "mail"));
        }
        echo "</td>";
        if ($sitePublish == "true") {
            $block2->cellRow($statusPublish[$idPublish]);
        }
        $block2->closeRow();
    }
    $block2->closeResults();

    if ($activeJpgraph == "true" && $gantt == "true") {
        echo <<<GANTT
		<div id="ganttChart_taskList" class="ganttChart">
			<img src="graphtasks.php?&project={$projectDetail["pro_id"]}&phase={$phaseDetail["pha_order_num"]}" alt=""><br/>
			<span class="listEvenBold"">{$blockPage->buildLink("http://www.aditus.nu/jpgraph/", "JpGraph", "powered")}</span>	
		</div>
GANTT;
    }
} else {
    $block2->noresults();
}
$block2->closeToggle();
$block2->closeFormResults();

$block2->openPaletteScript();
if ($teamMember == "true" || $profilSession == "5") {
    $block2->paletteScript(0, "add", "../tasks/edittask.php?project=$project&phase=" . $phaseDetail["pha_order_num"] . "", "true,true,true", $strings["add"]);
    $block2->paletteScript(1, "remove", "../tasks/deletetasks.php?project=$project", "false,true,true", $strings["delete"]);
    $block2->paletteScript(2, "copy", "../tasks/edittask.php?project=$project&docopy=true", "false,true,false", $strings["copy"]);
    if ($sitePublish == "true") {
        $block2->paletteScript(4, "add_projectsite", "../phases/viewphase.php?addToSite=true&phase=$phase&action=publish", "false,true,true", $strings["add_project_site"]);
        $block2->paletteScript(5, "remove_projectsite", "../phases/viewphase.php?removeToSite=true&phase=$phase&action=publish", "false,true,true", $strings["remove_project_site"]);
    }
}
$block2->paletteScript(6, "info", "../tasks/viewtask.php?", "false,true,false", $strings["view"]);
if ($teamMember == "true" || $profilSession == "5") {
    $block2->paletteScript(7, "edit", "../tasks/edittask.php?project=$project&phase=" . $phaseDetail["pha_order_num"] . "", "false,true,false", $strings["edit"]);
}
$block2->closePaletteScript(count($comptListTasks), $listTasks["tas_id"]);


if ($fileManagement == "true") {

    $block3 = new phpCollab\Block();
    $block3->form = "tdC";
    $block3->openForm("../phases/viewphase.php?&id=$id#" . $block3->form . "Anchor");
    $block3->headingToggle($strings["linked_content"]);
    $block3->openPaletteIcon();

    if ($teamMember == "true" || $profilSession == "5") {
        $block3->paletteIcon(0, "add", $strings["add"]);
        $block3->paletteIcon(1, "remove", $strings["delete"]);

        if ($sitePublish == "true") {
            $block3->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
            $block3->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
        }
    }

    $block3->paletteIcon(4, "info", $strings["view"]);

    if ($teamMember == "true" || $profilSession == "5") {
        $block3->paletteIcon(5, "edit", $strings["edit"]);
    }

    $block3->closePaletteIcon();
    $block3->sorting("files", $sortingUser->sor_files[0], "fil.name ASC", $sortingFields = [0 => "fil.extension", 1 => "fil.name", 2 => "fil.owner", 3 => "fil.date", 4 => "fil.status", 5 => "fil.published"]);

//    $tmpquery = "WHERE fil.project = {$projectDetail["pro_id"]} AND fil.phase = {$phaseDetail["pha_id"]} AND fil.task = 0 AND fil.vc_parent = 0 ORDER BY {$block3->sortingValue}";
//    $listFiles = new phpCollab\Request();
//    $listFiles->openFiles($tmpquery);
//    $comptListFiles = count($listFiles->fil_id);
    $listFiles = $files->getFilesByProjectAndPhaseWithoutTasksAndParent($projectDetail["pro_id"], $phaseDetail["pha_id"], $block3->sortingValue);

    if ($listFiles) {
//    if ($comptListFiles != "0") {
        $block3->openResults();
        $block3->labels($labels = array(0 => $strings["type"], 1 => $strings["name"], 2 => $strings["owner"], 3 => $strings["date"], 4 => $strings["approval_tracking"], 5 => $strings["published"]), "true");

        foreach ($listFiles as $file) {
//        for ($i = 0; $i < $comptListFiles; $i++) {
            $existFile = "false";
            $idStatus = $file["fil_status"];
            $idPublish = $file["fil_published"];

            $fileHandler = new phpCollab\FileHandler();
            $type = $fileHandler->fileInfoType($file["fil_extension"]);

            if (file_exists("../files/" . $file["fil_project"] . "/" . $file["fil_name"])) {
                $existFile = "true";
            }
            $block3->openRow();
            $block3->checkboxRow($file["fil_id"]);

            if ($existFile == "true") {
                $block3->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $type, "icone"));
            } else {
                $block3->cellRow("&nbsp;");
            }

            if ($existFile == "true") {
                $block3->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $file["fil_name"], "in"));
            } else {
                $block3->cellRow($strings["missing_file"] . " (" . $file["fil_name"] . ")");
            }

            $block3->cellRow($blockPage->buildLink($file["fil_mem_email_work"], $file["fil_mem_login"], "mail"));
            $block3->cellRow($file["fil_date"]);
            $block3->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $statusFile[$idStatus], "in"));

            if ($sitePublish == "true") {
                $block3->cellRow($statusPublish[$idPublish]);
            }
            $block3->closeRow();
        }
        $block3->closeResults();
    } else {
        $block3->noresults();
    }
    $block3->closeToggle();
    $block3->closeFormResults();
    $block3->openPaletteScript();

    if ($teamMember == "true" || $profilSession == "5") {
        $block3->paletteScript(0, "add", "../linkedcontent/addfile.php?project=" . $projectDetail["pro_id"] . "&phase=" . $phaseDetail["pha_id"] . "", "true,true,true", $strings["add"]);
        $block3->paletteScript(1, "remove", "../linkedcontent/deletefiles.php?project=" . $projectDetail["pro_id"] . "&phase=" . $phaseDetail["pha_id"] . "&sendto=phasedetail", "false,true,true", $strings["delete"]);
        if ($sitePublish == "true") {
            $block3->paletteScript(2, "add_projectsite", "../phases/viewphase.php?addToSiteFile=true&phase=" . $phaseDetail["pha_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
            $block3->paletteScript(3, "remove_projectsite", "../phases/viewphase.php?removeToSiteFile=true&phase=" . $phaseDetail["pha_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
        }
    }

    $block3->paletteScript(4, "info", "../linkedcontent/viewfile.php?", "false,true,false", $strings["view"]);
    if ($teamMember == "true" || $profilSession == "5") {
        $block3->paletteScript(5, "edit", "../linkedcontent/viewfile.php?edit=true", "false,true,false", $strings["edit"]);
    }
    $block3->closePaletteScript(count($listFiles), $listFiles["fil_id"]);
}

include APP_ROOT . '/themes/' . THEME . '/footer.php';
