<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../tasks/viewtask.php
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
** FILE: viewtask.php
**
** DESC: Screen:  view  task information
**
** HISTORY:
**	05/11/2004	-	fixed 1059973
**	19/05/2005	-	fixed and &amp; in link
**  22/05/2005	-	added [MOD] file owner label in linked content list
**  10/02/2007  -   Changed JPGraph implementation
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

$tasks = new \phpCollab\Tasks\Tasks();
$projects = new \phpCollab\Projects\Projects();
$phases = new \phpCollab\Phases\Phases();
$teams = new \phpCollab\Teams\Teams();
$updates = new \phpCollab\Updates\Updates();
$files = new \phpCollab\Files\Files();
$assignments = new \phpCollab\Assignments\Assignments();

$id = $_GET["id"];
$task = $_GET["task"];
$action = $_GET["action"];

// Global variables
$tableCollab = $GLOBALS["tableCollab"];
$status = $GLOBALS["status"];
$strings = $GLOBALS["strings"];
$date = $GLOBALS["date"];
$statusPublish = $GLOBALS["statusPublish"];
$priority = $GLOBALS["priority"];

$timezoneSession = $_SESSION["timezoneSession"];

$cheatCode = false;
if ($task != "") {
    $cheatCode = "true";
}

if (isset($action) && $action == "publish") {
    $addToSite = $_GET["addToSite"];
    $removeToSite = $_GET["removeToSite"];
    $addToSiteFile = $_GET["addToSiteFile"];
    $removeToSiteFile = $_GET["removeToSiteFile"];

    if (isset($addToSite) && $addToSite == "true") {
        $tasks->publishTasks($id);
        $msg = "addToSite";
    }

    if (isset($removeToSite) && $removeToSite == "true") {
        $tasks->unPublishTasks($id);
        $msg = "removeToSite";
    }

    if (isset($addToSiteFile) && $addToSiteFile == "true") {
        $id = str_replace("**", ",", $id);
        $tasks->addToSiteFile($id);
        $msg = "addToSite";
        $id = $task;
    }

    if (isset($removeToSiteFile) && $removeToSiteFile == "true") {
        $id = str_replace("**", ",", $id);
        $tasks->removeToSiteFile($id);
        $msg = "removeToSite";
        $id = $task;
    }
}

if ($task != "" && $cheatCode == "true") {
    $id = $task;
}

$taskDetail = $tasks->getTaskById($id);

$projectDetail = $projects->getProjectById($taskDetail["tas_project"]);

$targetPhase = null;
if ($projectDetail["pro_enable_phase"] != "0") {
    $tPhase = $taskDetail["tas_parent_phase"];
    if (!$tPhase) {
        $tPhase = '0';
    }
    $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($taskDetail["tas_project"], $tPhase);
}

$teamMember = false;
$teamMember = ($_SESSION["idSession"] == $taskDetail["tas_owner"] || $teams->isTeamMember($taskDetail["tas_project"], $_SESSION["idSession"]));

if ($teamMember === false && $projectsFilter === "true") {
    header("Location:../general/permissiondenied.php");
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
$blockPage->itemBreadcrumbs($taskDetail["tas_name"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "tdD";
$block1->openForm("../tasks/viewtask.php#" . $block1->form . "Anchor");

$block1->headingToggle($strings["task"] . " : " . $taskDetail["tas_name"]);

if ($teamMember === true || $profilSession == "5") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);
    $block1->paletteIcon(1, "copy", $strings["copy"]);

    if ($sitePublish == "true") {
        $block1->paletteIcon(3, "add_projectsite", $strings["add_project_site"]);
        $block1->paletteIcon(4, "remove_projectsite", $strings["remove_project_site"]);
    }

    $block1->paletteIcon(5, "edit", $strings["edit"]);
    $block1->closePaletteIcon();
}

if ($projectDetail["pro_org_id"] == "1") {
    $projectDetail["pro_org_name"] = $strings["none"];
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));

if ($projectDetail["pro_phase_set"] != "0") {
    $block1->contentRow($strings["phase"], $blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase["pha_id"], $targetPhase["pha_name"], "in"));
}

$block1->contentRow($strings["organization"], $projectDetail["pro_org_name"]);

$block1->contentRow($strings["created"], phpCollab\Util::createDate($taskDetail["tas_created"], $timezoneSession));
$block1->contentRow($strings["assigned"], phpCollab\Util::createDate($taskDetail["tas_assigned"], $timezoneSession));
$block1->contentRow($strings["modified"], phpCollab\Util::createDate($taskDetail["tas_modified"], $timezoneSession));

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"], $taskDetail["tas_name"]);

$block1->contentRow($strings["description"], nl2br($taskDetail["tas_description"]));
if ($taskDetail["tas_assigned_to"] == "0") {
    $block1->contentRow($strings["assigned_to"], $strings["unassigned"]);
} else {
    $block1->contentRow($strings["assigned_to"], $blockPage->buildLink("../users/viewuser.php?id=" . $taskDetail["tas_mem_id"], $taskDetail["tas_mem_name"], "in") . " (" . $blockPage->buildLink($taskDetail["tas_mem_email_work"], $taskDetail["tas_mem_login"], "mail") . ")");
}

$idStatus = $taskDetail["tas_status"];
$idPriority = $taskDetail["tas_priority"];
$idPublish = $taskDetail["tas_published"];
$complValue = ($taskDetail["tas_completion"] > 0) ? $taskDetail["tas_completion"] . "0 %" : $taskDetail["tas_completion"] . " %";
$block1->contentRow($strings["status"], $status[$idStatus]);
$block1->contentRow($strings["completion"], $complValue);
$block1->contentRow($strings["priority"], "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
$block1->contentRow($strings["start_date"], $taskDetail["tas_start_date"]);

if ($taskDetail["tas_due_date"] <= $date && $taskDetail["tas_completion"] != "10") {
    $block1->contentRow($strings["due_date"], "<b>" . $taskDetail["tas_due_date"] . "</b>");
} else {
    $block1->contentRow($strings["due_date"], $taskDetail["tas_due_date"]);
}

if ($taskDetail["tas_complete_date"] != "" && $taskDetail["tas_complete_date"] != "--" && $taskDetail["tas_due_date"] != "--") {
    $diff = phpCollab\Util::diffDate($taskDetail["tas_complete_date"], $taskDetail["tas_due_date"]);

    if ($diff > 0) {
        $diff = "<b>+$diff</b>";
    }

    $block1->contentRow($strings["complete_date"], $taskDetail["tas_complete_date"]);
    $block1->contentRow($strings["scope_creep"] . $blockPage->printHelp("task_scope_creep"), "$diff " . $strings["days"]);
}

$block1->contentRow($strings["estimated_time"], $taskDetail["tas_estimated_time"] . " " . $strings["hours"]);
$block1->contentRow($strings["actual_time"], $taskDetail["tas_actual_time"] . " " . $strings["hours"]);

if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $statusPublish[$idPublish]);
}

if ($enableInvoicing == "true") {
    if ($taskDetail["tas_invoicing"] == "1") {
        $block1->contentRow($strings["invoicing"], $strings["true"]);
    } else {
        $block1->contentRow($strings["invoicing"], $strings["false"]);
    }
}

$block1->contentRow($strings["worked_hours"], $taskDetail["tas_worked_hours"]);
$block1->contentRow($strings["comments"], nl2br($taskDetail["tas_comments"]));

$block1->contentTitle($strings["updates_task"]);

$listUpdates = $updates->getUpdates(1, $id);

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>";

if ($listUpdates) {
    $j = 1;

    foreach ($listUpdates as $update) {
        if (preg_match('/\[status:([0-9])\]/', $update["upd_comments"])) {
            preg_match('|\[status:([0-9])\]|i', $update["upd_comments"], $matches);
            $update["upd_comments"] = preg_replace('/\[status:([0-9])\]/', '', $update["upd_comments"] . '<br/>');
            $update["upd_comments"] .= $strings["status"] . " " . $status[$matches[1]];
        }
        if (preg_match('/\[priority:([0-9])\]/', $update["upd_comments"])) {
            preg_match('|\[priority:([0-9])\]|i', $update["upd_comments"], $matches);
            $update["upd_comments"] = preg_replace('/\[priority:([0-9])\]/', '', $update["upd_comments"] . '<br/>');
            $update["upd_comments"] .= $strings["priority"] . " " . $priority[$matches[1]];
        }
        if (preg_match('/\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]/', $update["upd_comments"])) {
            preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|i', $update["upd_comments"], $matches);
            $update["upd_comments"] = preg_replace('/\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]/', '', $update["upd_comments"] . '<br/>');
            $update["upd_comments"] .= $strings["due_date"] . ' ' . $matches[1];
        }

        $abbrev = stripslashes(substr($update["upd_comments"], 0, 100));
        echo "<b>" . $j . ".</b> <i>" . phpCollab\Util::createDate($update["upd_created"], $timezoneSession) . "</i> $abbrev";

        if (100 < strlen($update["upd_comments"])) {
            echo "...<br/>";
        } else {
            echo "<br/>";
        }

        $j++;
    }

    echo "<br/>" . $blockPage->buildLink("../tasks/historytask.php?type=1&item=$id", $strings["show_details"], "in");
} else {
    echo $strings["no_items"];
}

echo "</td></tr>";

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($teamMember === true || $profilSession == "5") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../tasks/deletetasks.php?project=" . $taskDetail["tas_project"] . "&id=" . $taskDetail["tas_id"] . "", "true,true,false", $strings["delete"]);
    $block1->paletteScript(1, "copy", "../tasks/edittask.php?project=" . $taskDetail["tas_project"] . "&task=" . $taskDetail["tas_id"] . "&docopy=true", "true,true,false", $strings["copy"]);

    if ($sitePublish == "true") {
        $block1->paletteScript(3, "add_projectsite", "../tasks/viewtask.php?addToSite=true&id=" . $taskDetail["tas_id"] . "&action=publish", "true,true,true", $strings["add_project_site"]);
        $block1->paletteScript(4, "remove_projectsite", "../tasks/viewtask.php?removeToSite=true&id=" . $taskDetail["tas_id"] . "&action=publish", "true,true,true", $strings["remove_project_site"]);
    }
    $block1->paletteScript(5, "edit", "../tasks/edittask.php?project=" . $taskDetail["tas_project"] . "&task=" . $taskDetail["tas_id"] . "&docopy=false", "true,true,false", $strings["edit"]);
    $block1->closePaletteScript("", []);
}

if ($fileManagement == "true") {
    $block2 = new phpCollab\Block();
    $block2->form = "tdC";
    $block2->openForm("../tasks/viewtask.php?id=$id#" . $block2->form . "Anchor");
    $block2->headingToggle($strings["linked_content"]);
    $block2->openPaletteIcon();

    if ($teamMember === true || $profilSession == "5") {
        $block2->paletteIcon(0, "add", $strings["add"]);
        $block2->paletteIcon(1, "remove", $strings["delete"]);

        if ($sitePublish == "true") {
            $block2->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
            $block2->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
        }
    }

    $block2->paletteIcon(4, "info", $strings["view"]);

    if ($teamMember === true || $profilSession == "5") {
        $block2->paletteIcon(5, "edit", $strings["edit"]);
    }

    $block2->closePaletteIcon();
    $block2->sorting("files", $sortingUser["files"], "fil.name ASC", $sortingFields = [0 => "fil.extension", 1 => "fil.name", 2 => "fil.owner", 3 => "fil.date", 4 => "fil.status", 5 => "fil.published"]);

    $listFiles = $files->getFilesByTaskIdAndVCParentEqualsZero($id, $block2->sortingValue);

    if ($listFiles) {
        $block2->openResults();
        $block2->labels($labels = [0 => $strings["type"], 1 => $strings["name"], 2 => $strings["owner"], 3 => $strings["date"], 4 => $strings["approval_tracking"], 5 => $strings["published"]], "true");

        foreach ($listFiles as $file) {
            $existFile = "false";
            $idStatus = $file["fil_status"];
            $idPublish = $file["fil_published"];

            $fileHandler = new phpCollab\FileHandler();
            $type = $fileHandler->fileInfoType($file["fil_extension"]);

            if (file_exists("../files/" . $file["fil_project"] . "/" . $file["fil_task"] . "/" . $file["fil_name"])) {
                $existFile = "true";
            }

            $block2->openRow();
            $block2->checkboxRow($file["fil_id"]);

            if ($existFile == "true") {
                $block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $type, "icone"));
            } else {
                $block2->cellRow("&nbsp;");
            }

            if ($existFile == "true") {
                $block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $file["fil_name"], "in"));
            } else {
                $block2->cellRow($strings["missing_file"] . " (" . $file["fil_name"] . ")");
            }

            $block2->cellRow($blockPage->buildLink($file["fil_mem_email_work"], $file["fil_mem_login"], "mail"));
            $block2->cellRow($file["fil_date"]);
            $block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $GLOBALS["statusFile"][$idStatus], "in"));

            if ($sitePublish == "true") {
                $block2->cellRow($statusPublish[$idPublish]);
            }
            $block2->closeRow();
        }
        $block2->closeResults();
    } else {
        $block2->noresults();
    }

    $block2->closeToggle();
    $block2->closeFormResults();
    $block2->openPaletteScript();

    if ($teamMember === true || $profilSession == "5") {
        $block2->paletteScript(0, "add", "../linkedcontent/addfile.php?project=" . $taskDetail["tas_project"] . "&task=$id", "true,true,true", $strings["add"]);
        $block2->paletteScript(1, "remove", "../linkedcontent/deletefiles.php?project=" . $projectDetail["pro_id"] . "&task=" . $taskDetail["tas_id"] . "", "false,true,true", $strings["delete"]);

        if ($sitePublish == "true") {
            $block2->paletteScript(2, "add_projectsite", "../tasks/viewtask.php?addToSiteFile=true&task=" . $taskDetail["tas_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
            $block2->paletteScript(3, "remove_projectsite", "../tasks/viewtask.php?removeToSiteFile=true&task=" . $taskDetail["tas_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
        }
    }

    $block2->paletteScript(4, "info", "../linkedcontent/viewfile.php?", "false,true,false", $strings["view"]);

    if ($teamMember === true || $profilSession == "5") {
        $block2->paletteScript(5, "edit", "../linkedcontent/viewfile.php?edit=true", "false,true,false", $strings["edit"]);
    }
    $block2->closePaletteScript(count($listFiles), array_column($listFiles, 'fil_id'));
}

// Assignment History block
$block3 = new phpCollab\Block();

$block3->form = "ahT";
$block3->openForm("../tasks/viewtask.php?id=$id#" . $block3->form . "Anchor");
$block3->headingToggle($strings["assignment_history"]);
$block3->sorting("assignment", $sortingUser["assignment"], "ass.assigned DESC", $sortingFields = [0 => "ass.comments", 1 => "mem1.login", 2 => "mem2.login", 3 => "ass.assigned"]);

$listAssign = $assignments->getAssignmentsByTaskId($id, $block3->sortingValue);

$block3->openResults($checkbox = "false");
$block3->labels($labels = [0 => $strings["comment"], 1 => $strings["assigned_by"], 2 => $strings["to"], 3 => $strings["assigned_on"]], "false");

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

// Subtasks block
$block4 = new phpCollab\Block();
$block4->form = "subT";
$block4->openForm("../tasks/viewtask.php?task=$id#" . $block4->form . "Anchor");
$block4->headingToggle($strings["subtasks"]);
$block4->openPaletteIcon();

if ($teamMember === true || $profilSession == "5") {
    $block4->paletteIcon(0, "add", $strings["add"]);
    $block4->paletteIcon(1, "remove", $strings["delete"]);
}
$block4->paletteIcon(6, "info", $strings["view"]);

if ($teamMember === true || $profilSession == "5") {
    $block4->paletteIcon(7, "edit", $strings["edit"]);
}

$block4->closePaletteIcon();
$block4->sorting("subtasks", $sortingUser["subtasks"], "subtas.name ASC", $sortingFields = [0 => "subtas.name", 1 => "subtas.priority", 2 => "subtas.status", 3 => "subtas.completion", 4 => "subtas.due_date", 5 => "mem.login", 6 => "subtas.published"]);

$listSubtasks = $tasks->getSubtasksByParentTaskId($id, $block4->sortingValue);

if ($listSubtasks) {
    $block4->openResults();
    $block4->labels($labels = [0 => $strings["subtask"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["completion"], 4 => $strings["due_date"], 5 => $strings["assigned_to"], 6 => $strings["published"]], "true");

    foreach ($listSubtasks as $subtask) {
        if ($subtask["subtas_due_date"] == "") {
            $subtask["subtas_due_date"] = $strings["none"];
        }

        $idStatus = $subtask["subtas_status"];
        $idPriority = $subtask["subtas_priority"];
        $idPublish = $subtask["subtas_published"];
        $complValue = ($subtask["subtas_completion"] > 0) ? $subtask["subtas_completion"] . "0 %" : $subtask["subtas_completion"] . " %";

        $block4->openRow();
        $block4->checkboxRow($subtask["subtas_id"]);
        $block4->cellRow($blockPage->buildLink("../subtasks/viewsubtask.php?id=" . $subtask["subtas_id"] . "&task=$id", $subtask["subtas_name"], "in"));
        $block4->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
        $block4->cellRow($status[$idStatus]);
        $block4->cellRow($complValue);

        if ($subtask["subtas_due_date"] <= $date && $subtask["subtas_completion"] != "10") {
            $block4->cellRow("<b>" . $subtask["subtas_due_date"] . "</b>");
        } else {
            $block4->cellRow($subtask["subtas_due_date"]);
        }

        if ($subtask["subtas_start_date"] != "--" && $subtask["subtas_due_date"] != "--") {
            $gantt = true;
        }

        if ($subtask["subtas_assigned_to"] == "0") {
            $block4->cellRow($strings["unassigned"]);
        } else {
            $block4->cellRow($blockPage->buildLink($subtask["subtas_mem_email_work"], $subtask["subtas_mem_login"], "mail"));
        }

        if ($sitePublish == "true") {
            $block4->cellRow($statusPublish[$idPublish]);
        }
        $block4->closeRow();
    }
    $block4->closeResults();

    if ($activeJpgraph === "true" && (isset($gantt) && $gantt === true)) {
        echo "
			<div id='ganttChart_taskList' class='ganttChart'>
				<img src='../subtasks/graphsubtasks.php?task=" . $id . "' alt=''><br/>
				<span class='listEvenBold''>" . $blockPage->buildLink("http://www.aditus.nu/jpgraph/", "JpGraph", "powered") . "</span>	
			</div>
		";
    }
} else {
    $block4->noresults();
}

$block4->closeToggle();
$block4->closeFormResults();
$block4->openPaletteScript();


if ($teamMember === true || $profilSession == "5") {
    $block4->paletteScript(0, "add", "../subtasks/editsubtask.php?task=$id", "true,false,false", $strings["add"]);
    $block4->paletteScript(1, "remove", "../subtasks/deletesubtasks.php?task=$id", "false,true,true", $strings["delete"]);
}
$block4->paletteScript(6, "info", "../subtasks/viewsubtask.php?task=$id", "false,true,false", $strings["view"]);

if ($teamMember === true || $profilSession == "5") {
    $block4->paletteScript(7, "edit", "../subtasks/editsubtask.php?task=$id", "false,true,true", $strings["edit"]);
}
$block4->closePaletteScript(count($listSubtasks), array_column($listSubtasks, 'subtas_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
