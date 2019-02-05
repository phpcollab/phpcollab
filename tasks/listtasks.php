<?php

$checkSession = "true";
include_once '../includes/library.php';

$id = $_GET["id"];
$project = $_GET["project"];
$idSession = $_SESSION["idSession"];
$strings = $GLOBALS["strings"];

$tasks = new \phpCollab\Tasks\Tasks();
$projects = new \phpCollab\Projects\Projects();
$teams = new \phpCollab\Teams\Teams();

if ($_GET["action"] == "publish") {
    if ($_GET["addToSite"] == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $tasks->publishTasks($id);
        $msg = "addToSite";
        $id = $project;
    }

    if ($_GET["removeToSite"] == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $tasks->unPublishTasks($id);
        $msg = "removeToSite";
        $id = $project;
    }
}

$projectDetail = $projects->getProjectById($project);

$teamMember = "false";

$teamMember = $teams->isTeamMember($project, $idSession);

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($strings["tasks"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$blockPage->setLimitsNumber(1);

$block1 = new phpCollab\Block();

$block1->form = "saT";
$block1->openForm("../tasks/listtasks.php?&project=$project#" . $block1->form . "Anchor");

$block1->heading($strings["tasks"]);

$block1->openPaletteIcon();
if ($teamMember == "true") {
    $block1->paletteIcon(0, "add", $strings["add"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);
    $block1->paletteIcon(2, "copy", $strings["copy"]);

    if ($sitePublish == "true") {
        $block1->paletteIcon(4, "add_projectsite", $strings["add_project_site"]);
        $block1->paletteIcon(5, "remove_projectsite", $strings["remove_project_site"]);
    }
}
$block1->paletteIcon(6, "info", $strings["view"]);
if ($teamMember == "true") {
    $block1->paletteIcon(7, "edit", $strings["edit"]);
}
$block1->closePaletteIcon();

$block1->setLimit($blockPage->returnLimit(1));
$block1->setRowsLimit(20);

$block1->sorting("tasks", $sortingUser["tasks"], "tas.name ASC", $sortingFields = [0 => "tas.name", 1 => "tas.priority", 2 => "tas.status", 3 => "tas.completion", 4 => "tas.due_date", 5 => "mem.login", 6 => "tas.published"]);

$taskList = $tasks->getTasksByProjectId($project, $block1->getLimit(), $block1->getRowsLimit(), $block1->sortingValue);
$block1->setRecordsTotal(count($taskList));

if ($taskList) {
    $block1->openResults();

    $block1->labels($labels = array(0 => $strings["task"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["completion"], 4 => $strings["due_date"], 5 => $strings["assigned_to"], 6 => $strings["published"]), "true");

    foreach ($taskList as $task) {

        if ($task["tas_due_date"] == "") {
            $task["tas_due_date"] = $strings["none"];
        }
        $idStatus = $task["tas_status"];
        $idPriority = $task["tas_priority"];

        $idPublish = $task["tas_published"];
        $complValue = ($task["tas_completion"] > 0) ? $task["tas_completion"] . "0 %" : $task["tas_completion"] . " %";
        $block1->openRow();
        $block1->checkboxRow($task["tas_id"]);
        $block1->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $task["tas_id"], $task["tas_name"], "in"));
        $block1->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> " . $priority[$idPriority]);
        $block1->cellRow($status[$idStatus]);
        $block1->cellRow($complValue);
        if ($task["tas_due_date"] <= $date && $task["tas_completion"] != "10") {
            $block1->cellRow("<b>" . $task["tas_due_date"] . "</b>");
        } else {
            $block1->cellRow($task["tas_due_date"]);
        }
        if ($task["tas_start_date"] != "--" && $task["tas_due_date"] != "--") {
            $gantt = "true";
        }
        if ($task["tas_assigned_to"] == "0") {
            $block1->cellRow($strings["unassigned"]);
        } else {
            $block1->cellRow($blockPage->buildLink($task["tas_mem_email_work"], $task["tas_mem_login"], "mail"));
        }
        if ($sitePublish == "true") {
            $block1->cellRow($statusPublish[$idPublish]);
        }
        $block1->closeRow();
    }
    $block1->closeResults();

    $block1->limitsFooter("1", $blockPage->getLimitsNumber(), "", "project=$project");

    if ($activeJpgraph == "true" && $gantt == "true") {
        echo <<<GANTT
		<div id="ganttChart_taskList" class="ganttChart">
			<img alt="" src="graphtasks.php?&project={$projectDetail["pro_id"]}"><br/>
			<span class="listEvenBold">{$blockPage->buildLink("http://www.aditus.nu/jpgraph/", "JpGraph", "powered")}</span>	
		</div>
GANTT;
    }
} else {
    $block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
if ($teamMember == "true") {
    $block1->paletteScript(0, "add", "../tasks/edittask.php?project=$project", "true,false,false", $strings["add"]);
    $block1->paletteScript(1, "remove", "../tasks/deletetasks.php?project=$project", "false,true,true", $strings["delete"]);
    $block1->paletteScript(2, "copy", "../tasks/edittask.php?project=$project&docopy=true", "false,true,false", $strings["copy"]);
    if ($sitePublish == "true") {
        $block1->paletteScript(4, "add_projectsite", "../tasks/listtasks.php?addToSite=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
        $block1->paletteScript(5, "remove_projectsite", "../tasks/listtasks.php?removeToSite=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
    }
}
$block1->paletteScript(6, "info", "../tasks/viewtask.php?", "false,true,false", $strings["view"]);
if ($teamMember == "true") {
    $block1->paletteScript(7, "edit", "../tasks/edittask.php?project=$project", "false,true,true", $strings["edit"]);
}
$block1->closePaletteScript($comptListTasks, $listTasks->tas_id);

include APP_ROOT . '/themes/' . THEME . '/footer.php';
