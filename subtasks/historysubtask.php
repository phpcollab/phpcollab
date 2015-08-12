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
** DESC: Screen:  view sub task mod history
**
** HISTORY:
**	05/11/2004	-	fixed 1059973 
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

if ($type == "2") {
    $tmpquery = "WHERE subtas.id = '$item'";
    $subtaskDetail = new Request();
    $subtaskDetail->openSubtasks($tmpquery);

    $tmpquery = "WHERE tas.id = '" . $subtaskDetail->subtas_task[0] . "'";
    $taskDetail = new Request();
    $taskDetail->openTasks($tmpquery);

    $tmpquery = "WHERE pro.id = '" . $taskDetail->tas_project[0] . "'";
    $projectDetail = new Request();
    $projectDetail->openProjects($tmpquery);

    if ($projectDetail->pro_enable_phase[0] != "0") {
        $tPhase = $taskDetail->tas_parent_phase[0];
        if (!$tPhase) {
            $tPhase = '0';
        }
        $tmpquery = "WHERE pha.project_id = '" . $taskDetail->tas_project[0] . "' AND pha.order_num = '$tPhase'";
        $targetPhase = new Request();
        $targetPhase->openPhases($tmpquery);
    }
}

if ($type == "1") {
    $tmpquery = "WHERE tas.id = '$item'";
    $taskDetail = new Request();
    $taskDetail->openTasks($tmpquery);

    $tmpquery = "WHERE pro.id = '" . $taskDetail->tas_project[0] . "'";
    $projectDetail = new Request();
    $projectDetail->openProjects($tmpquery);

    if ($projectDetail->pro_enable_phase[0] != "0") {
        $tPhase = $taskDetail->tas_parent_phase[0];
        if (!$tPhase) {
            $tPhase = '0';
        }
        $tmpquery = "WHERE pha.project_id = '" . $taskDetail->tas_project[0] . "' AND pha.order_num = '$tPhase'";
        $targetPhase = new Request();
        $targetPhase->openPhases($tmpquery);
    }
}

include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], in));

if ($projectDetail->pro_phase_set[0] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail->pro_id[0], $strings["phases"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase->pha_id[0], $targetPhase->pha_name[0], in));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail->pro_id[0], $strings["tasks"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail->tas_id[0], $taskDetail->tas_name[0], in));

if ($type == "2") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../subtasks/viewsubtask.php?task=" . $taskDetail->tas_id[0] . "&id=" . $subtaskDetail->subtas_id[0], $subtaskDetail->subtas_name[0], in));
    $blockPage->itemBreadcrumbs($strings["updates_subtask"]);
}

if ($type == "1") {
    $blockPage->itemBreadcrumbs($strings["updates_task"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "tdP";
$block1->openForm("");

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if ($type == "1") {
    $block1->heading($strings["task"] . " : " . $taskDetail->tas_name[0]);
}
if ($type == "2") {
    $block1->heading($strings["subtask"] . " : " . $subtaskDetail->subtas_name[0]);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$tmpquery = "WHERE upd.type='$type' AND upd.item = '$item' ORDER BY upd.created DESC";
$listUpdates = new Request();
$listUpdates->openUpdates($tmpquery);
$comptListUpdates = count($listUpdates->upd_id);

for ($i = 0; $i < $comptListUpdates; $i++) {

    if (preg_match('|\[status:([0-9])\]|', $listUpdates->upd_comments[$i])) {
        preg_match('|\[status:([0-9])\]|i', $listUpdates->upd_comments[$i], $matches);
        $listUpdates->upd_comments[$i] = preg_replace('|\[status:([0-9])\]|', '', $listUpdates->upd_comments[$i] . '<br/>');
        $listUpdates->upd_comments[$i] .= $strings["status"] . ' ' . $status[$matches[1]];
    }
    if (preg_match('|\[priority:([0-9])\]|', $listUpdates->upd_comments[$i])) {
        preg_match('|\[priority:([0-9])\]|i', $listUpdates->upd_comments[$i], $matches);
        $listUpdates->upd_comments[$i] = preg_replace('|\[priority:([0-9])\]|', '', $listUpdates->upd_comments[$i] . '<br/>');
        $listUpdates->upd_comments[$i] .= $strings["priority"] . ' ' . $priority[$matches[1]];
    }
    if (preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|', $listUpdates->upd_comments[$i])) {
        preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|i', $listUpdates->upd_comments[$i], $matches);
        $listUpdates->upd_comments[$i] = preg_replace('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|', '', $listUpdates->upd_comments[$i] . '<br/>');
        $listUpdates->upd_comments[$i] .= $strings["due_date"] . " " . $matches[1];
    }

    $block1->contentRow($strings["posted_by"], $blockPage->buildLink($listUpdates->upd_mem_email_work[$i], $listUpdates->upd_mem_name[$i], mail));
    if ($listUpdates->upd_created[$i] > $lastvisiteSession) {
        $block1->contentRow($strings["when"], "<b>" . Util::createDate($listUpdates->upd_created[$i], $timezoneSession) . "</b>");
    } else {
        $block1->contentRow($strings["when"], Util::createDate($listUpdates->upd_created[$i], $timezoneSession));
    }
    $block1->contentRow("", nl2br($listUpdates->upd_comments[$i]));
    $block1->contentRow("", "", "true");
}

$block1->closeContent();

$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
?>