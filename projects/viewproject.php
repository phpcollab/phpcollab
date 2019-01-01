<?php

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';

$topics = new \phpCollab\Topics\Topics();
$tasks = new \phpCollab\Tasks\Tasks();
$teams = new \phpCollab\Teams\Teams();
$files = new \phpCollab\Files\Files();
$notes = new \phpCollab\Notes\Notes();
$support = new \phpCollab\Support\Support();
$projects = new \phpCollab\Projects\Projects();
$topics = new \phpCollab\Topics\Topics();
$notes = new \phpCollab\Notes\Notes();
$phases = new \phpCollab\Phases\Phases();

$id = phpCollab\Util::returnGlobal('id', 'REQUEST');
$project = phpCollab\Util::returnGlobal('project', 'REQUEST');
$action = phpCollab\Util::returnGlobal('action', 'GET');

if ($action == "publish") {
    $closeTopic = phpCollab\Util::returnGlobal('closeTopic', 'GET');

    if ($closeTopic == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
            $pieces = explode(",", $id);
            $num = count($pieces);
        } else {
            $num = "1";
        }

        $topics->closeTopic($id);

        $msg = "closeTopic";
        $id = $project;
    }

    $addToSiteTask = phpCollab\Util::returnGlobal('addToSiteTask', 'GET');

    if ($addToSiteTask == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }

        $tasks->publishTasks($id);
        $msg = "addToSite";
        $id = $project;
    }

    $removeToSiteTask = phpCollab\Util::returnGlobal('removeToSiteTask', 'GET');

    if ($removeToSiteTask == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }

        $tasks->unPublishTasks($id);
        $msg = "removeToSite";
        $id = $project;
    }

    $addToSiteTopic = phpCollab\Util::returnGlobal('addToSiteTopic', 'GET');

    if ($addToSiteTopic == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }

        $topics->publishTopic($id);
        $msg = "addToSite";
        $id = $project;
    }

    $removeToSiteTopic = phpCollab\Util::returnGlobal('removeToSiteTopic', 'GET');
    if ($removeToSiteTopic == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $topics->unPublishTopic($id);

        $msg = "removeToSite";
        $id = $project;
    }

    $addToSiteTeam = phpCollab\Util::returnGlobal('addToSiteTeam', 'GET');
    if ($addToSiteTeam == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }

        $teams->publishToSite($project, $id);
        $msg = "addToSite";
        $id = $project;
    }

    $removeToSiteTeam = phpCollab\Util::returnGlobal('removeToSiteTeam', 'GET');
    if ($removeToSiteTeam == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }

        $teams->unPublishToSite($project, $id);
        $msg = "removeToSite";
        $id = $project;
    }

    $addToSiteFile = phpCollab\Util::returnGlobal('addToSiteFile', 'GET');

    if ($addToSiteFile == "true") {
        $id = str_replace("**", ",", $id);
        $files->publishFileByIdOrVcParent($id);
        $msg = "addToSite";
        $id = $project;
    }

    $removeToSiteFile = phpCollab\Util::returnGlobal('removeToSiteFile', 'GET');

    if ($removeToSiteFile == "true") {
        $id = str_replace("**", ",", $id);
        $files->unPublishFileByIdOrVcParent($id);
        $msg = "removeToSite";
        $id = $project;
    }

    $addToSiteNote = phpCollab\Util::returnGlobal('addToSiteNote', 'GET');

    if ($addToSiteNote == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }

        $notes->publishToSite($id);
        $msg = "addToSite";
        $id = $project;
    }

    $removeToSiteNote = phpCollab\Util::returnGlobal('removeToSiteNote', 'GET');
    if ($removeToSiteNote == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }

        $notes->unPublishFromSite($id);
        $msg = "removeToSite";
        $id = $project;
    }
}

if ($msg == "demo") {
    $id = $project;
}

$projectDetail = $projects->getProjectById($id);

if (!$projectDetail) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}

$listTasksTime = $tasks->getTasksByProjectId($id, 'tas.name');

if ($listTasksTime) {
    foreach ($listTasksTime as $task) {
        $estimated_time = (isset($estimated_time) && $estimated_time != "") ? $estimated_time : 0;
        $estimated_time = $estimated_time + intval($task["tas_estimated_time"]);

        $actual_time = (isset($actual_time) && $actual_time != "") ? $actual_time : 0;
        $actual_time = $actual_time + intval($task["tas_actual_time"]);


        if ($task["tas_complete_date"] != "" && $task["tas_complete_date"] != "--" && $task["tas_due_date"] != "--") {
            $diff = phpCollab\Util::diffDate($task["tas_complete_date"], $task["tas_due_date"]);
            $diff_time = (isset($diff_time) && $diff_time != "") ? $diff_time : 0;
            $diff_time = $diff_time + $diff;
        }
    }

    if ($diff_time > 0) {
        $diff_time = "<b>+$diff_time</b>";
    }
}

$teamMember = "false";
$teamMember = $teams->isTeamMember($id, $idSession);

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

if ($enableHelpSupport == "true" && ($teamMember == "true" || $profilSession == "5")) {
    $comptListNewRequests = count($support->getSupportRequestByStatusAndProjectId(0, $projectDetail["pro_id"]));
    $comptListOpenRequests = count($support->getSupportRequestByStatusAndProjectId(1, $projectDetail["pro_id"]));
    $comptListCompleteRequests = count($support->getSupportRequestByStatusAndProjectId(2, $projectDetail["pro_id"]));
}

$setTitle .= " : View Project (" . $projectDetail["pro_name"] . ")";

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($projectDetail["pro_name"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->setLimitsNumber(4);

$idStatus = $projectDetail["pro_status"];
$idPriority = $projectDetail["pro_priority"];

$block1 = new phpCollab\Block();

$block1->form = "pdD";
$block1->openForm("../projects/listprojects.php#" . $block1->form . "Anchor");

$block1->headingToggle($strings["project"] . " : " . $projectDetail["pro_name"]);

if ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5") {
    $block1->openPaletteIcon();

    if ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5") {
        $block1->paletteIcon(0, "remove", $strings["delete"]);
        $block1->paletteIcon(1, "copy", $strings["copy"]);
        $block1->paletteIcon(2, "export", $strings["export"]);
        $block1->paletteIcon(3, "edit", $strings["edit"]);
    }

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        $block1->paletteIcon(5, "bug", $strings["bug"]);
    }

    $block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"], $projectDetail["pro_name"]);
$block1->contentRow($strings["project_id"], $projectDetail["pro_id"]);
$block1->contentRow($strings["priority"], "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);

//List open phases and link to phase details
if ($projectDetail["pro_phase_set"] != "0") {
    $projectPhases = $phases->getPhasesByProjectIdAndIsCompleted($id, 'pha.order_num');
    $comptCurrentPhase = count($projectPhases);

    if (!$projectPhases) {
        $block1->contentRow($strings["current_phase"], $strings["no_current_phase"]);
    } else {
        foreach ($projectPhases as $phase) {
            if ($i != $comptCurrentPhase) {
                $pnum = $i + 1;
                $phasesList .= "$pnum.<a href=\"../phases/viewphase.php?id=" . $phase["pha_id"] . "\">" . $phase["pha_name"] . "</a>  ";
            }
        }
        $block1->contentRow($strings["current_phase"], $phasesList);
    }
} else {
    $block1->contentRow($strings["phase_enabled"], $strings["false"]);
}

$block1->contentRow($strings["description"], nl2br($projectDetail["pro_description"]));
$block1->contentRow($strings["url_dev"], $blockPage->buildLink($projectDetail["pro_url_dev"], $projectDetail["pro_url_dev"], "out"));
$block1->contentRow($strings["url_prod"], $blockPage->buildLink($projectDetail["pro_url_prod"], $projectDetail["pro_url_prod"], "out"));
$block1->contentRow($strings["owner"], $blockPage->buildLink("../users/viewuser.php?id=" . $projectDetail["pro_mem_id"], $projectDetail["pro_mem_name"], "in") . " (" . $blockPage->buildLink($projectDetail["pro_mem_email_work"], $projectDetail["pro_mem_login"], "mail") . ")");
$block1->contentRow($strings["created"], phpCollab\Util::createDate($projectDetail["pro_created"], $timezoneSession));
$block1->contentRow($strings["modified"], phpCollab\Util::createDate($projectDetail["pro_modified"], $timezoneSession));

if ($projectDetail["pro_org_id"] == "1") {
    $block1->contentRow($strings["organization"], $strings["none"]);
} else {
    $block1->contentRow($strings["organization"], $blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"], $projectDetail["pro_org_name"], "in"));
}

$block1->contentRow($strings["status"], $status[$idStatus]);

if ($fileManagement == "true") {
    $block1->contentRow($strings["max_upload"] . $blockPage->printHelp("max_file_size"), phpCollab\Util::convertSize($projectDetail["pro_upload_max"]));
    $block1->contentRow(
        $strings["project_folder_size"] . $blockPage->printHelp("project_disk_space"),
        phpCollab\Util::convertSize(phpCollab\Util::folderInfoSize("../files/" . $projectDetail["pro_id"] . "/"))
    );
}

$block1->contentRow($strings["estimated_time"], $estimated_time . " " . $strings["hours"]);
$block1->contentRow($strings["actual_time"], $actual_time . " " . $strings["hours"]);
$block1->contentRow($strings["scope_creep"] . $blockPage->printHelp("project_scope_creep"), $diff_time . " " . $strings["days"]);

if ($sitePublish == "true") {
    if ($projectDetail["pro_published"] == "1") {
        $block1->contentRow($strings["project_site"], "&lt;" . $blockPage->buildLink("../projects/addprojectsite.php?id=$id", $strings["create"] . "...", "in") . "&gt;");
    } else {
        $block1->contentRow($strings["project_site"], "&lt;" . $blockPage->buildLink("../projects/viewprojectsite.php?id=$id", $strings["details"], "in") . "&gt;");
    }
}

if ($enableInvoicing == "true" && ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5")) {
    if ($projectDetail["pro_invoicing"] == "1") {
        $block1->contentRow($strings["invoicing"], $strings["true"]);
    } else {
        $block1->contentRow($strings["invoicing"], $strings["false"]);
    }

    $block1->contentRow($strings["hourly_rate"], $projectDetail["pro_hourly_rate"]);
}

if ($enableHelpSupport == "true" && ($teamMember == "true" || $profilSession == "5") && $supportType == "team") {
    $block1->contentTitle($strings["support"]);
    $block1->contentRow($strings["new_requests"], "$comptListNewRequests - " . $blockPage->buildLink("../support/support.php?action=new&project=" . $projectDetail["pro_id"], $strings["manage_new_requests"], "in"));
    $block1->contentRow($strings["open_requests"], "$comptListOpenRequests - " . $blockPage->buildLink("../support/support.php?action=open&project=" . $projectDetail["pro_id"], $strings["manage_open_requests"], "in"));
    $block1->contentRow($strings["closed_requests"], "$comptListCompleteRequests - " . $blockPage->buildLink("../support/support.php?action=complete&project=" . $projectDetail["pro_id"], $strings["manage_closed_requests"], "in"));
}

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5") {
    $block1->openPaletteScript();

    if ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5") {
        $block1->paletteScript(0, "remove", "../projects/deleteproject.php?id=$id", "true,true,false", $strings["delete"]);
        $block1->paletteScript(1, "copy", "../projects/editproject.php?id=" . $projectDetail["pro_id"] . "&docopy=true", "true,true,false", $strings["copy"]);
        $block1->paletteScript(2, "export", "../projects/exportproject.php?languageSession={$_SESSION["languageSession"]}&type=project&id=" . $projectDetail["pro_id"] . "", "true,true,false", $strings["export"]);
        $block1->paletteScript(3, "edit", "../projects/editproject.php?id=" . $projectDetail["pro_id"] . "&docopy=false", "true,true,false", $strings["edit"]);
    }

    if ($enableMantis == "true") {
        $block1->paletteScript(5, "bug", $pathMantis . "login.php?id=" . $projectDetail["pro_id"] . "&url=http://{$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]}&username=$loginSession&password=$passwordSession", "true,true,false", $strings["bug"]);
    }

    $block1->closePaletteScript("", []);
}

//Phase or Task list block
if ($projectDetail["pro_phase_set"] != "0") {
    $block7 = new phpCollab\Block();
    $block7->form = "wbSe";
    $block7->openForm("../projects/viewproject.php?id=$id&#" . $block7->form . "Anchor");
    $block7->headingToggle($strings["phases"]);
    $block7->openPaletteIcon();

    $block7->paletteIcon(0, "info", $strings["view"]);

    if ($teamMember == "true" || $profilSession == "5") {
        if ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5") {
            $block7->paletteIcon(1, "edit", $strings["edit"]);
        }
    }

    $block7->closePaletteIcon();

    $block7->sorting("phases", $sortingUser["phases"], "pha.order_num ASC", $sortingFields = array(0 => "pha.order_num", 1 => "pha.name", 2 => "none", 3 => "none", 4 => "pha.status", 5 => "pha.date_start", 6 => "pha.date_end"));

    $listPhases = $phases->getPhasesByProjectId($id, $block7->sortingValue);

    if ($listPhases) {
        $block7->openResults();
        $block7->labels(
            $labels = array(
                0 => $strings["order"],
                1 => $strings["name"],
                2 => $strings["total_tasks"],
                3 => $strings["uncomplete_tasks"],
                4 => $strings["status"],
                5 => $strings["date_start"],
                6 => $strings["date_end"]
            ),
        "false");

        foreach ($listPhases as $phase) {
            $block7->openRow();
            $block7->checkboxRow($phase["pha_id"]);
            $block7->cellRow($phase["pha_order_num"]);
            $block7->cellRow($blockPage->buildLink("../phases/viewphase.php?id=" . $phase["pha_id"], $phase["pha_name"], "in"));
            $block7->cellRow($tasks->getCountOpenTasksByPhaseAndProject($phase["pha_order_num"], $id));
            $block7->cellRow($tasks->getCountUncompletedTasks($phase["pha_order_num"], $id));
            $block7->cellRow($phaseStatus[$phase["pha_status"]]);
            $block7->cellRow($phase["pha_date_start"]);
            $block7->cellRow($phase["pha_date_end"]);
            $block7->closeRow();
        }

        $block7->closeResults();
    } else {
        $block7->noresults();
    }

    $block7->closeToggle();
    $block7->closeFormResults();

    $block7->openPaletteScript();
    $block7->paletteScript(0, "info", "../phases/viewphase.php?", "false,true,true", $strings["view"]);

    if ($teamMember == "true" || $profilSession == "5") {
        if ($idSession == $projectDetail["pro_owner"] || $profilSession == "0" || $profilSession == "5") {
            $block7->paletteScript(1, "edit", "../phases/editphase.php?", "false,true,true", $strings["edit"]);
        }
    }

    $block7->closePaletteScript(count($listPhases), array_column($listPhases, 'pha_id'));
} else {
    $block2 = new phpCollab\Block();
    $block2->form = "wbTuu";
    $block2->openForm("../projects/viewproject.php?&id=" . $projectDetail["pro_id"] . "#" . $block2->form . "Anchor");

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

    $block2->setLimit($blockPage->returnLimit(1));
    $block2->setRowsLimit(5);
    $block2->setSortName("projects");

    $block2->sorting("project_tasks", $sortingUser["project_tasks"], "tas.name ASC", $sortingFields = array(0 => "tas.name", 1 => "tas.priority", 2 => "tas.status", 3 => "tas.completion", 4 => "tas.due_date", 5 => "mem.login", 6 => "tas.published"));

    $block2->setRecordsTotal( $tasks->getCountAllTasksForProject($id) );

    $listTasks = $tasks->getTasksByProjectId($id, $block2->getLimit(), $block2->getRowsLimit(), $block2->sortingValue);
    if ($listTasks) {
        $block2->openResults();
        $block2->labels($labels = array(0 => $strings["name"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["completion"], 4 => $strings["due_date"], 5 => $strings["assigned_to"], 6 => $strings["published"]), "true");

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

            if ($task["tas_due_date"] <= $date && $task["tas_completion"] != "10") {
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

            if ($sitePublish == "true") {
                $block2->cellRow($statusPublish[$idPublish]);
            }

            $block2->closeRow();
        }

        $block2->closeResults();
        $block2->limitsFooter("1", $blockPage->getLimitsNumber(), "../tasks/listtasks.php?project=$id&", "id=$id");

        if ($activeJpgraph == "true" && $gantt == "true") {
            echo "
				<div id='ganttChart_taskList' class='ganttChart'>
					<img src='../tasks/graphtasks.php?&project=" . $projectDetail["pro_id"] . "' alt=''><br/>
					<span class='listEvenBold''>" . $blockPage->buildLink("http://www.aditus.nu/jpgraph/", "JpGraph", "powered") . "</span>	
				</div>
			";
        }
    } else {
        $block2->noresults();
    }

    $block2->closeToggle();
    $block2->closeFormResults();

    $block2->openPaletteScript();
    if ($teamMember == "true" || $profilSession == "5") {
        $block2->paletteScript(0, "add", "../tasks/edittask.php?project=" . $projectDetail["pro_id"] . "", "true,false,false", $strings["add"]);
        $block2->paletteScript(1, "remove", "../tasks/deletetasks.php?project=" . $projectDetail["pro_id"] . "", "false,true,true", $strings["delete"]);
        $block2->paletteScript(2, "copy", "../tasks/edittask.php?project=" . $projectDetail["pro_id"] . "&docopy=true", "false,true,false", $strings["copy"]);
        if ($sitePublish == "true") {
            $block2->paletteScript(4, "add_projectsite", "../projects/viewproject.php?addToSiteTask=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
            $block2->paletteScript(5, "remove_projectsite", "../projects/viewproject.php?removeToSiteTask=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
        }
    }

    $block2->paletteScript(6, "info", "../tasks/viewtask.php?", "false,true,false", $strings["view"]);
    if ($teamMember == "true" || $profilSession == "5") {
        $block2->paletteScript(7, "edit", "../tasks/edittask.php?project=" . $projectDetail["pro_id"] . "", "false,true,true", $strings["edit"]);
    }

    $block2->closePaletteScript(count($listTasks), array_column($listTasks, 'tas_id'));
}

$discussionsBlock = new phpCollab\Block();
$discussionsBlock->form = "pdH";
$discussionsBlock->openForm("../projects/viewproject.php?id=$id&#" . $discussionsBlock->form . "Anchor");
$discussionsBlock->headingToggle($strings["discussions"]);
$discussionsBlock->openPaletteIcon();

if ($teamMember == "true" || $profilSession == "5") {
    $discussionsBlock->paletteIcon(0, "add", $strings["add"]);
}

if ($idSession == $projectDetail["pro_owner"] || $profilSession == "5") {
    $discussionsBlock->paletteIcon(1, "remove", $strings["delete"]);
    $discussionsBlock->paletteIcon(2, "lock", $strings["close"]);

    if ($sitePublish == "true") {
        $discussionsBlock->paletteIcon(3, "add_projectsite", $strings["add_project_site"]);
        $discussionsBlock->paletteIcon(4, "remove_projectsite", $strings["remove_project_site"]);
    }
}

$discussionsBlock->paletteIcon(5, "info", $strings["view"]);
$discussionsBlock->closePaletteIcon();
$discussionsBlock->setLimit($blockPage->returnLimit(2));
$discussionsBlock->setRowsLimit(5);
$discussionsBlock->setSortName("discussion");
$discussionsBlock->sorting("project_discussions", $sortingUser["project_discussions"], "topic.last_post DESC", $sortingFields = array(0 => "topic.subject", 1 => "mem.login", 2 => "topic.posts", 3 => "topic.last_post", 4 => "topic.status", 5 => "topic.published"));

$topicsList = $topics->getTopicsByProjectId($id, $discussionsBlock->getLimit(), $discussionsBlock->getRowsLimit(), $discussionsBlock->sortingValue);
$discussionsBlock->setRecordsTotal( $topics->getTopicCountForProject($id) );

if ($topicsList) {
    $discussionsBlock->openResults();

    $discussionsBlock->labels($labels = array(0 => $strings["topic"], 1 => $strings["owner"], 2 => $strings["posts"], 3 => $strings["last_post"], 4 => $strings["status"], 5 => $strings["published"]), "true");

    foreach ($topicsList as $topic) {
        $idStatus = $topic["top_status"];
        $idPublish = $topic["top_published"];
        $discussionsBlock->openRow();
        $discussionsBlock->checkboxRow($topic["top_id"]);
        $discussionsBlock->cellRow($blockPage->buildLink("../topics/viewtopic.php?id=" . $topic["top_id"], $topic["top_subject"], "in"));
        $discussionsBlock->cellRow($blockPage->buildLink($topic["top_mem_email_work"], $topic["top_mem_login"], "mail"));
        $discussionsBlock->cellRow($topic["top_posts"]);

        if ($topic["top_last_post"] > $_SESSION["lastvisiteSession"]) {
            $discussionsBlock->cellRow("<b>" . phpCollab\Util::createDate($topic["top_last_post"], $timezoneSession) . "</b>");
        } else {
            $discussionsBlock->cellRow(phpCollab\Util::createDate($topic["top_last_post"], $timezoneSession));
        }

        $discussionsBlock->cellRow($statusTopic[$idStatus]);

        if ($sitePublish == "true") {
            $discussionsBlock->cellRow($statusPublish[$idPublish]);
        }

        $discussionsBlock->closeRow();
    }

    $discussionsBlock->closeResults();
    $discussionsBlock->limitsFooter("2", $blockPage->getLimitsNumber(), "../topics/listtopics.php?project=$id&", "id=$id");
} else {
    $discussionsBlock->noresults();
}

$discussionsBlock->closeToggle();
$discussionsBlock->closeFormResults();
$discussionsBlock->openPaletteScript();

if ($teamMember == "true" || $profilSession == "5") {
    $discussionsBlock->paletteScript(0, "add", "../topics/addtopic.php?project=" . $projectDetail["pro_id"] . "", "true,false,false", $strings["add"]);
}

if ($idSession == $projectDetail["pro_owner"] || $profilSession == "5") {
    $discussionsBlock->paletteScript(1, "remove", "../topics/deletetopics.php?project=" . $projectDetail["pro_id"] . "", "false,true,true", $strings["delete"]);
    $discussionsBlock->paletteScript(2, "lock", "../projects/viewproject.php?closeTopic=true&project=$id&action=publish", "false,true,true", $strings["close"]);

    if ($sitePublish == "true") {
        $discussionsBlock->paletteScript(3, "add_projectsite", "../projects/viewproject.php?addToSiteTopic=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
        $discussionsBlock->paletteScript(4, "remove_projectsite", "../projects/viewproject.php?&removeToSiteTopic=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
    }
}

$discussionsBlock->paletteScript(5, "info", "../topics/viewtopic.php?", "false,true,false", $strings["view"]);
$discussionsBlock->closePaletteScript(count($topicsList), array_column($topicsList, 'top_id'));

$teamBlock = new phpCollab\Block();
$teamBlock->form = "pdM";
$teamBlock->openForm("../projects/viewproject.php?&id=" . $projectDetail["pro_id"] . "#" . $teamBlock->form . "Anchor");
$teamBlock->headingToggle($strings["team"]);
$teamBlock->openPaletteIcon();

if ($idSession == $projectDetail["pro_owner"] || $profilSession == "5") {
    $teamBlock->paletteIcon(0, "add", $strings["add"]);
    $teamBlock->paletteIcon(1, "remove", $strings["delete"]);

    if ($sitePublish == "true") {
        $teamBlock->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
        $teamBlock->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
    }
}

$teamBlock->paletteIcon(4, "info", $strings["view"]);
$teamBlock->paletteIcon(5, "email", $strings["email"]);
$teamBlock->closePaletteIcon();
$teamBlock->setLimit($blockPage->returnLimit(3));
$teamBlock->setRowsLimit(5);
$teamBlock->setSortName("team");
$teamBlock->sorting("team", $sortingUser["team"], "mem.name ASC", $sortingFields = array(0 => "mem.name", 1 => "mem.title", 2 => "mem.login", 3 => "mem.phone_work", 4 => "log.connected", 5 => "tea.published"));

$teamBlock->setRecordsTotal( $teams->getTopicCountByProject($id) );

$teamList = $teams->getTeamByProjectId($id, $teamBlock->getLimit(), $teamBlock->getRowsLimit(), $teamBlock->sortingValue);

$teamBlock->openResults();
$teamBlock->labels($labels = array(0 => $strings["full_name"], 1 => $strings["title"], 2 => $strings["user_name"], 3 => $strings["work_phone"], 4 => $strings["connected"], 5 => $strings["published"]), "true");

foreach ($teamList as $teamMember) {
    
    if ($teamMember["tea_mem_phone_work"] == "") {
        $teamMember["tea_mem_phone_work"] = \phpCollab\Util::doubleDash();
    }

    if ($teamMember["tea_mem_title"] == "") {
        $teamMember["tea_mem_title"] = \phpCollab\Util::doubleDash();
    }

    $idPublish = $teamMember["tea_published"];
    $teamBlock->openRow();
    $teamBlock->checkboxRow($teamMember["tea_mem_id"]);
    $teamBlock->cellRow($blockPage->buildLink("../users/viewuser.php?id=" . $teamMember["tea_mem_id"], "(" .$teamMember["tea_mem_id"] . ") " . $teamMember["tea_mem_name"], "in"));
    $teamBlock->cellRow($teamMember["tea_mem_title"]);
    $teamBlock->cellRow($blockPage->buildLink($teamMember["tea_mem_email_work"], $teamMember["tea_mem_login"], "mail"));
    $teamBlock->cellRow($teamMember["tea_mem_phone_work"]);

    if ($teamMember["tea_log_connected"] > $dateunix - 5 * 60) {
        $teamBlock->cellRow($strings["yes"] . " " . $z);
    } else {
        $teamBlock->cellRow($strings["no"]);
    }

    if ($sitePublish == "true") {
        $teamBlock->cellRow($statusPublish[$idPublish]);
    }

    $teamBlock->closeRow();
}

$teamBlock->closeResults();
$teamBlock->limitsFooter("3", $blockPage->getLimitsNumber(), "../teams/listusers.php?id=$id&", "id=$id");
$teamBlock->closeToggle();
$teamBlock->closeFormResults();
$teamBlock->openPaletteScript();

if ($idSession == $projectDetail["pro_owner"] || $profilSession == "5") {
    $teamBlock->paletteScript(0, "add", "../teams/adduser.php?project=" . $projectDetail["pro_id"] . "", "true,true,true", $strings["add"]);
    $teamBlock->paletteScript(1, "remove", "../teams/deleteusers.php?project=" . $projectDetail["pro_id"] . "", "false,true,true", $strings["delete"]);

    if ($sitePublish == "true") {
        $teamBlock->paletteScript(2, "add_projectsite", "../projects/viewproject.php?addToSiteTeam=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
        $teamBlock->paletteScript(3, "remove_projectsite", "../projects/viewproject.php?removeToSiteTeam=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
    }
}

$teamBlock->paletteScript(4, "info", "../users/viewuser.php?", "false,true,false", $strings["view"]);
$teamBlock->paletteScript(5, "email", "../users/emailusers.php?", "false,true,true", $strings["email"]);
$teamBlock->closePaletteScript(count($teamList), array_column($teamList, 'tea_mem_id'));

/**
 * Begin Linked Content
 */
if ($fileManagement == "true") {
    $files = new \phpCollab\Files\Files();

    $filesBlock = new phpCollab\Block();
    $filesBlock->form = "tdC";
    $filesBlock->openForm("../projects/viewproject.php?&id=$id#" . $filesBlock->form . "Anchor");
    $filesBlock->headingToggle($strings["linked_content"]);
    $filesBlock->openPaletteIcon();

    if ($teamMember == "true" || $profilSession == "5") {
        $filesBlock->paletteIcon(0, "add", $strings["add"]);
        $filesBlock->paletteIcon(1, "remove", $strings["delete"]);

        if ($sitePublish == "true") {
            $filesBlock->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
            $filesBlock->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
        }
    }

    $filesBlock->paletteIcon(4, "info", $strings["view"]);

    if ($teamMember == "true" || $profilSession == "5") {
        $filesBlock->paletteIcon(5, "edit", $strings["edit"]);
    }

    $filesBlock->closePaletteIcon();
    $filesBlock->sorting("files", $sortingUser["files"], "fil.name ASC", $sortingFields = array(0 => "fil.extension", 1 => "fil.name", 2 => "fil.owner", 3 => "fil.date", 4 => "fil.status", 5 => "fil.published"));

    $filesList = $files->getFilesByProjectAndPhaseWithoutTasksAndParent($id, 0, $filesBlock->sortingValue);

    if ($filesList) {
        $filesBlock->openResults();
        $filesBlock->labels($labels = array(0 => $strings["type"], 1 => $strings["name"], 2 => $strings["owner"], 3 => $strings["date"], 4 => $strings["approval_tracking"], 5 => $strings["published"]), "true");

        foreach ($filesList as $file) {
            $existFile = "false";
            $idStatus = $file["fil_status"];

            $idPublish = $file["fil_published"];

            $fileHandler = new phpCollab\FileHandler();
            $type = $fileHandler->fileInfoType($file["fil_extension"]);

            if (file_exists("../files/" . $file["fil_project"] . "/" . $file["fil_name"])) {
                $existFile = "true";
            }

            $filesBlock->openRow();
            $filesBlock->checkboxRow($file["fil_id"]);

            if ($existFile == "true") {
                $filesBlock->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $type, "icone"));
            } else {
                $filesBlock->cellRow("&nbsp;");
            }

            if ($existFile == "true") {
                $filesBlock->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $file["fil_name"], "in"));
            } else {
                $filesBlock->cellRow($strings["missing_file"] . " (" . $file["fil_name"] . ")");
            }

            $filesBlock->cellRow($blockPage->buildLink($file["fil_mem_email_work"], $file["fil_mem_login"], "mail"));
            $filesBlock->cellRow($file["fil_date"]);
            $filesBlock->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=" . $file["fil_id"], $statusFile[$idStatus], "in"));

            if ($sitePublish == "true") {
                $filesBlock->cellRow($statusPublish[$idPublish]);
            }

            $filesBlock->closeRow();
        }

        $filesBlock->closeResults();
    } else {
        $filesBlock->noresults();
    }

    $filesBlock->closeToggle();
    $filesBlock->closeFormResults();
    $filesBlock->openPaletteScript();

    if ($teamMember == "true" || $profilSession == "5") {
        $filesBlock->paletteScript(0, "add", "../linkedcontent/addfile.php?project=$id", "true,true,true", $strings["add"]);
        $filesBlock->paletteScript(1, "remove", "../linkedcontent/deletefiles.php?project=$id", "false,true,true", $strings["delete"]);

        if ($sitePublish == "true") {
            $filesBlock->paletteScript(2, "add_projectsite", "../projects/viewproject.php?addToSiteFile=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
            $filesBlock->paletteScript(3, "remove_projectsite", "../projects/viewproject.php?removeToSiteFile=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
        }
    }

    $filesBlock->paletteScript(4, "info", "../linkedcontent/viewfile.php?", "false,true,false", $strings["view"]);

    if ($teamMember == "true" || $profilSession == "5") {
        $filesBlock->paletteScript(5, "edit", "../linkedcontent/viewfile.php?edit=true", "false,true,false", $strings["edit"]);
    }

    $filesBlock->closePaletteScript(count($filesList), array_column($filesList, 'fil_id'));
}
/**
 * End Linked Content
 * -------------------
 * Begin Notes Section
 */
$notesBlock = new phpCollab\Block();
$notesBlock->form = "wbJ";
$notesBlock->openForm("../projects/viewproject.php?&id=" . $projectDetail["pro_id"] . "#" . $notesBlock->form . "Anchor");
$notesBlock->headingToggle($strings["notes"]);
$notesBlock->openPaletteIcon();

if ($teamMember == "true" || $profilSession == "5") {
    $notesBlock->paletteIcon(0, "add", $strings["add"]);
    $notesBlock->paletteIcon(1, "remove", $strings["delete"]);
    if ($sitePublish == "true") {
        $notesBlock->paletteIcon(3, "add_projectsite", $strings["add_project_site"]);
        $notesBlock->paletteIcon(4, "remove_projectsite", $strings["remove_project_site"]);
    }
}

$notesBlock->paletteIcon(5, "info", $strings["view"]);
if ($teamMember == "true" || $profilSession == "5") {
    $notesBlock->paletteIcon(6, "edit", $strings["edit"]);
}

$notesBlock->closePaletteIcon();
$notesBlock->setLimit($blockPage->returnLimit(4));
$notesBlock->setRowsLimit(5);
$notesBlock->setSortName("notes");

$comptTopic = count($topicNote);

if ($comptTopic != "0") {
    $notesBlock->sorting("notes", $sortingUser["notes"], "note.date DESC", $sortingFields = array(0 => "note.subject", 1 => "note.topic", 2 => "note.date", 3 => "mem.login", 4 => "note.published"));
} else {
    $notesBlock->sorting("notes", $sortingUser["notes"], "note.date DESC", $sortingFields = array(0 => "note.subject", 1 => "note.date", 2 => "mem.login", 3 => "note.published"));
}

$notesBlock->setRecordsTotal( count($notes->getNotesCountByProject($id)));

$notesList = $notes->getNoteByProject($id, $notesBlock->getLimit(), $notesBlock->getRowsLimit(), $notesBlock->sortingValue);

if ($notesList) {
    $notesBlock->openResults();

    if ($comptTopic != "0") {
        $notesBlock->labels($labels = array(0 => $strings["subject"], 1 => $strings["topic"], 2 => $strings["date"], 3 => $strings["owner"], 4 => $strings["published"]), "true");
    } else {
        $notesBlock->labels($labels = array(0 => $strings["subject"], 1 => $strings["date"], 2 => $strings["owner"], 3 => $strings["published"]), "true");
    }

    foreach ($notesList as $note) {
        $idPublish = $note["note_published"];
        $notesBlock->openRow();
        $notesBlock->checkboxRow($note["note_id"]);
        $notesBlock->cellRow($blockPage->buildLink("../notes/viewnote.php?id=" . $note["note_id"], $note["note_subject"], "in"));

        if ($comptTopic != "0") {
            $notesBlock->cellRow( !empty($topicNote[$note["note_topic"]]) ? $topicNote[$note["note_topic"]] : \phpCollab\Util::doubleDash());
        }

        $notesBlock->cellRow($note["note_date"]);
        $notesBlock->cellRow($blockPage->buildLink($note["note_mem_email_work"], $note["note_mem_login"], "mail"));

        if ($sitePublish == "true") {
            $notesBlock->cellRow($statusPublish[$idPublish]);
        } else {
            $notesBlock->cellRow("&nbsp;");
        }

        $notesBlock->closeRow();
    }

    $notesBlock->closeResults();
    $notesBlock->limitsFooter("4", $blockPage->getLimitsNumber(), "../notes/listnotes.php?project=$id&", "id=$id");
} else {
    $notesBlock->noresults();
}

$notesBlock->closeToggle();
$notesBlock->closeFormResults();
$notesBlock->openPaletteScript();

if ($teamMember == "true" || $profilSession == "5") {
    $notesBlock->paletteScript(0, "add", "../notes/editnote.php?project=" . $projectDetail["pro_id"] . "", "true,true,true", $strings["add"]);
    $notesBlock->paletteScript(1, "remove", "../notes/deletenotes.php?project=" . $projectDetail["pro_id"] . "", "false,true,true", $strings["delete"]);
    if ($sitePublish == "true") {
        $notesBlock->paletteScript(3, "add_projectsite", "../projects/viewproject.php?addToSiteNote=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
        $notesBlock->paletteScript(4, "remove_projectsite", "../projects/viewproject.php?removeToSiteNote=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
    }
}

$notesBlock->paletteScript(5, "info", "../notes/viewnote.php?", "false,true,false", $strings["view"]);

if ($teamMember == "true" || $profilSession == "5") {
    $notesBlock->paletteScript(6, "edit", "../notes/editnote.php?project=" . $projectDetail["pro_id"] . "", "false,true,false", $strings["edit"]);
}

$notesBlock->closePaletteScript(count($notesList), array_column($notesList, 'note_id'));
/**
 * End Notes section
 */

include APP_ROOT . '/themes/' . THEME . '/footer.php';
