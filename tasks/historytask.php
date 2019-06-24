<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../topics/viewtopic.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewtopic.php
**
** DESC: Screen:  view task mod history
**
** HISTORY:
**	05/11/2004	-	fixed 1059973
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


use phpCollab\Phases\Phases;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Updates\Updates;

$checkSession = "true";
include_once '../includes/library.php';

$type = isset($_GET["type"]) ? $_GET["type"] : null;
$item = isset($_GET["item"]) ? $_GET["item"] : null;

$tasks = new Tasks();
$projects = new Projects();
$phases = new Phases();
$updates = new Updates();

$strings = $GLOBALS["strings"];

$subtaskDetail = $targetPhase = $projectDetail = null;

if ($type == "2") {
    $subtaskDetail = $tasks->getSubTaskById($item);

    $taskDetail = $tasks->getTasksById($subtaskDetail["subtas_task"]);

    $projectDetail = $projects->getProjectById($taskDetail["tas_project"]);

    if ($projectDetail["pro_enable_phase"] != "0") {
        $tPhase = $taskDetail["tas_parent_phase"];
        $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($taskDetail["tas_project"], $tPhase);
    }
}

if ($type == "1") {
    $taskDetail = $tasks->getTasksById($item);

    $projectDetail = $projects->getProjectById($taskDetail["tas_project"]);

    if ($projectDetail["pro_enable_phase"] != "0") {
        $tPhase = $taskDetail["tas_parent_phase"];
        if (!$tPhase) {
            $tPhase = '0';
        }
        $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($taskDetail["tas_project"], $tPhase);
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));

if ($projectDetail["pro_phase_set"] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail["pro_id"], $strings["phases"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase["pha_id"], $targetPhase["pha_name"], "in"));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail["pro_id"], $strings["tasks"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail["tas_id"], $taskDetail["tas_name"], "in"));

if ($type == "2") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../subtasks/viewsubtask.php?task=" . $taskDetail["tas_id"] . "&id=" . $subtaskDetail["subtas_id"], $subtaskDetail["subtas_name"], "in"));
    $blockPage->itemBreadcrumbs($strings["updates_subtask"]);
}

if ($type == "1") {
    $blockPage->itemBreadcrumbs($strings["updates_task"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "tdP";
$block1->openForm("./historytask.php");

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if ($type == "1") {
    $block1->heading($strings["task"] . " : " . $taskDetail["tas_name"]);
}
if ($type == "2") {
    $block1->heading($strings["subtask"] . " : " . $subtaskDetail["subtas_name"]);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$listUpdates = $updates->getUpdates($type, $item);

//for ($i = 0; $i < $comptListUpdates; $i++) {
foreach ($listUpdates as $update) {
    if (preg_match('/\[status:([0-9])\]/', $update["upd_comments"])) {
        preg_match('|\[status:([0-9])\]|i', $update["upd_comments"], $matches);
        $update["upd_comments"] = preg_replace('/\[status:([0-9])\]/', '', $update["upd_comments"] . '<br/>');
        $update["upd_comments"] .= $strings["status"] . " " . $GLOBALS["status"][$matches[1]];
    }
    if (preg_match('/\[priority:([0-9])\]/', $update["upd_comments"])) {
        preg_match('|\[priority:([0-9])\]|i', $update["upd_comments"], $matches);
        $update["upd_comments"] = preg_replace('/\[priority:([0-9])\]/', '', $update["upd_comments"] . '<br/>');
        $update["upd_comments"] .= $strings["priority"] . " " . $GLOBALS["priority"][$matches[1]];
    }
    if (preg_match('/\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]/', $update["upd_comments"])) {
        preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|i', $update["upd_comments"], $matches);
        $update["upd_comments"] = preg_replace('/\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]/', '', $update["upd_comments"] . '<br/>');
        $update["upd_comments"] .= $strings["due_date"] . " " . $matches[1];
    }

    $block1->contentRow($strings["posted_by"], $blockPage->buildLink($update["upd_mem_email_work"], $update["upd_mem_name"], "mail"));
    if ($update["upd_created"] > $_SESSION["lastvisiteSession"]) {
        $block1->contentRow($strings["when"], "<b>" . phpCollab\Util::createDate($update["upd_created"], $_SESSION["timezoneSession"]) . "</b>");
    } else {
        $block1->contentRow($strings["when"], phpCollab\Util::createDate($update["upd_created"], $_SESSION["timezoneSession"]));
    }
    $block1->contentRow("", nl2br($update["upd_comments"]));
    $block1->contentRow("", "", "true");
}

$block1->closeContent();

$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
