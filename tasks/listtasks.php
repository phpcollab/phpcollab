<?php

$checkSession = "true";
include_once '../includes/library.php';

$id = $request->query->get('id');
$project = $request->query->get('project');
$strings = $GLOBALS["strings"];

$tasks = $container->getTasksLoader();
$projects = $container->getProjectsLoader();
$teams = $container->getTeams();

if ($request->query->get('action') == "publish") {
    if ($request->query->get('addToSite') == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $tasks->publishTasks($id);
        $msg = "addToSite";
        $id = $project;
    }

    if ($request->query->get('removeToSite') == "true") {
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

$teamMember = $teams->isTeamMember($project, $session->get("id"));

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
    $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($strings["tasks"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$blockPage->setLimitsNumber(1);

$tasksBlock = new phpCollab\Block();

$tasksBlock->form = "saT";
$tasksBlock->openForm("../tasks/listtasks.php?&project=$project#" . $tasksBlock->form . "Anchor", null, $csrfHandler);

$tasksBlock->heading($strings["tasks"]);

$tasksBlock->openPaletteIcon();
if ($teamMember == "true") {
    $tasksBlock->paletteIcon(0, "add", $strings["add"]);
    $tasksBlock->paletteIcon(1, "remove", $strings["delete"]);
    $tasksBlock->paletteIcon(2, "copy", $strings["copy"]);

    if ($sitePublish == "true") {
        $tasksBlock->paletteIcon(4, "add_projectsite", $strings["add_project_site"]);
        $tasksBlock->paletteIcon(5, "remove_projectsite", $strings["remove_project_site"]);
    }
}
$tasksBlock->paletteIcon(6, "info", $strings["view"]);
if ($teamMember == "true") {
    $tasksBlock->paletteIcon(7, "edit", $strings["edit"]);
}
$tasksBlock->closePaletteIcon();

$tasksBlock->setLimit($blockPage->returnLimit(1));

$tasksBlock->setRowsLimit(20);

$tasksBlock->sorting("tasks", $sortingUser["tasks"], "tas.name ASC", $sortingFields = [
    0 => "tas.name",
    1 => "tas.priority",
    2 => "tas.status",
    3 => "tas.completion",
    4 => "tas.due_date",
    5 => "mem.login",
    6 => "tas.published"
]);

$taskList = $tasks->getTasksByProjectId($project, $tasksBlock->getLimit(), $tasksBlock->getRowsLimit(),
    $tasksBlock->sortingValue);
$tasksBlock->setRecordsTotal($tasks->getCountAllTasksForProject($project));

if ($taskList) {
    $tasksBlock->openResults();
    $tasksBlock->labels($labels = [
        0 => $strings["task"],
        1 => $strings["priority"],
        2 => $strings["status"],
        3 => $strings["completion"],
        4 => $strings["due_date"],
        5 => $strings["assigned_to"],
        6 => $strings["published"]
    ], "true");

    foreach ($taskList as $task) {

        if ($task["tas_due_date"] == "") {
            $task["tas_due_date"] = $strings["none"];
        }
        $idStatus = $task["tas_status"];
        $idPriority = $task["tas_priority"];

        $idPublish = $task["tas_published"];
        $complValue = ($task["tas_completion"] > 0) ? $task["tas_completion"] . "0 %" : $task["tas_completion"] . " %";
        $tasksBlock->openRow();
        $tasksBlock->checkboxRow($task["tas_id"]);
        $tasksBlock->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $task["tas_id"], $task["tas_name"],
            "in"));
        $tasksBlock->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt='" . $strings["priority"] . ": " . $priority[$idPriority] . "' /> " . $priority[$idPriority]);
        $tasksBlock->cellRow($status[$idStatus]);
        $tasksBlock->cellRow($complValue);
        if ($task["tas_due_date"] <= $date && $task["tas_completion"] != "10") {
            $tasksBlock->cellRow("<b>" . $task["tas_due_date"] . "</b>");
        } else {
            $tasksBlock->cellRow($task["tas_due_date"]);
        }
        if ($task["tas_start_date"] != "--" && $task["tas_due_date"] != "--") {
            $gantt = "true";
        }
        if ($task["tas_assigned_to"] == "0") {
            $tasksBlock->cellRow($strings["unassigned"]);
        } else {
            $tasksBlock->cellRow($blockPage->buildLink($task["tas_mem_email_work"], $task["tas_mem_login"], "mail"));
        }
        if ($sitePublish == "true") {
            $tasksBlock->cellRow($statusPublish[$idPublish]);
        }
        $tasksBlock->closeRow();
    }
    $tasksBlock->closeResults();
    $tasksBlock->limitsFooter("1", $blockPage->getLimitsNumber(), "", "project=$project");

    if ($activeJpgraph == "true" && $gantt == "true") {
        echo <<<GANTT
		<div id="ganttChart_taskList" class="ganttChart">
			<img alt="" src="graphtasks.php?&project={$projectDetail["pro_id"]}"><br/>
			<span class="listEvenBold">{$blockPage->buildLink("http://www.aditus.nu/jpgraph/", "JpGraph", "powered")}</span>	
		</div>
GANTT;
    }
} else {
    $tasksBlock->noresults();
}
$tasksBlock->closeFormResults();

$tasksBlock->openPaletteScript();
if ($teamMember == "true") {
    $tasksBlock->paletteScript(0, "add", "../tasks/edittask.php?project=$project", "true,false,false", $strings["add"]);
    $tasksBlock->paletteScript(1, "remove", "../tasks/deletetasks.php?project=$project", "false,true,true",
        $strings["delete"]);
    $tasksBlock->paletteScript(2, "copy", "../tasks/edittask.php?project=$project&docopy=true", "false,true,false",
        $strings["copy"]);
    if ($sitePublish == "true") {
        $tasksBlock->paletteScript(4, "add_projectsite",
            "../tasks/listtasks.php?addToSite=true&project=" . $projectDetail["pro_id"] . "&action=publish",
            "false,true,true", $strings["add_project_site"]);
        $tasksBlock->paletteScript(5, "remove_projectsite",
            "../tasks/listtasks.php?removeToSite=true&project=" . $projectDetail["pro_id"] . "&action=publish",
            "false,true,true", $strings["remove_project_site"]);
    }
}
$tasksBlock->paletteScript(6, "info", "../tasks/viewtask.php?", "false,true,false", $strings["view"]);
if ($teamMember == "true") {
    $tasksBlock->paletteScript(7, "edit", "../tasks/edittask.php?project=$project", "false,true,true",
        $strings["edit"]);
}
$tasksBlock->closePaletteScript(count($taskList), array_column($taskList, 'tas_id'));

include APP_ROOT . '/views/layout/footer.php';
