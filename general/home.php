<?php

use phpCollab\Bookmarks\Bookmarks;
use phpCollab\Members\Members;
use phpCollab\NewsDesk\NewsDesk;
use phpCollab\Notes\Notes;
use phpCollab\Projects\Projects;
use phpCollab\Reports\Reports;
use phpCollab\Tasks\Tasks;
use phpCollab\Topics\Topics;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';

$strings = $GLOBALS["strings"];
$passwordSession = $_SESSION["passwordSession"];

$setTitle .= " : Home Page";

$defaultNumRowsToDisplay = 15;

$topics = new Topics();
$members = new Members();
$projects = new Projects();
$tasks = new Tasks();
$reports = new Reports();
$notes = new Notes();
$newsdesk = new NewsDesk();

$test = $date;
$DateYear = substr("$test", 0, 4); // DateYear
$DateMonth = substr("$test", 5, 2); // DateMonth
$DateDay = substr("$test", 8, 2); // DateDay
$DateMonth = $DateMonth - 1;
if ($DateMonth <= 0) {
    $DateMonth = $DateMonth + 12;
    $DateYear = $DateYear - 1;
}
$DateMonth = (strlen($DateMonth) > 1) ? $DateMonth : "0" . $DateMonth;

$dateFilter = "$DateYear-$DateMonth-$DateDay";

if ($action == 'publish') {
    if ($closeTopic == 'true') {
        $multi = strstr($id, '**');

        if ($multi != '') {
            $id = str_replace('**', ',', $id);
            $pieces = explode(',', $id);
            $num = count($pieces);
        } else {
            $num = '1';
        }

        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
            $topics->closeTopic($id);
        } else {
            $topics->closeTopic($id);
        }
        $msg = 'closeTopic';
    }

    if ($_GET['addToSiteTopic'] == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
            $topics->publishTopic($id);
        } else {
            $topics->publishTopic($id);
        }

        $msg = 'addToSite';
    }

    if ($_GET['removeToSiteTopic'] == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
            $topics->unPublishTopic($id);
        } else {
            $topics->unPublishTopic($id);
        }

        $msg = "removeToSite";
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();

$blockPage->setLimitsNumber(4);

$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php", $strings["home"], 'in'));
$blockPage->itemBreadcrumbs($nameSession);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

/**
 * start to show bookmark block
 */
if ($showHomeBookmarks) {
    $bookmarks = new Bookmarks();

    $block6 = new phpCollab\Block();

    $block6->sorting("bookmarks", $sortingUser["bookmarks"], "boo.name ASC", $sortingFields = [0 => "boo.name", 1 => "boo.category", 2 => "boo.shared"]);

    $bookmarksList = $bookmarks->getMyHomeBookmarks($idSession, $block6->sortingValue);

    $comptListBookmarks = count($bookmarksList);

    if ($comptListBookmarks != "0") {
        $block6->form = "boo";
        $block6->openForm("../bookmarks/listbookmarks.php?view=my&project=$project#" . $block6->form . "Anchor");

        $block6->headingToggle($strings["bookmarks_my"]);

        $block6->openPaletteIcon();
        $block6->paletteIcon(0, "add", $strings["add"]);
        $block6->paletteIcon(1, "remove", $strings["delete"]);

        $block6->paletteIcon(5, "info", $strings["view"]);
        $block6->paletteIcon(6, "edit", $strings["edit"]);

        $block6->closePaletteIcon();

        $block6->sorting("bookmarks", $sortingUser["bookmarks"], "boo.name ASC", $sortingFields = [0 => "boo.name", 1 => "boo.category", 2 => "boo.shared"]);

        $bookmarkIds = [];

        if ($comptListBookmarks != "0") {
            $block6->openResults();

            $block6->labels($labels = [0 => $strings["name"], 1 => $strings["bookmark_category"], 2 => $strings["shared"]], "false");

            foreach ($bookmarksList as $bookmark) {
                array_push($bookmarkIds, $bookmark['boo_id']);

                $block6->openRow();
                $block6->checkboxRow($bookmark['boo_id']);
                $block6->cellRow($blockPage->buildLink("../bookmarks/viewbookmark.php?id=" . $bookmark['boo_id'], $bookmark['boo_name'], 'in') . " " . $blockPage->buildLink($bookmark['boo_url'], "(" . $strings["url"] . ")", 'out'));
                $block6->cellRow( !empty($bookmark['boo_boocat_name']) ? $bookmark['boo_boocat_name'] : Util::doubleDash());

                if ($bookmark['boo_shared'] == "1") {
                    $printShared = $strings["yes"];
                } else {
                    $printShared = $strings["no"];
                }

                $block6->cellRow($printShared);
                $block6->closeRow();
            }

            $block6->closeResults();
        } else {
            $block6->noresults();
        }

        $block6->closeToggle();
        $block6->closeFormResults();

        $block6->openPaletteScript();
        $block6->paletteScript(0, "add", "../bookmarks/editbookmark.php", "true,false,false", $strings["add"]);
        $block6->paletteScript(1, "remove", "../bookmarks/deletebookmarks.php", "false,true,true", $strings["delete"]);

        $block6->paletteScript(5, "info", "../bookmarks/viewbookmark.php", "false,true,false", $strings["view"]);
        $block6->paletteScript(6, "edit", "../bookmarks/editbookmark.php", "false,true,false", $strings["edit"]);

        $block6->closePaletteScript($comptListBookmarks, $bookmarkIds);
    }
}
// end showHomeBookmarks

/**
 * start to show projects block
 */
if ($showHomeProjects) {
    $projects = new Projects();

    $block1 = new phpCollab\Block();

    $block1->form = "wbP";
    $block1->openForm("../general/home.php#" . $block1->form . "Anchor");

    $block1->headingToggle($strings["my_projects"]);

    $block1->openPaletteIcon();

    if ($profilSession == "0" || $profilSession == "1") {
        $block1->paletteIcon(0, "add", $strings["add"]);
        $block1->paletteIcon(1, "remove", $strings["delete"]);
        $block1->paletteIcon(2, "copy", $strings["copy"]);
    }
    $block1->paletteIcon(5, "info", $strings["view"]);

    if ($profilSession == "0" || $profilSession == "1") {
        $block1->paletteIcon(6, "edit", $strings["edit"]);
    }

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        $block1->paletteIcon(8, "bug", $strings["bug"]);
    }

    $block1->closePaletteIcon();
    $block1->setLimit($blockPage->returnLimit(1));
    $block1->setRowsLimit($defaultNumRowsToDisplay);

    $block1->sorting(
        "home_projects",
        $sortingUser["home_projects"],
        "pro.name ASC",
        $sortingFields = [
        0 => "pro.id",
        1 => "pro.name",
        2 => "pro.priority",
        3 => "org.name",
        4 => "pro.status",
        5 => "mem2.login",
        6 => "pro.published"
        ]
    );

    $sorting = $block1->sortingValue;

    $projectCount = $projects->getProjectList($idSession, $_GET["typeProjects"]);
    $block1->setRecordsTotal(count($projectCount));

    $projectsList = $projects->getProjectList($idSession, $_GET["typeProjects"], $block1->getRowsLimit(), $block1->getLimit(), $sorting);


    $projectsTopics = [];

    $comptListProjects = count($projectsList);

    if ($projectsList) {
        $block1->openResults();

        $block1->labels($labels = [0 => $strings["id"], 1 => $strings["project"], 2 => $strings["priority"], 3 => $strings["organization"], 4 => $strings["status"], 5 => $strings["owner"], 6 => $strings["project_site"]], "true");

        foreach ($projectsList as $project) {
            $idStatus = $project["pro_status"];
            $idPriority = $project["pro_priority"];

            $block1->openRow();
            $block1->checkboxRow($project["pro_id"]);
            $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $project["pro_id"], $project["pro_id"], 'in'));
            $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $project["pro_id"], $project["pro_name"], 'in'));
            $block1->cellRow('<img src="../themes/' . THEME . '/images/gfx_priority/' . $idPriority . '.gif" alt=""> ' . $priority[$idPriority]);
            $block1->cellRow($project["pro_org_name"]);
            $block1->cellRow($status[$idStatus]);

            $block1->cellRow($blockPage->buildLink('../users/viewuser.php?id=' . $project["pro_mem_id"], $project["pro_mem_login"], 'in'));

            if ($sitePublish == "true") {
                if ($project["pro_published"] == "1") {
                    $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/addprojectsite.php?id=" . $project["pro_id"], $strings["create"] . "...", 'in') . "&gt;");
                } else {
                    $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/viewprojectsite.php?id=" . $project["pro_id"], $strings["details"], 'in') . "&gt;");
                }
            }

            $block1->closeRow();
            array_push($projectsTopics, $project["pro_id"]);
        }
        $block1->closeResults();
        $block1->limitsFooter("1", $blockPage->getLimitsNumber(), "", "");
    } else {
        $block1->noresults();
    }


    $block1->closeToggle();
    $block1->closeFormResults();

    $block1->openPaletteScript();
    if ($profilSession == "0" || $profilSession == "1") {
        $block1->paletteScript(0, "add", "../projects/editproject.php", "true,true,true", $strings["add"]);
        $block1->paletteScript(1, "remove", "../projects/deleteproject.php", "false,true,false", $strings["delete"]);
        $block1->paletteScript(2, "copy", "../projects/editproject.php?docopy=true", "false,true,false", $strings["copy"]);
    }

    $block1->paletteScript(5, "info", "../projects/viewproject.php", "false,true,false", $strings["view"]);

    if ($profilSession == "0" || $profilSession == "1") {
        $block1->paletteScript(6, "edit", "../projects/editproject.php", "false,true,false", $strings["edit"]);
    }

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        $block1->paletteScript(8, "bug", $pathMantis . "login.php?url=http://{$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]}&username=$loginSession&password=$passwordSession", "false,true,false", $strings["bug"]);
    }

    $block1->closePaletteScript($comptListProjects, $listProjects->tea_pro_id);
}
// end showHomeProjects

/**
 * start to show the task
 */
if ($showHomeTasks) {
    $block2 = new phpCollab\Block();

    $block2->form = "xwbT";
    $block2->openForm("../general/home.php#" . $block2->form . "Anchor");

    $block2->headingToggle($strings["my_tasks"]);

    $block2->openPaletteIcon();

    $block2->paletteIcon(0, "remove", $strings["delete"]);
    $block2->paletteIcon(1, "copy", $strings["copy"]);
    $block2->paletteIcon(3, "info", $strings["view"]);
    $block2->paletteIcon(4, "edit", $strings["edit"]);

    $block2->closePaletteIcon();
    $block2->setLimit($blockPage->returnLimit(3));
    $block2->setRowsLimit($defaultNumRowsToDisplay);

    $block2->sorting("home_tasks", $sortingUser["home_tasks"], "tas.name ASC", $sortingFields = [0 => "tas.name", 1 => "tas.priority", 2 => "tas.status", 3 => "tas.completion", 4 => "tas.due_date", 5 => "mem2.login", 6 => "tas.project", 7 => "tas.published"]);

    $tasksList = $tasks->getSubtasksAssignedToMe($idSession);

    $subtasks = null;
    if ($tasksList) {
        foreach ($tasksList as $task) {
            $subtasks .= $task['subtas_task'] . ',';
        }
        $subtasks = rtrim(rtrim($subtasks), ',');
    }

    $taskCount = 0;

    if (!empty($subtasks)) {
        $taskCount = count($tasks->getAllMyTasks($idSession, $subtasks));
    }

    $block2->setRecordsTotal($taskCount);

    $listTasks = $tasks->getAllMyTasks($idSession, $subtasks, $block2->getLimit(), $block2->getRowsLimit(), $block2->sortingValue);

    if ($listTasks) {
        $block2->openResults();

        $block2->labels($labels = [0 => $strings["name"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["completion"], 4 => $strings["due_date"], 5 => $strings["assigned_by"], 6 => $strings["project"], 7 => $strings["published"]], "true");

        foreach ($listTasks as $task) {
            if ($task["tas_due_date"] == "") {
                $task["tas_due_date"] = $strings["none"];
            }
            $idStatus = $task["tas_status"];
            $idPriority = $task["tas_priority"];
            $idPublish = $task["tas_published"];
            $complValue = ($task["tas_completion"] > 0) ? $task["tas_completion"] . "0 %" : $task["tas_completion"] . " %";

            //skip completed tasks
            //28/05/03 Florian DECKERT
            if ($idStatus == 1) {
                continue;
            }

            $block2->openRow();
            $block2->checkboxRow($task["tas_id"]);

            if ($task["tas_assigned_to"] == "0") {
                $block2->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $task["tas_id"], $task["tas_name"], 'in') . " -> " . $strings["subtask"]);
            } else {
                $block2->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $task["tas_id"], $task["tas_name"], 'in'));
            }
            $block2->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
            $block2->cellRow($status[$idStatus]);
            $block2->cellRow($complValue);

            if ($task["tas_due_date"] <= $date && $task["tas_completion"] != "10") {
                if ($task["tas_due_date"] == '--') {
                    $block2->cellRow( Util::doubleDash() );
                } else {
                    $block2->cellRow("<b>" . $task["tas_due_date"] . "</b>");
                }
            } else {
                if ($task["tas_due_date"] == '--') {
                    $block2->cellRow( Util::doubleDash() );
                } else {
                    $block2->cellRow($task["tas_due_date"]);

                }
            }

            $block2->cellRow($blockPage->buildLink($task["tas_mem2_email_work"], $task["tas_mem2_name"], 'mail'));

            $block2->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $task["tas_project"], $task["tas_pro_name"], 'in'));
            if ($sitePublish == "true") {
                $block2->cellRow($statusPublish[$idPublish]);
            }
            $block2->closeRow();
        }
        $block2->closeResults();
        $block2->limitsFooter("3", $blockPage->getLimitsNumber(), "", "");
    } else {
        $block2->noresults();
    }
    $block2->closeToggle();
    $block2->closeFormResults();

    $block2->openPaletteScript();
    $block2->paletteScript(0, "remove", "../tasks/deletetasks.php", "false,true,true", $strings["delete"]);
    $block2->paletteScript(1, "copy", "../tasks/edittask.php?docopy=true", "false,true,false", $strings["copy"]);
    $block2->paletteScript(3, "info", "../tasks/viewtask.php", "false,true,false", $strings["view"]);
    $block2->paletteScript(4, "edit", "../tasks/edittask.php", "false,true,false", $strings["edit"]);
    $block2->closePaletteScript($comptListTasks, $listTasks->tas_id);
}
// end showHomeTasks

/**
 * start to show the subtask
 */
if ($showHomeSubtasks) {
    $block3 = new phpCollab\Block();

    $block3->form = "xwbR";
    $block3->openForm("../general/home.php#" . $block3->form . "Anchor");

    $block3->headingToggle($strings["my_subtasks"]);

    $block3->sorting("home_subtasks", $sortingUser["home_subtasks"], "subtas.name ASC", $sortingFields = [0 => "subtas.name", 1 => "subtas.priority", 2 => "subtas.status", 3 => "subtas.completion", 4 => "subtas.due_date", 5 => "mem.login", 6 => "subtas.task", 7 => "subtas.published"]);

    $listSubtasks = $tasks->getSubtasksAssignedToMe($idSession);

    foreach ($listSubtasks as $subtask) {
        $subtasks .= $listSubtasks['subtas_task'] . ',';
    }
    $subtasks = rtrim(rtrim($subtasks), ',');

    if ($subtasks != "") {
        // Since $listTasks was used above, let's clear it out
        unset($listTasks);
        $listTasks = $tasks->getOpenAndCompletedSubTasksAssignedToMe($idSession, $block3->sortingValue);


        if ($listTasks) {
            $block3->openResults();

            $block3->labels($labels = [0 => $strings["name"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["completion"], 4 => $strings["due_date"], 5 => $strings["assigned_by"], 6 => $strings["task"], 7 => $strings["published"]], "true");

            foreach ($listTasks as $task) {
                if ($task['subtas_due_date'] == "") {
                    $task['subtas_due_date'] = $strings["none"];
                }
                $idStatus = $task['subtas_status'];
                $idPriority = $task['subtas_priority'];
                $idPublish = $task['subtas_published'];
                $complValue = ($task['subtas_completion'] > 0) ? $task['subtas_completion'] . "0 %" : $task['subtas_completion'] . " %";

                //skip completed tasks
                //28/05/03 Florian DECKERT
                if ($idStatus == 1) {
                    continue;
                }

                $block3->openRow();
                $block3->checkboxRow($task['subtas_id']);

                if ($task['subtas_assigned_to'] == "0") {
                    $block3->cellRow($blockPage->buildLink("../subtasks/viewsubtask.php?id=" . $task['subtas_id'] . "&task=" . $task['subtas_task'], $task['subtas_name'], 'in') . " -> " . $strings["subtask"]);
                } else {
                    $block3->cellRow($blockPage->buildLink("../subtasks/viewsubtask.php?id=" . $task['subtas_id'] . "&task=" . $task['subtas_task'], $task['subtas_name'], 'in'));
                }
                $block3->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
                $block3->cellRow($status[$idStatus]);
                $block3->cellRow($complValue);

                if ($task['subtas_due_date'] <= $date && $task['subtas_completion'] != "10") {
                    $block3->cellRow("<b>" . $task['subtas_due_date'] . "</b>");
                } else {
                    $block3->cellRow($task['subtas_due_date']);
                }

                $block3->cellRow($blockPage->buildLink($task['subtas_mem2_email_work'], $task['subtas_mem2_login'], 'mail'));
                $block3->cellRow($task['subtas_tas_name']);
                if ($sitePublish == "true") {
                    $block3->cellRow($statusPublish[$idPublish]);
                }
                $block3->closeRow();
            }
            $block3->closeResults();
        } else {
            $block3->noresults();
        }
    } else {
        $block3->noresults();
    }
    $block3->closeToggle();
    $block3->closeFormResults();
}
// end showHomeSubtasks

/**
 * start to show the discussion block
 */

if ($showHomeDiscussions) {
    $block4 = new phpCollab\Block();

    $homeTopics = new Topics();

    $block4->form = "wbTh";
    $block4->openForm("../general/home.php#" . $block4->form . "Anchor");

    $block4->headingToggle($strings["my_discussions"]);

    $block4->openPaletteIcon();

    $block4->paletteIcon(0, "add", $strings["add"]);
    $block4->paletteIcon(1, "lock", $strings["close"]);
    $block4->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
    $block4->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
    $block4->paletteIcon(4, "info", $strings["view"]);

    $block4->closePaletteIcon();

    $block4->sorting("home_discussions", $sortingUser["home_discussions"], "topic.last_post DESC", $sortingFields = [0 => "topic.subject", 1 => "mem.login", 2 => "topic.posts", 3 => "topic.last_post", 4 => "topic.status", 5 => "topic.project", 6 => "topic.published"]);

    if (count($projectsTopics) == 0) {
        $projectsTopics = "0";
    } else {
        $projectsTopics = implode(',', $projectsTopics);
    }

    $listTopics = $homeTopics->getHomeTopics($projectsTopics, $dateFilter, $block4->sortingValue);

    if ($listTopics) {
        $block4->openResults();

        $block4->labels($labels = [0 => $strings["topic"], 1 => $strings["owner"], 2 => $strings["posts"], 3 => $strings["last_post"], 4 => $strings["status"], 5 => $strings["project"], 6 => $strings["published"]], "true");

        foreach ($listTopics as $topic) {
            
            $idStatus = $topic["top_status"];
            $idPublish = $topic["top_published"];
            $block4->openRow();
            $block4->checkboxRow($topic["top_id"]);
            $block4->cellRow($blockPage->buildLink("../topics/viewtopic.php?project=" . $topic["top_project"] . "&id=" . $topic["top_id"], $topic["top_subject"], 'in'));
            $block4->cellRow($blockPage->buildLink($topic["top_mem_email_work"], $topic["top_mem_login"], 'mail'));
            $block4->cellRow($topic["top_posts"]);
            if ($topic["top_last_post"] > $_SESSION["lastvisiteSession"]) {
                $block4->cellRow("<b>" . phpCollab\Util::createDate($topic["top_last_post"], $timezoneSession) . "</b>");
            } else {
                $block4->cellRow(phpCollab\Util::createDate($topic["top_last_post"], $timezoneSession));
            }
            $block4->cellRow($statusTopic[$idStatus]);
            $block4->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $topic["top_project"], $topic["top_pro_name"], 'in'));
            if ($sitePublish == "true") {
                $block4->cellRow($statusPublish[$idPublish]);
            }
            $block4->closeRow();
        }
        $block4->closeResults();
    } else {
        $block4->noresults();
    }
    $block4->closeToggle();
    $block4->closeFormResults();

    $block4->openPaletteScript();
    $block4->paletteScript(0, "remove", "../topics/deletetopics.php", "false,true,true", $strings["delete"]);
    $block4->paletteScript(1, "lock", "../general/home.php?closeTopic=true&action=publish", "false,true,true", $strings["close"]);
    $block4->paletteScript(2, "add_projectsite", "../general/home.php?addToSiteTopic=true&action=publish", "false,true,true", $strings["add_project_site"]);
    $block4->paletteScript(3, "remove_projectsite", "../general/home.php?removeToSiteTopic=true&action=publish", "false,true,true", $strings["remove_project_site"]);
    $block4->paletteScript(4, "info", "threaddetail?", "false,true,false", $strings["view"]);
    $block4->closePaletteScript(count($listTopics), $listTopics->top_id);
}
// end showHomeDiscussions

/**
 * start to show the reports block
 */

if ($showHomeReports) {
    $reportsBlock = new phpCollab\Block();

    $reportsBlock->form = "wbSe";
    $reportsBlock->openForm("../general/home.php#" . $reportsBlock->form . "Anchor");

    $reportsBlock->headingToggle($strings["my_reports"]);

    $reportsBlock->openPaletteIcon();
    $reportsBlock->paletteIcon(0, "add", $strings["new"]);
    $reportsBlock->paletteIcon(1, "remove", $strings["delete"]);
    $reportsBlock->paletteIcon(2, "info", $strings["view"]);
    $reportsBlock->paletteIcon(3, "export", $strings["export"]);
    $reportsBlock->closePaletteIcon();

    $reportsBlock->sorting("home_reports", $sortingUser["home_reports"], "rep.name ASC", $sortingFields = [0 => "rep.name", 1 => "rep.created"]);
    $listReports = $reports->getReportsByOwner($idSession, $reportsBlock->sortingValue);

    if ($listReports) {
        $reportsBlock->openResults();

        $reportsBlock->labels($labels = [0 => $strings["name"], 1 => $strings["created"]], "false");

        foreach ($listReports as $listReport) {
            $reportsBlock->openRow();
            $reportsBlock->checkboxRow($listReport["rep_id"]);
            $reportsBlock->cellRow($blockPage->buildLink("../reports/resultsreport.php?id=" . $listReport["rep_id"], $listReport["rep_name"], 'in'));
            $reportsBlock->cellRow(phpCollab\Util::createDate($listReport["rep_created"], $timezoneSession));
            $reportsBlock->closeRow();
        }
        $reportsBlock->closeResults();
    } else {
        $reportsBlock->noresults();
    }
    $reportsBlock->closeToggle();
    $reportsBlock->closeFormResults();

    $reportsBlock->openPaletteScript();
    $reportsBlock->paletteScript(0, "add", "../reports/createreport.php", "true,true,true", $strings["new"]);
    $reportsBlock->paletteScript(1, "remove", "../reports/deletereports.php", "false,true,true", $strings["delete"]);
    $reportsBlock->paletteScript(2, "info", "../reports/resultsreport.php", "false,true,true", $strings["view"]);
    $reportsBlock->paletteScript(3, "export", "../reports/exportreport.php", "false,true,true", $strings["export"]);
    $reportsBlock->closePaletteScript(count($listReports), $listReports->rep_id);
}
// end showHomeReports

/**
 * start to show notes block
 */
if ($showHomeNotes) {
    $notesBlock = new phpCollab\Block();
    $notesBlock->form = "saJ";
    $notesBlock->openForm("../general/home.php?project=$project#" . $notesBlock->form . "Anchor");
    $notesBlock->headingToggle($strings["my_notes"]);

    $notesBlock->openPaletteIcon();

    $notesBlock->paletteIcon(5, "info", $strings["view"]);
    $notesBlock->paletteIcon(6, "edit", $strings["edit"]);
    $notesBlock->closePaletteIcon();

    $comptTopic = count($topicNote);

    if ($comptTopic != "0") {
        $notesBlock->sorting("notes", $sortingUser["notes"], "note.date DESC", $sortingFields = [0 => "note.subject", 1 => "note.topic", 2 => "note.date", 3 => "mem.login", 4 => "note.published"]);
    } else {
        $notesBlock->sorting("notes", $sortingUser["notes"], "note.date DESC", $sortingFields = [0 => "note.subject", 1 => "note.date", 2 => "mem.login", 3 => "note.published"]);
    }

    $listNotes = $notes->getMyDateFilteredNotes($idSession, $dateFilter, $notesBlock->sortingValue);

    if ($listNotes) {
        $notesBlock->openResults();

        if ($comptTopic != "0") {
            $notesBlock->labels($labels = [0 => $strings["subject"], 1 => $strings["topic"], 2 => $strings["date"], 3 => $strings["owner"], 4 => $strings["published"]], "true");
        } else {
            $notesBlock->labels($labels = [0 => $strings["subject"], 1 => $strings["date"], 2 => $strings["owner"], 3 => $strings["published"]], "true");
        }
        foreach ($listNotes as $note) {
            $idPublish = $note["note_published"];
            $notesBlock->openRow();
            $notesBlock->checkboxRow($note["note_id"]);
            $notesBlock->cellRow($blockPage->buildLink("../notes/viewnote.php?id=" . $note["note_id"], $note["note_subject"], 'in'));
            if ($comptTopic != "0") {
                $notesBlock->cellRow( !empty($topicNote[$note["note_topic"]]) ? $topicNote[$note["note_topic"]] : Util::doubleDash());
            }
            $notesBlock->cellRow($note["note_date"]);
            $notesBlock->cellRow($blockPage->buildLink($note["note_mem_email_work"], $note["note_mem_login"], 'mail'));
            if ($sitePublish == "true") {
                $notesBlock->cellRow($statusPublish[$idPublish]);
            }
            $notesBlock->closeRow();
        }
        $notesBlock->closeResults();
    } else {
        $notesBlock->noresults();
    }
    $notesBlock->closeToggle();
    $notesBlock->closeFormResults();

    $notesBlock->openPaletteScript();
    $notesBlock->paletteScript(5, "info", "../notes/viewnote.php", "false,true,false", $strings["view"]);
    $notesBlock->paletteScript(6, "edit", "../notes/editnote.php?project=$project", "false,true,false", $strings["edit"]);
    $notesBlock->closePaletteScript(count($listNotes), $listNotes->note_id);
}
// end showHomeNotes

/**
 * start to show newsdesk mod
 */
if ($showHomeNewsdesk) {
    $newsdeskBlock = new phpCollab\Block();
    $newsdeskBlock->form = "saN";
    $newsdeskBlock->openForm("../general/home.php?project=$project#" . $newsdeskBlock->form . "Anchor");
    $newsdeskBlock->headingToggle($strings["my_newsdesk"]);

    $newsdeskBlock->openPaletteIcon();
    if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
        $newsdeskBlock->paletteIcon(0, "add", $strings["add_newsdesk"]);
        $newsdeskBlock->paletteIcon(1, "edit", $strings["edit_newsdesk"]);
        $newsdeskBlock->paletteIcon(2, "remove", $strings["del_newsdesk"]);
    }

    $newsdeskBlock->paletteIcon(5, "info", $strings["view"]);

    $newsdeskBlock->closePaletteIcon();

    $newsdeskBlock->setLimit($blockPage->returnLimit(4));
    $newsdeskBlock->setRowsLimit(10); // 5
    $newsdeskBlock->sorting("newsdesk", $sortingUser["newsdesk"], "news.pdate DESC", $sortingFields = [0 => "news.title", 1 => "news.pdate", 2 => "news.author", 3 => "news.related"]);
    $newsdeskBlock->openContent();

    $idCount = count($projectsList);

    // this query select the news to show in the home page
    // this news can be: write from the user, has the RSS enabled, has the Generic Value enable, is related to the user's project
    if ($idCount > 0) {
        $relatedQuery = array_column($projectsList, 'pro_id');
    } else {
        $relatedQuery = 'g';
    }
    $newsdeskBlock->setRecordsTotal( $newsdesk->getHomePostCount($idSession, $relatedQuery) );

    $newsdeskPosts = $newsdesk->getHomeViewNewsdeskPosts(
        $idSession,
        $relatedQuery,
        $newsdeskBlock->getLimit(),
        $newsdeskBlock->getRowsLimit(),
        $newsdeskBlock->sortingValue
    );

    if ($newsdeskPosts) {
        $newsdeskBlock->openResults();
        $newsdeskBlock->labels($labels = [0 => $strings["topic"], 1 => $strings["date"], 2 => $strings["author"], 3 => $strings["newsdesk_related"]], "true");

        foreach ($newsdeskPosts as $newsdeskPost) {
            $newsAuthor = $members->getMemberById($newsdeskPost["news_author"]);

            // take the name of the related article
            if ($newsdeskPost["news_related"] != 'g') {
                $projectDetail = $projects->getProjectById($newsdeskPost["news_related"]);

                if ($projectDetail) {
                    $article_related = "<a href='../projects/viewproject.php?id=" . $projectDetail['pro_id'] . "' title='" . $projectDetail['pro_name'] . "'>" . $projectDetail['pro_name'] . "</a>";
                } else {
                    $article_related = Util::doubleDash();
                }
            } else {
                $article_related = '' . $strings["newsdesk_related_generic"];
            }

            $newsdeskBlock->openRow();
            $newsdeskBlock->checkboxRow($newsdeskPost["news_id"]);
            $newsdeskBlock->cellRow($blockPage->buildLink("../newsdesk/viewnews.php?id=" . $newsdeskPost["news_id"], $newsdeskPost["news_title"], 'in'));
            $newsdeskBlock->cellRow($newsdeskPost["news_date"]);
            $newsdeskBlock->cellRow($newsAuthor['mem_name']);
            $newsdeskBlock->cellRow($article_related);
            $newsdeskBlock->closeRow();
        }

        $newsdeskBlock->closeResults();
        $newsdeskBlock->limitsFooter("4", $blockPage->getLimitsNumber(), "", "");
    } else {
        $newsdeskBlock->noresults();
    }

    $newsdeskBlock->closeFormResults();

    $newsdeskBlock->openPaletteScript();
    if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
        $newsdeskBlock->paletteScript(0, "add", "../newsdesk/editnews.php", "true,false,false", $strings["add_newsdesk"]);
        $newsdeskBlock->paletteScript(1, "edit", "../newsdesk/editnews.php", "false,true,true", $strings["edit_newsdesk"]);
        $newsdeskBlock->paletteScript(2, "remove", "../newsdesk/editnews.php?action=remove&", "false,true,true", $strings["del_newsdesk"]);
    }

    $newsdeskBlock->paletteScript(5, "info", "../newsdesk/viewnews.php", "false,true,false", $strings["view"]);
    $newsdeskBlock->closePaletteScript(count($newsdeskPosts), $listPosts->news_id);
}
// end showHomeNewsdesk

include APP_ROOT . '/themes/' . THEME . '/footer.php';
