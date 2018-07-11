<?php
/*
** Application name: phpCollab
** Last Edit page: 12/01/2005
** Path by root: ../general/home.php
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
** FILE: home.php
**
** DESC: Screen: user home page
**
** HISTORY:
**	2003-09-01	-	added newsdesk block
** 	2003-10-23	-	added new document info
**	12/01/2005	-	added show/hide block from settings
**	26/09/2006	-	added subtask block
** -----------------------------------------------------------------------------
** TO-DO:
**	add option to customize block
**	save block info in a db
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';

$setTitle .= " : Home Page";

$topics = new \phpCollab\Topics\Topics();
$members = new \phpCollab\Members\Members();
$projects = new \phpCollab\Projects\Projects();
$tasks = new \phpCollab\Tasks\Tasks();

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

    if ($addToSiteTopic == "true") {
        $multi = strstr($id, "**");

        if ($multi != "") {
            $id = str_replace("**", ",", $id);
            $topics->publishTopic($id);
        } else {
            $topics->publishTopic($id);
        }

        $msg = 'addToSite';
    }

    if ($removeToSiteTopic == "true") {
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

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
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
    $bookmarks = new \phpCollab\Bookmarks\Bookmarks();

    $block6 = new phpCollab\Block();

    $block6->sorting("bookmarks", $sortingUser["bookmarks"], "boo.name ASC", $sortingFields = array(0 => "boo.name", 1 => "boo.category", 2 => "boo.shared"));

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

        $block6->sorting("bookmarks", $sortingUser["bookmarks"], "boo.name ASC", $sortingFields = array(0 => "boo.name", 1 => "boo.category", 2 => "boo.shared"));

        $bookmarkIds = [];

        if ($comptListBookmarks != "0") {
            $block6->openResults();

            $block6->labels($labels = array(0 => $strings["name"], 1 => $strings["bookmark_category"], 2 => $strings["shared"]), "false");

            foreach ($bookmarksList as $bookmark) {
                array_push($bookmarkIds, $bookmark['boo_id']);

                $block6->openRow();
                $block6->checkboxRow($bookmark['boo_id']);
                $block6->cellRow($blockPage->buildLink("../bookmarks/viewbookmark.php?view=$view&id=" . $bookmark['boo_id'], $bookmark['boo_name'], 'in') . " " . $blockPage->buildLink($bookmark['boo_url'], "(" . $strings["url"] . ")", 'out'));
                $block6->cellRow($bookmark['boo_boocat_name']);

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

/**
 * start to show projects block
 */
if ($showHomeProjects) {
    $projects = new \phpCollab\Projects\Projects();

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

    if ($enable_cvs == "true") {
        $block1->paletteIcon(7, "cvs", $strings["browse_cvs"]);
    }

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        $block1->paletteIcon(8, "bug", $strings["bug"]);
    }

    $block1->closePaletteIcon();

    $block1->sorting(
        "home_projects",
        $sortingUser["home_projects"],
        "pro.name ASC",
        $sortingFields = array(
        0 => "pro.id",
        1 => "pro.name",
        2 => "pro.priority",
        3 => "org.name",
        4 => "pro.status",
        5 => "mem2.login",
        6 => "pro.published"
    )
    );

    $sorting = $block1->sortingValue;

    $dataSet = $projects->getProjectList($idSession, $typeProjects, $sorting);
    $projectsTopics = array();

    $comptListProjects = count($dataSet);

    if ($dataSet) {
        $block1->openResults();

        $block1->labels($labels = array(0 => $strings["id"], 1 => $strings["project"], 2 => $strings["priority"], 3 => $strings["organization"], 4 => $strings["status"], 5 => $strings["owner"], 6 => $strings["project_site"]), "true");

        foreach ($dataSet as $data) {
            $idStatus = $data["pro_status"];
            $idPriority = $data["pro_priority"];

            $block1->openRow();
            $block1->checkboxRow($data["pro_id"]);
            $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $data["pro_id"], $data["pro_id"], in));
            $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $data["pro_id"], $data["pro_name"], in));
            $block1->cellRow('<img src="../themes/' . THEME . '/images/gfx_priority/' . $idPriority . '.gif" alt=""> ' . $priority[$idPriority]);
            $block1->cellRow($data["pro_org_name"]);
            $block1->cellRow($status[$idStatus]);

            $block1->cellRow($blockPage->buildLink('../users/viewuser.php?id=' . $data["pro_mem_id"], $data["pro_mem_login"], in));

            if ($sitePublish == "true") {
                if ($data["pro_published"] == "1") {
                    $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/addprojectsite.php?id=" . $data["pro_id"], $strings["create"] . "...", in) . "&gt;");
                } else {
                    $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/viewprojectsite.php?id=" . $data["pro_id"], $strings["details"], in) . "&gt;");
                }
            }

            $block1->closeRow();
            array_push($projectsTopics, $data["pro_id"]);
        }
        $block1->closeResults();
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

    if ($enable_cvs == "true") {
        $block1->paletteScript(7, "cvs", "../browsecvs/browsecvs.php", "false,true,false", $strings["browse_cvs"]);
    }
    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        $block1->paletteScript(8, "bug", $pathMantis . "login.php?url=http://{$HTTP_HOST}{$REQUEST_URI}&username=$loginSession&password=$passwordSession", "false,true,false", $strings["bug"]);
    }

    $block1->closePaletteScript($comptListProjects, $listProjects->tea_pro_id);
}

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

    $block2->sorting("home_tasks", $sortingUser["home_tasks"], "tas.name ASC", $sortingFields = array(0 => "tas.name", 1 => "tas.priority", 2 => "tas.status", 3 => "tas.completion", 4 => "tas.due_date", 5 => "mem.login", 6 => "tas.project", 7 => "tas.published"));

    $tasksList = $tasks->getSubtasksAssignedToMe($idSession);

    $subtasks = '';
    if ($tasksList) {
        foreach ($tasksList as $task) {
            $subtasks .= $task['subtas_task'] . ',';
        }
        $subtasks = rtrim(rtrim($subtasks), ',');
    }

    if ($subtasks != "") {
        $tmpquery = "WHERE (tas.assigned_to = '$idSession' AND tas.status IN(0,2,3) AND pro.status IN(0,2,3)) OR tas.id IN($subtasks) ORDER BY $block2->sortingValue";
    } else {
        $tmpquery = "WHERE tas.assigned_to = '$idSession' AND tas.status IN(0,2,3) AND pro.status IN(0,2,3) ORDER BY $block2->sortingValue";
    }

    $listTasks = new phpCollab\Request();
    $listTasks->openTasks($tmpquery);
    $comptListTasks = count($listTasks->tas_id);

    if ($comptListTasks != "0") {
        $block2->openResults();

        $block2->labels($labels = array(0 => $strings["name"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["completion"], 4 => $strings["due_date"], 5 => $strings["assigned_by"], 6 => $strings["project"], 7 => $strings["published"]), "true");

        for ($i = 0; $i < $comptListTasks; $i++) {
            if ($listTasks->tas_due_date[$i] == "") {
                $listTasks->tas_due_date[$i] = $strings["none"];
            }
            $idStatus = $listTasks->tas_status[$i];
            $idPriority = $listTasks->tas_priority[$i];
            $idPublish = $listTasks->tas_published[$i];
            $complValue = ($listTasks->tas_completion[$i] > 0) ? $listTasks->tas_completion[$i] . "0 %" : $listTasks->tas_completion[$i] . " %";

            //skip completed tasks
            //28/05/03 Florian DECKERT
            if ($idStatus == 1) {
                continue;
            }

            $block2->openRow();
            $block2->checkboxRow($listTasks->tas_id[$i]);

            if ($listTasks->tas_assigned_to[$i] == "0") {
                $block2->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $listTasks->tas_id[$i], $listTasks->tas_name[$i], in) . " -> " . $strings["subtask"]);
            } else {
                $block2->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $listTasks->tas_id[$i], $listTasks->tas_name[$i], in));
            }
            $block2->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
            $block2->cellRow($status[$idStatus]);
            $block2->cellRow($complValue);

            if ($listTasks->tas_due_date[$i] <= $date && $listTasks->tas_completion[$i] != "10") {
                $block2->cellRow("<b>" . $listTasks->tas_due_date[$i] . "</b>");
            } else {
                $block2->cellRow($listTasks->tas_due_date[$i]);
            }

            $block2->cellRow($blockPage->buildLink($listTasks->tas_mem2_email_work[$i], $listTasks->tas_mem2_login[$i], mail));

            $block2->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listTasks->tas_project[$i], $listTasks->tas_pro_name[$i], in));
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
    $block2->paletteScript(0, "remove", "../tasks/deletetasks.php", "false,true,true", $strings["delete"]);
    $block2->paletteScript(1, "copy", "../tasks/edittask.php?docopy=true", "false,true,false", $strings["copy"]);
    $block2->paletteScript(3, "info", "../tasks/viewtask.php", "false,true,false", $strings["view"]);
    $block2->paletteScript(4, "edit", "../tasks/edittask.php", "false,true,false", $strings["edit"]);
    $block2->closePaletteScript($comptListTasks, $listTasks->tas_id);
}

/**
 * start to show the subtask
 */
if ($showHomeSubtasks) {
    $block3 = new phpCollab\Block();

    $block3->form = "xwbR";
    $block3->openForm("../general/home.php#" . $block3->form . "Anchor");

    $block3->headingToggle($strings["my_subtasks"]);

    $block3->sorting("home_subtasks", $sortingUser["home_subtasks"], "subtas.name ASC", $sortingFields = array(0 => "subtas.name", 1 => "subtas.priority", 2 => "subtas.status", 3 => "subtas.completion", 4 => "subtas.due_date", 5 => "mem.login", 6 => "subtas.task", 7 => "subtas.published"));

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

            $block3->labels($labels = array(0 => $strings["name"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["completion"], 4 => $strings["due_date"], 5 => $strings["assigned_by"], 6 => $strings["task"], 7 => $strings["published"]), "true");

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
                    $block3->cellRow($blockPage->buildLink("../subtasks/viewsubtask.php?id=" . $task['subtas_id'] . "&task=" . $task['subtas_task'], $task['subtas_name'], in) . " -> " . $strings["subtask"]);
                } else {
                    $block3->cellRow($blockPage->buildLink("../subtasks/viewsubtask.php?id=" . $task['subtas_id'] . "&task=" . $task['subtas_task'], $task['subtas_name'], in));
                }
                $block3->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
                $block3->cellRow($status[$idStatus]);
                $block3->cellRow($complValue);

                if ($task['subtas_due_date'] <= $date && $task['subtas_completion'] != "10") {
                    $block3->cellRow("<b>" . $task['subtas_due_date'] . "</b>");
                } else {
                    $block3->cellRow($task['subtas_due_date']);
                }

                $block3->cellRow($blockPage->buildLink($task['subtas_mem2_email_work'], $task['subtas_mem2_login'], mail));
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

/**
 * start to show the discussion block
 */

if ($showHomeDiscussions) {
    $block4 = new phpCollab\Block();

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

    $block4->sorting("home_discussions", $sortingUser["home_discussions"], "topic.last_post DESC", $sortingFields = array(0 => "topic.subject", 1 => "mem.login", 2 => "topic.posts", 3 => "topic.last_post", 4 => "topic.status", 5 => "topic.project", 6 => "topic.published"));

    if (count($projectsTopics) == 0) {
        $projectsTopics = "0";
    } else {
        $projectsTopics = implode(',', $projectsTopics);
    }

    // Todo: Refactore to use PDO
    $tmpquery = "WHERE topic.project IN($projectsTopics) AND topic.last_post > '$dateFilter' AND topic.status = '1' ORDER BY $block4->sortingValue";

    $listTopics = new phpCollab\Request();
    $listTopics->openTopics($tmpquery);
    $comptListTopics = count($listTopics->top_id);

    if ($comptListTopics != "0") {
        $block4->openResults();

        $block4->labels($labels = array(0 => $strings["topic"], 1 => $strings["owner"], 2 => $strings["posts"], 3 => $strings["last_post"], 4 => $strings["status"], 5 => $strings["project"], 6 => $strings["published"]), "true");

        for ($i = 0; $i < $comptListTopics; $i++) {
            $idStatus = $listTopics->top_status[$i];
            $idPublish = $listTopics->top_published[$i];
            $block4->openRow();
            $block4->checkboxRow($listTopics->top_id[$i]);
            $block4->cellRow($blockPage->buildLink("../topics/viewtopic.php?project=" . $listTopics->top_project[$i] . "&id=" . $listTopics->top_id[$i], $listTopics->top_subject[$i], in));
            $block4->cellRow($blockPage->buildLink($listTopics->top_mem_email_work[$i], $listTopics->top_mem_login[$i], mail));
            $block4->cellRow($listTopics->top_posts[$i]);
            if ($listTopics->top_last_post[$i] > $lastvisiteSession) {
                $block4->cellRow("<b>" . phpCollab\Util::createDate($listTopics->top_last_post[$i], $timezoneSession) . "</b>");
            } else {
                $block4->cellRow(phpCollab\Util::createDate($listTopics->top_last_post[$i], $timezoneSession));
            }
            $block4->cellRow($statusTopic[$idStatus]);
            $block4->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listTopics->top_project[$i], $listTopics->top_pro_name[$i], in));
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
    $block4->closePaletteScript($comptListTopics, $listTopics->top_id);
}

/**
 * start to show the reports block
 */

if ($showHomeReports) {
    $block5 = new phpCollab\Block();

    $block5->form = "wbSe";
    $block5->openForm("../general/home.php#" . $block5->form . "Anchor");

    $block5->headingToggle($strings["my_reports"]);

    $block5->openPaletteIcon();
    $block5->paletteIcon(0, "add", $strings["new"]);
    $block5->paletteIcon(1, "remove", $strings["delete"]);
    $block5->paletteIcon(2, "info", $strings["view"]);
    $block5->paletteIcon(3, "export", $strings["export"]);
    $block5->closePaletteIcon();

    $block5->sorting("home_reports", $sortingUser["home_reports"], "rep.name ASC", $sortingFields = array(0 => "rep.name", 1 => "rep.created"));

    // Todo: Refactore to use PDO
    $tmpquery = "WHERE rep.owner = '$idSession' ORDER BY $block5->sortingValue";
    $listReports = new phpCollab\Request();
    $listReports->openReports($tmpquery);
    $comptListReports = count($listReports->rep_id);

    if ($comptListReports != "0") {
        $block5->openResults();

        $block5->labels($labels = array(0 => $strings["name"], 1 => $strings["created"]), "false");

        for ($i = 0; $i < $comptListReports; $i++) {
            $block5->openRow();
            $block5->checkboxRow($listReports->rep_id[$i]);
            $block5->cellRow($blockPage->buildLink("../reports/resultsreport.php?id=" . $listReports->rep_id[$i], $listReports->rep_name[$i], in));
            $block5->cellRow(phpCollab\Util::createDate($listReports->rep_created[$i], $timezoneSession));
            $block5->closeRow();
        }
        $block5->closeResults();
    } else {
        $block5->noresults();
    }
    $block5->closeToggle();
    $block5->closeFormResults();

    $block5->openPaletteScript();
    $block5->paletteScript(0, "add", "../reports/createreport.php", "true,true,true", $strings["new"]);
    $block5->paletteScript(1, "remove", "../reports/deletereports.php", "false,true,true", $strings["delete"]);
    $block5->paletteScript(2, "info", "../reports/resultsreport.php", "false,true,true", $strings["view"]);
    $block5->paletteScript(3, "export", "../reports/exportreport.php", "false,true,true", $strings["export"]);
    $block5->closePaletteScript($comptListReports, $listReports->rep_id);
}

/**
 * start to show notes block
 */
if ($showHomeNotes) {
    $block6 = new phpCollab\Block();
    $block6->form = "saJ";
    $block6->openForm("../general/home.php?project=$project#" . $block6->form . "Anchor");
    $block6->headingToggle($strings["my_notes"]);

    $block6->openPaletteIcon();

    $block6->paletteIcon(5, "info", $strings["view"]);
    $block6->paletteIcon(6, "edit", $strings["edit"]);
    $block6->closePaletteIcon();

    $comptTopic = count($topicNote);

    if ($comptTopic != "0") {
        $block6->sorting("notes", $sortingUser["notes"], "note.date DESC", $sortingFields = array(0 => "note.subject", 1 => "note.topic", 2 => "note.date", 3 => "mem.login", 4 => "note.published"));
    } else {
        $block6->sorting("notes", $sortingUser["notes"], "note.date DESC", $sortingFields = array(0 => "note.subject", 1 => "note.date", 2 => "mem.login", 3 => "note.published"));
    }

    // Todo: Refactore to use PDO
    $tmpquery = "WHERE note.owner = '$idSession' AND note.date > '$dateFilter' AND pro.status IN(0,2,3) ORDER BY $block6->sortingValue";
    $listNotes = new phpCollab\Request();
    $listNotes->openNotes($tmpquery);
    $comptListNotes = count($listNotes->note_id);

    if ($comptListNotes != "0") {
        $block6->openResults();

        if ($comptTopic != "0") {
            $block6->labels($labels = array(0 => $strings["subject"], 1 => $strings["topic"], 2 => $strings["date"], 3 => $strings["owner"], 4 => $strings["published"]), "true");
        } else {
            $block6->labels($labels = array(0 => $strings["subject"], 1 => $strings["date"], 2 => $strings["owner"], 3 => $strings["published"]), "true");
        }
        for ($i = 0; $i < $comptListNotes; $i++) {
            $idPublish = $listNotes->note_published[$i];
            $block6->openRow();
            $block6->checkboxRow($listNotes->note_id[$i]);
            $block6->cellRow($blockPage->buildLink("../notes/viewnote.php?id=" . $listNotes->note_id[$i], $listNotes->note_subject[$i], in));
            if ($comptTopic != "0") {
                $block6->cellRow($topicNote[$listNotes->note_topic[$i]]);
            }
            $block6->cellRow($listNotes->note_date[$i]);
            $block6->cellRow($blockPage->buildLink($listNotes->note_mem_email_work[$i], $listNotes->note_mem_login[$i], mail));
            if ($sitePublish == "true") {
                $block6->cellRow($statusPublish[$idPublish]);
            }
            $block6->closeRow();
        }
        $block6->closeResults();
    } else {
        $block6->noresults();
    }
    $block6->closeToggle();
    $block6->closeFormResults();

    $block6->openPaletteScript();
    $block6->paletteScript(5, "info", "../notes/viewnote.php", "false,true,false", $strings["view"]);
    $block6->paletteScript(6, "edit", "../notes/editnote.php?project=$project", "false,true,false", $strings["edit"]);
    $block6->closePaletteScript($comptListNotes, $listNotes->note_id);
}

/**
 * start to show newsdesk mod
 */
if ($showHomeNewsdesk) {
    $block7 = new phpCollab\Block();
    $block7->form = "saN";
    $block7->openForm("../general/home.php?project=$project#" . $block7->form . "Anchor");
    $block7->headingToggle($strings["my_newsdesk"]);

    $block7->openPaletteIcon();
    if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
        $block7->paletteIcon(0, "add", $strings["add_newsdesk"]);
        $block7->paletteIcon(1, "edit", $strings["edit_newsdesk"]);
        $block7->paletteIcon(2, "remove", $strings["del_newsdesk"]);
    }

    $block7->paletteIcon(5, "info", $strings["view"]);

    $block7->closePaletteIcon();

    $block7->setLimit($blockPage->returnLimit(1));
    $block7->setRowsLimit(40);

    $block7->sorting("newsdesk", $sortingUser["newsdesk"], "news.pdate DESC", $sortingFields = array(0 => "news.title", 1 => "news.pdate", 2 => "news.author", 3 => "news.related"));

    $block7->openContent();

    // this query select the news to show in the home page
    // this news can be: write from the user, has the RSS enabled, has the Generic Value enable, is related to the user's project

    $idCount = count($listProjects->tea_pro_id);
    if ($idCount > 0) {
        $relatedQuery = " IN (" . implode(',', $listProjects->tea_pro_id) . " , 'g') ";
    }

    $relatedQuery = " = 'g' ";

    // Todo: Refactore to use PDO
    $tmpquery = "WHERE news.author = '" . phpCollab\Util::fixInt($sessionId) . "' OR news.rss = '1' OR news.related " . $relatedQuery . " ORDER BY $block7->sortingValue ";
    $block7->setRecordsTotal(phpCollab\Util::computeTotal($initrequest["newsdeskposts"] . " " . $tmpquery));

    $listPosts = new phpCollab\Request();
    $listPosts->openNewsDesk($tmpquery, $block7->getLimit(), $block7->getRowsLimit());
    $comptPosts = count($listPosts->news_id);

    if ($comptPosts != "0") {
        $block7->openResults();
        $block7->labels($labels = array(0 => $strings["topic"], 1 => $strings["date"], 2 => $strings["author"], 3 => $strings["newsdesk_related"]), "true");

        $blockPage->limitNumber = "1";

        for ($i = 0; $i < $comptPosts; $i++) {
            $newsAuthor = $members->getMemberById($listPosts->news_author[$i]);

            // take the name of the related article
            if ($listPosts->news_related[$i] != 'g') {
                $projectDetail = $projects->getProjectById($listPosts->news_related[$i]);
                $article_related = "<a href='../projects/viewproject.php?id=" . $projectDetail['pro_id'] . "' title='" . $projectDetail['pro_name'] . "'>" . $projectDetail['pro_name'] . "</a>";
            } else {
                $article_related = '' . $strings["newsdesk_related_generic"];
            }

            $block7->openRow();
            $block7->checkboxRow($listPosts->news_id[$i]);
            $block7->cellRow($blockPage->buildLink("../newsdesk/viewnews.php?id=" . $listPosts->news_id[$i], $listPosts->news_title[$i], in));
            $block7->cellRow($listPosts->news_date[$i]);
            $block7->cellRow($newsAuthor['mem_name']);
            $block7->cellRow($article_related);
            $block7->closeRow();
        }

        $block7->closeResults();
        $block7->limitsFooter("1", $blockPage->limitNumber, "", "");
    } else {
        $block7->noresults();
    }

    $block7->closeFormResults();

    $block7->openPaletteScript();
    if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
        $block7->paletteScript(0, "add", "../newsdesk/editnews.php", "true,false,false", $strings["add_newsdesk"]);
        $block7->paletteScript(1, "edit", "../newsdesk/editnews.php", "false,true,true", $strings["edit_newsdesk"]);
        $block7->paletteScript(2, "remove", "../newsdesk/editnews.php?action=remove&", "false,true,true", $strings["del_newsdesk"]);
    }

    $block7->paletteScript(5, "info", "../newsdesk/viewnews.php", "false,true,false", $strings["view"]);
    $block7->closePaletteScript($comptPosts, $listPosts->news_id);
}

include '../themes/' . THEME . '/footer.php';
