<?php
/*
** Application name: phpCollab
** Path by root:  ../tasks/viewsubtask.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewsubtask.php
**
** DESC: Screen: view sub task information
**
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';

$tasks = $container->getTasksLoader();
$subtasks = $container->getSubtasksLoader();
$projects = $container->getProjectsLoader();
$teams = $container->getTeams();
$assignments = $container->getAssignmentsManager();

$id = $request->query->get("id");
$task = $request->query->get("task");
$addToSite = $request->query->get("addToSite");
$msg = null;

if ($request->query->get("action") == "publish") {
    if ($addToSite == "true") {
        $subtasks->publish($id);
        $msg = "addToSite";
    }

    if ($request->query->get("removeToSite") == "true") {
        $subtasks->unpublish($id);
        $msg = "removeToSite";
    }
}

include APP_ROOT . '/views/layout/header.php';

$subtaskDetail = $subtasks->getById($id);

$taskDetail = $tasks->getTaskById($task);

$projectDetail = $projects->getProjectById($taskDetail['tas_project']);

if ($projectDetail['pro_enable_phase'] != "0") {
    $phases = $container->getPhasesLoader();
    $tPhase = $taskDetail['tas_parent_phase'];
    if (!$tPhase) {
        $tPhase = '0';
    }
    $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($taskDetail['tas_project'], $tPhase);
}

$teamMember = "false";
$teamMember = $teams->isTeamMember($taskDetail["tas_project"], $session->get("id"));

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], 'in'));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail['pro_id'],
    $projectDetail['pro_name'], 'in'));

if ($projectDetail['pro_phase_set'] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail['pro_id'],
        $strings["phases"], 'in'));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase['pha_id'],
        $targetPhase['pha_name'], 'in'));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail['pro_id'],
    $strings["tasks"], 'in'));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail['tas_id'],
    $taskDetail['tas_name'], 'in'));
$blockPage->itemBreadcrumbs($subtaskDetail['subtas_name']);
$blockPage->closeBreadcrumbs();

if (!empty($msg)) {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "tdD";
$block1->openForm("../subtasks/viewsubtask.php#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->headingToggle($strings["subtask"] . " : " . $subtaskDetail['subtas_name']);


if ($teamMember == "true" || $session->get("profile") == "5") {
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


$block1->contentRow($strings["project"],
    $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail['pro_id'], $projectDetail['pro_name'],
        'in'));

//Display task's phase
if ($projectDetail['pro_phase_set'] != "0") {
    $block1->contentRow($strings["phase"],
        $blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase['pha_id'], $targetPhase['pha_name'], 'in'));
}

$block1->contentRow($strings["task"],
    $blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail['tas_id'], $taskDetail['tas_name'], 'in'));
$block1->contentRow($strings["organization"], $projectDetail['pro_org_name']);
$block1->contentRow($strings["created"],
    phpCollab\Util::createDate($subtaskDetail['subtas_created'], $session->get("timezone")));
$block1->contentRow($strings["assigned"],
    phpCollab\Util::createDate($subtaskDetail['subtas_assigned'], $session->get("timezone")));
$block1->contentRow($strings["modified"],
    phpCollab\Util::createDate($subtaskDetail['subtas_modified'], $session->get("timezone")));

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"], $subtaskDetail['subtas_name']);

$block1->contentRow($strings["description"], nl2br($subtaskDetail['subtas_description']));
if ($subtaskDetail['subtas_assigned_to'] == "0") {
    $block1->contentRow($strings["assigned_to"], $strings["unassigned"]);
} else {
    $block1->contentRow($strings["assigned_to"],
        $blockPage->buildLink("../users/viewuser.php?id=" . $subtaskDetail['subtas_mem_id'],
            $subtaskDetail['subtas_mem_name'],
            'in') . " (" . $blockPage->buildLink($subtaskDetail['subtas_mem_email_work'],
            $subtaskDetail['subtas_mem_login'], 'mail') . ")");
}
$idStatus = $subtaskDetail['subtas_status'];
$idPriority = $subtaskDetail['subtas_priority'];
$idPublish = $subtaskDetail['subtas_published'];
$complValue = ($subtaskDetail['subtas_completion'] > 0) ? $subtaskDetail['subtas_completion'] . "0 %" : $subtaskDetail['subtas_completion'] . " %";
$block1->contentRow($strings["status"], $status[$idStatus]);
$block1->contentRow($strings["completion"], $complValue);
$block1->contentRow($strings["priority"],
    "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
$block1->contentRow($strings["start_date"], $subtaskDetail['subtas_start_date']);
if ($subtaskDetail['subtas_due_date'] <= $date && $subtaskDetail['subtas_completion'] != "10") {
    $block1->contentRow($strings["due_date"], "<strong>" . $subtaskDetail['subtas_due_date'] . "</strong>");
} else {
    $block1->contentRow($strings["due_date"], $subtaskDetail['subtas_due_date']);
}
if ($subtaskDetail['subtas_complete_date'] != "" && $subtaskDetail['subtas_complete_date'] != "--" && $subtaskDetail['subtas_due_date'] != "--") {
    $diff = phpCollab\Util::diffDate($subtaskDetail['subtas_complete_date'], $subtaskDetail['subtas_due_date']);
    if ($diff > 0) {
        $diff = "<strong>+$diff</strong>";
    }
    $block1->contentRow($strings["complete_date"], $subtaskDetail['subtas_complete_date']);
    $block1->contentRow($strings["scope_creep"] . $blockPage->printHelp("task_scope_creep"),
        "$diff " . $strings["days"]);
}
$block1->contentRow($strings["estimated_time"], $subtaskDetail['subtas_estimated_time'] . " " . $strings["hours"]);
$block1->contentRow($strings["actual_time"], $subtaskDetail['subtas_actual_time'] . " " . $strings["hours"]);
if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $statusPublish[$idPublish]);
}

$block1->contentRow($strings["comments"], nl2br($subtaskDetail['subtas_comments']));

$block1->contentTitle($strings["updates_subtask"]);

$updates = $container->getTaskUpdateService();
$listUpdates = $updates->getUpdates(2, $id);

$comptListUpdates = count($listUpdates);

echo <<< HTML
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td>
HTML;


if ($comptListUpdates != "0") {
    $j = 1;
    foreach ($listUpdates as $update) {
        if (preg_match('|\[status:([0-9])]|', $update['upd_comments'])) {
            preg_match('|\[status:([0-9])]|i', $update['upd_comments'], $matches);
            $update['upd_comments'] = preg_replace('|\[status:([0-9])]|', '', $update['upd_comments'] . '<br/>');
            $update['upd_comments'] .= $strings["status"] . ' ' . $status[$matches[1]];
        }
        if (preg_match('|\[priority:([0-9])]|', $update['upd_comments'])) {
            preg_match('|\[priority:([0-9])]|i', $update['upd_comments'], $matches);
            $update['upd_comments'] = preg_replace('|\[priority:([0-9])]|', '', $update['upd_comments'] . '<br/>');
            $update['upd_comments'] .= $strings["priority"] . ' ' . $priority[$matches[1]];
        }
        if (preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})]|', $update['upd_comments'])) {
            preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})]|i', $update['upd_comments'], $matches);
            $update['upd_comments'] = preg_replace('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})]|', '',
                $update['upd_comments'] . '<br/>');
            $update['upd_comments'] .= $strings["due_date"] . ' ' . $matches[1];
        }

        $abbrev = stripslashes(substr($update['upd_comments'], 0, 100));
        echo "<strong>" . $j . ".</strong> <em>" . phpCollab\Util::createDate($update['upd_created'],
                $session->get("timezone")) . "</em> $abbrev";
        if (100 < strlen($update['upd_comments'])) {
            echo "...<br/>";
        } else {
            echo "<br/>";
        }
        $j++;
    }
    echo "<br/>" . $blockPage->buildLink("../subtasks/historysubtask.php?type=2&item=$id", $strings["show_details"],
            "in");
} else {
    echo $strings["no_items"];
}

echo <<< HTML
    </td>
</tr>
HTML;

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($teamMember == "true" || $session->get("profile") == "5") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove",
        "../subtasks/deletesubtasks.php?task=$task&id=" . $subtaskDetail['subtas_id'] . "", "true,true,false",
        $strings["delete"]);
    if ($sitePublish == "true") {
        $block1->paletteScript(3, "add_projectsite",
            "../subtasks/viewsubtask.php?addToSite=true&task=$task&id=" . $subtaskDetail['subtas_id'] . "&action=publish",
            "true,true,true", $strings["add_project_site"]);
        $block1->paletteScript(4, "remove_projectsite",
            "../subtasks/viewsubtask.php?removeToSite=true&task=$task&id=" . $subtaskDetail['subtas_id'] . "&action=publish",
            "true,true,true", $strings["remove_project_site"]);
    }
    $block1->paletteScript(5, "edit", "../subtasks/editsubtask.php?task=$task&id=$id&docopy=false", "true,true,false",
        $strings["edit"]);
    $block1->closePaletteScript(0, []);
}

$block3 = new phpCollab\Block();

$block3->form = "ahT";
$block3->openForm("../subtasks/viewsubtask.php?&id=$id&task=$task#" . $block3->form . "Anchor", null, $csrfHandler);

$block3->headingToggle($strings["assignment_history"]);

$block3->sorting("assignment", $sortingUser["assignment"], "ass.assigned DESC",
    $sortingFields = array(0 => "ass.comments", 1 => "mem1.login", 2 => "mem2.login", 3 => "ass.assigned"));

$listAssign = $assignments->getAssignmentsBySubtaskId($id, $block3->sortingValue);

$block3->openResults($checkbox = "false");

$block3->labels($labels = array(
    0 => $strings["comment"],
    1 => $strings["assigned_by"],
    2 => $strings["to"],
    3 => $strings["assigned_on"]
), "false");

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
        $block3->cellRow($blockPage->buildLink($assignment["ass_mem2_email_work"], $assignment["ass_mem2_login"],
            "mail"));
    }
    $block3->cellRow(phpCollab\Util::createDate($assignment["ass_assigned"], $session->get("timezone")));
    $block3->closeRow();
}
$block3->closeResults();

$block3->closeToggle();
$block3->closeFormResults();

include APP_ROOT . '/views/layout/footer.php';
