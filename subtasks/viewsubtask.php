<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../tasks/viewsubtask.php
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
** FILE: viewsubtask.php
**
** DESC: Screen: view sub task information
**
** HISTORY:
**	05/11/2004	-	fixed 1059973
**	19/05/2005	-	fixed and &amp; in link
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


use phpCollab\Assignments\Assignments;
use phpCollab\Phases\Phases;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use phpCollab\Updates\Updates;

$checkSession = "true";
include_once '../includes/library.php';

$tasks = new Tasks();
$projects = new Projects();
$teams = new Teams();
$assignments = new Assignments();

$task = $_GET['task'];
$addToSite = $_GET['addToSite'];
$removeToSite = $_GET['removeToSite'];
$addToSiteFile = $_GET['addToSiteFile'];
$removeToSiteFile = $_GET['removeToSiteFile'];
$id = $_GET['id'];
$idSession = $_SESSION['idSession'];

if ($_GET['action'] == "publish") {
    if ($addToSite == "true") {
        $tmpquery1 = "UPDATE {$tableCollab["subtasks"]} SET published=0 WHERE id = :subtask_id";
        $dbParams = ["subtask_id" => $id];
        phpCollab\Util::newConnectSql($tmpquery1, $dbParams);
        unset($dbParams);
        $msg = "addToSite";
    }

    if ($removeToSite == "true") {
        $tmpquery1 = "UPDATE {$tableCollab["subtasks"]} SET published=1 WHERE id = :subtask_id";
        $dbParams = ["subtask_id" => $id];
        phpCollab\Util::newConnectSql($tmpquery1, $dbParams);
        unset($dbParams);
        $msg = "removeToSite";
    }

    if ($addToSiteFile == "true") {
        $id = str_replace("**", ",", $id);

        $tasks->addToSiteFile($id);
        $msg = "addToSite";
        $id = $task;
    }

    if ($removeToSiteFile == "true") {
        $id = str_replace("**", ",", $id);
        $tasks->removeToSiteFile($id);
        $msg = "removeToSite";
        $id = $task;
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$subtaskDetail = $tasks->getSubTaskById($id);

$taskDetail = $tasks->getTaskById($task);

$projectDetail = $projects->getProjectById($taskDetail['tas_project']);

if ($projectDetail['pro_enable_phase'] != "0") {
    $phases = new Phases();
    $tPhase = $taskDetail['tas_parent_phase'];
    if (!$tPhase) {
        $tPhase = '0';
    }
    $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($taskDetail['tas_project'], $tPhase);
}

$teamMember = "false";
$comptMemberTest = count($teams->getTeamByProjectIdAndTeamMember($taskDetail['tas_project'], $idSession));

if ($comptMemberTest == "0") {
    $teamMember = "false";
} else {
    $teamMember = "true";
}

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], 'in'));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail['pro_id'], $projectDetail['pro_name'], 'in'));

if ($projectDetail['pro_phase_set'] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail['pro_id'], $strings["phases"], 'in'));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase['pha_id'], $targetPhase['pha_name'], 'in'));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail['pro_id'], $strings["tasks"], 'in'));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail['tas_id'], $taskDetail['tas_name'], 'in'));
$blockPage->itemBreadcrumbs($subtaskDetail['subtas_name']);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "tdD";
$block1->openForm("../subtasks/viewsubtask.php#" . $block1->form . "Anchor");

$block1->headingToggle($strings["subtask"] . " : " . $subtaskDetail['subtas_name']);


if ($teamMember == "true" || $profilSession == "5") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);
    if ($sitePublish == "true") {
        $block1->paletteIcon(3, "add_projectsite", $strings["add_project_site"]);
        $block1->paletteIcon(4, "remove_projectsite", $strings["remove_project_site"]);
    }
    $block1->paletteIcon(5, "edit", $strings["edit"]);
    $block1->closePaletteIcon();
}

if ($projectDetail['pro_org_id'] == "1") {
    $projectDetail['pro_org_name'] = $strings["none"];
}

$block1->openContent();
$block1->contentTitle($strings["info"]);


$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail['pro_id'], $projectDetail['pro_name'], 'in'));

//Display task's phase
if ($projectDetail['pro_phase_set'] != "0") {
    $block1->contentRow($strings["phase"], $blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase['pha_id'], $targetPhase['pha_name'], 'in'));
}

$block1->contentRow($strings["task"], $blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail['tas_id'], $taskDetail['tas_name'], 'in'));
$block1->contentRow($strings["organization"], $projectDetail['pro_org_name']);
$block1->contentRow($strings["created"], phpCollab\Util::createDate($subtaskDetail['subtas_created'], $timezoneSession));
$block1->contentRow($strings["assigned"], phpCollab\Util::createDate($subtaskDetail['subtas_assigned'], $timezoneSession));
$block1->contentRow($strings["modified"], phpCollab\Util::createDate($subtaskDetail['subtas_modified'], $timezoneSession));

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"], $subtaskDetail['subtas_name']);

$block1->contentRow($strings["description"], nl2br($subtaskDetail['subtas_description']));
if ($subtaskDetail['subtas_assigned_to'] == "0") {
    $block1->contentRow($strings["assigned_to"], $strings["unassigned"]);
} else {
    $block1->contentRow($strings["assigned_to"], $blockPage->buildLink("../users/viewuser.php?id=" . $subtaskDetail['subtas_mem_id'], $subtaskDetail['subtas_mem_name'], 'in') . " (" . $blockPage->buildLink($subtaskDetail['subtas_mem_email_work'], $subtaskDetail['subtas_mem_login'], 'mail') . ")");
}
$idStatus = $subtaskDetail['subtas_status'];
$idPriority = $subtaskDetail['subtas_priority'];
$idPublish = $subtaskDetail['subtas_published'];
$complValue = ($subtaskDetail['subtas_completion'] > 0) ? $subtaskDetail['subtas_completion'] . "0 %" : $subtaskDetail['subtas_completion'] . " %";
$block1->contentRow($strings["status"], $status[$idStatus]);
$block1->contentRow($strings["completion"], $complValue);
$block1->contentRow($strings["priority"], "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
$block1->contentRow($strings["start_date"], $subtaskDetail['subtas_start_date']);
if ($subtaskDetail['subtas_due_date'] <= $date && $subtaskDetail['subtas_completion'] != "10") {
    $block1->contentRow($strings["due_date"], "<b>" . $subtaskDetail['subtas_due_date'] . "</b>");
} else {
    $block1->contentRow($strings["due_date"], $subtaskDetail['subtas_due_date']);
}
if ($subtaskDetail['subtas_complete_date'] != "" && $subtaskDetail['subtas_complete_date'] != "--" && $subtaskDetail['subtas_due_date'] != "--") {
    $diff = phpCollab\Util::diffDate($subtaskDetail['subtas_complete_date'], $subtaskDetail['subtas_due_date']);
    if ($diff > 0) {
        $diff = "<b>+$diff</b>";
    }
    $block1->contentRow($strings["complete_date"], $subtaskDetail['subtas_complete_date']);
    $block1->contentRow($strings["scope_creep"] . $blockPage->printHelp("task_scope_creep"), "$diff " . $strings["days"]);
}
$block1->contentRow($strings["estimated_time"], $subtaskDetail['subtas_estimated_time'] . " " . $strings["hours"]);
$block1->contentRow($strings["actual_time"], $subtaskDetail['subtas_actual_time'] . " " . $strings["hours"]);
if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $statusPublish[$idPublish]);
}

$block1->contentRow($strings["comments"], nl2br($subtaskDetail['subtas_comments']));

$block1->contentTitle($strings["updates_subtask"]);

$updates = new Updates();
$listUpdates = $updates->getUpdates(2, $id);

$comptListUpdates = count($listUpdates);

echo '<tr class="odd"><td valign="top" class="leftvalue">&nbsp;</td><td>';
if ($comptListUpdates != "0") {
    $j = 1;
    foreach ($listUpdates as $update) {
        if (preg_match('|\[status:([0-9])\]|', $update['upd_comments'])) {
            preg_match('|\[status:([0-9])\]|i', $update['upd_comments'], $matches);
            $update['upd_comments'] = preg_replace('|\[status:([0-9])\]|', '', $update['upd_comments'] . '<br/>');
            $update['upd_comments'] .= $strings["status"] . ' ' . $status[$matches[1]];
        }
        if (preg_match('|\[priority:([0-9])\]|', $update['upd_comments'])) {
            preg_match('|\[priority:([0-9])\]|i', $update['upd_comments'], $matches);
            $update['upd_comments'] = preg_replace('|\[priority:([0-9])\]|', '', $update['upd_comments'] . '<br/>');
            $update['upd_comments'] .= $strings["priority"] . ' ' . $priority[$matches[1]];
        }
        if (preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|', $update['upd_comments'])) {
            preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|i', $update['upd_comments'], $matches);
            $update['upd_comments'] = preg_replace('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|', '', $update['upd_comments'] . '<br/>');
            $update['upd_comments'] .= $strings["due_date"] . ' ' . $matches[1];
        }

        $abbrev = stripslashes(substr($update['upd_comments'], 0, 100));
        echo "<b>" . $j . ".</b> <i>" . phpCollab\Util::createDate($update['upd_created'], $timezoneSession) . "</i> $abbrev";
        if (100 < strlen($update['upd_comments'])) {
            echo "...<br/>";
        } else {
            echo "<br/>";
        }
        $j++;
    }
    echo "<br/>" . $blockPage->buildLink("../subtasks/historysubtask.php?type=2&item=$id", $strings["show_details"], "in");
} else {
    echo $strings["no_items"];
}

echo "</td></tr>";

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($teamMember == "true" || $profilSession == "5") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../subtasks/deletesubtasks.php?task=$task&id=" . $subtaskDetail['subtas_id'] . "", "true,true,false", $strings["delete"]);
    if ($sitePublish == "true") {
        $block1->paletteScript(3, "add_projectsite", "../subtasks/viewsubtask.php?addToSite=true&task=$task&id=" . $subtaskDetail['subtas_id'] . "&action=publish", "true,true,true", $strings["add_project_site"]);
        $block1->paletteScript(4, "remove_projectsite", "../subtasks/viewsubtask.php?removeToSite=true&task=$task&id=" . $subtaskDetail['subtas_id'] . "&action=publish", "true,true,true", $strings["remove_project_site"]);
    }
    $block1->paletteScript(5, "edit", "../subtasks/editsubtask.php?task=$task&id=$id&docopy=false", "true,true,false", $strings["edit"]);
    $block1->closePaletteScript(count($listAssign), $listAssign["ass_id"]);
}

$block3 = new phpCollab\Block();

$block3->form = "ahT";
$block3->openForm("../subtasks/viewsubtask.php?&id=$id&task=$task#" . $block3->form . "Anchor");

$block3->headingToggle($strings["assignment_history"]);

$block3->sorting("assignment", $sortingUser["assignment"], "ass.assigned DESC", $sortingFields = array(0 => "ass.comments", 1 => "mem1.login", 2 => "mem2.login", 3 => "ass.assigned"));

$tmpquery = "WHERE ass.subtask = '$id' ORDER BY $block3->sortingValue";
$listAssign = new phpCollab\Request();
$listAssign->openAssignments($tmpquery);

$listAssign = $assignments->getAssignmentsBySubtaskId($id, $block3->sortingValue);

//$comptListAssign = count($listAssign->ass_id);

$block3->openResults($checkbox = "false");

$block3->labels($labels = array(0 => $strings["comment"], 1 => $strings["assigned_by"], 2 => $strings["to"], 3 => $strings["assigned_on"]), "false");

//for ($i = 0; $i < $comptListAssign; $i++) {
foreach ($listAssign as $assignment) {
    $block3->openRow();
    $block3->checkboxRow($assignment["ass_id"], $checkbox = "false");
    if ($assignment["ass_comments"] != "") {
        $block3->cellRow($assignment["ass_comments"]);
    } elseif ($assignment["ass_assigned_to"] == "0") {
        $block3->cellRow($strings["task_unassigned"]);
    } else {
        $block3->cellRow($strings["task_assigned"] . " " . $assignment["ass_mem2_name"] . " (" . $assignment["ass_mem2_login"] . ")");
    }
    $block3->cellRow($blockPage->buildLink($assignment["ass_mem1_email_work"], $assignment["ass_mem1_login"], "mail"));
    if ($assignment["ass_assigned_to"] == "0") {
        $block3->cellRow($strings["unassigned"]);
    } else {
        $block3->cellRow($blockPage->buildLink($assignment["ass_mem2_email_work"], $assignment["ass_mem2_login"], "mail"));
    }
    $block3->cellRow(phpCollab\Util::createDate($assignment["ass_assigned"], $timezoneSession));
    $block3->closeRow();
}
$block3->closeResults();

$block3->closeToggle();
$block3->closeFormResults();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
