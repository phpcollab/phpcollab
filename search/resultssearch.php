<?php

use phpCollab\Members\Members;
use phpCollab\Notes\Notes;
use phpCollab\Organizations\Organizations;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use phpCollab\Topics\Topics;

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';

$projects = new Projects();
$tasks = new Tasks();
$members = new Members();
$teams = new Teams();
$organizations = new Organizations();
$notes = new Notes();
$topics = new Topics();

$setTitle .= " : Search Results";
$bodyCommand = "onLoad=\"document.searchForm.searchfor.focus()\"";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$searchFor = urldecode($searchFor);
$searchfor = phpCollab\Util::convertData($searchfor);
$searchfor = strtolower($searchfor);
$mots = explode(" ", $searchfor);
$number_words = count($mots);

$initrequest = $GLOBALS["initrequest"];

$heading = $_GET["heading"];

if ($heading == "ALL") {
    $validNotes = "true";
    $validOrganizations = "true";
    $validProjects = "true";
    $validTasks = "true";
    $validSubtasks = "true";
    $validTopics = "true";
    $validMembers = "true";
    $selectedAll = "selected";
}

if ($heading == "notes") {
    $validNotes = "true";
    $selectedNotes = "selected";
}
if ($heading == "organizations") {
    $validOrganizations = "true";
    $selectedOrganizations = "selected";
}
if ($heading == "projects") {
    $validProjects = "true";
    $selectedProjects = "selected";
}
if ($heading == "tasks") {
    $validTasks = "true";
    $selectedTasks = "selected";
}
if ($heading == "subtasks") {
    $validSubtasks = "true";
    $selectedSubtasks = "selected";
}
if ($heading == "discussions") {
    $validTopics = "true";
    $selectedDiscussions = "selected";
}
if ($heading == "members") {
    $validMembers = "true";
    $selectedMembers = "selected";
}

$searchProjects = "WHERE (pro.name like '%$mots[0]%'";
$z = 1;
while ($z < $number_words) {
    $searchProjects .= " OR pro.name like '%$mots[$z]%' ";
    $z++;
}
$searchProjects .= " OR pro.description like '%$mots[0]%'";
$y = 1;
while ($y < $number_words) {
    $searchProjects .= " OR pro.description like '%$mots[$y]%' ";
    $y++;
}


$searchProjects .= " OR org.name like '%$mots[0]%'";
$x = 1;
while ($x < $number_words) {
    $searchProjects .= " OR org.name like '%$mots[$x]%' ";
    $x++;
}
$searchProjects .= ")";

$searchTasks = "WHERE (tas.name like '%$mots[0]%'";
$z = 1;
while ($z < $number_words) {
    $searchTasks .= " OR tas.name like '%$mots[$z]%' ";
    $z++;
}
$searchTasks .= " OR tas.description like '%$mots[0]%'";
$y = 1;
while ($y < $number_words) {
    $searchTasks .= " OR tas.description like '%$mots[$y]%' ";
    $y++;
}
$searchTasks .= ")";

$searchSubtasks = "WHERE (subtas.name like '%$mots[0]%'";
$z = 1;
while ($z < $number_words) {
    $searchSubtasks .= " OR subtas.name like '%$mots[$z]%' ";
    $z++;
}
$searchSubtasks .= " OR subtas.description like '%$mots[0]%'";
$y = 1;
while ($y < $number_words) {
    $searchSubtasks .= " OR subtas.description like '%$mots[$y]%' ";
    $y++;
}
$searchSubtasks .= ")";
$searchMembers = "WHERE (mem.login like '%$mots[0]%'";
$z = 1;
while ($z < $number_words) {
    $searchMembers .= " OR mem.login like '%$mots[$z]%' ";
    $z++;
}
$searchMembers .= " OR mem.name like '%$mots[0]%'";
$y = 1;
while ($y < $number_words) {
    $searchMembers .= " OR mem.name like '%$mots[$y]%' ";
    $y++;
}

$searchOrganizations = "WHERE (org.name like '%$mots[0]%'";
$z = 1;
while ($z < $number_words) {
    $searchOrganizations .= " OR org.name like '%$mots[$z]%' ";
    $z++;
}
$searchOrganizations .= ")";

$searchTopics = "WHERE topic.subject like '%$mots[0]%'";
$z = 1;
while ($z < $number_words) {
    $searchTopics .= " OR topic.subject like '%$mots[$z]%' ";
    $z++;
}

$searchNotes = "WHERE note.subject like '%$mots[0]%'";
$z = 1;
while ($z < $number_words) {
    $searchNotes .= " OR note.subject like '%$mots[$z]%' ";
    $z++;
}
$searchNotes .= " OR note.description like '%$mots[0]%'";
$y = 1;
while ($y < $number_words) {
    $searchNotes .= " OR note.description like '%$mots[$y]%' ";
    $y++;
}

$blockPage = new phpCollab\Block();

$block1 = new phpCollab\Block();

$block1->setLimit($blockPage->returnLimit(1));
$block1->setRowsLimit(10);

$block1->sorting("projects",
    $sortingUser["projects"],
    "pro.name ASC",
    $sortingFields = array(
        0 => "pro.id",
        1 => "pro.name",
        2 => "pro.priority",
        3 => "org.name",
        4 => "pro.status",
        5 => "mem.login",
        6 => "pro.published"
    )
);

if ($projectsFilter == "true") {
    $projectsQuery = "LEFT OUTER JOIN " . $tableCollab["teams"] . " teams ON teams.project = pro.id ";
    $projectsQuery .= "$searchProjects AND teams.member = '$idSession'";
} else {
    $projectsQuery = "$searchProjects";
}
$comptListProjects = "0";

if ($validProjects == "true") {
    $block1->setRecordsTotal(count($projects->searchProjects($projectsQuery)));
    $listProjects = $projects->searchProjects($projectsQuery, $block1->sortingValue, $block1->getLimit(), $block1->getRowsLimit());
}

$block2 = new phpCollab\Block();

$block2->setLimit($blockPage->returnLimit(2));
$block2->setRowsLimit(10);

$block2->sorting("home_tasks", $sortingUser["home_tasks"], "tas.name ASC", $sortingFields = array(0 => "tas.name", 1 => "tas.priority", 2 => "tas.status", 3 => "tas.due_date", 4 => "mem.login", 5 => "tas.project", 6 => "tas.published"));

if ($projectsFilter == "true") {
    $projectsQuery = "LEFT OUTER JOIN " . $tableCollab["teams"] . " teams ON teams.project = pro.id ";
    $projectsQuery .= "WHERE pro.status IN(0,2,3) AND teams.member = '$idSession'";

    $listProjectsFilter = $projects->searchProjects($projectsQuery);

    $filterResults = implode(",", array_column($listProjectsFilter, "pro_id"));
}

if ($projectsFilter == "true") {
    if (count($listProjectsFilter) != "0") {
        $tasksQuery = "$searchTasks AND pro.id IN($filterResults)";
    } else {
        $validTasks = "false";
    }
} else {
    $tasksQuery = "$searchTasks";
}

if ($validTasks == "true") {
    $listTasks = $tasks->getSearchTasks($tasksQuery, $block2->sortingValue);
    $block2->setRecordsTotal(count($listTasks));

    $listTasks = $tasks->getSearchTasks($tasksQuery, $block2->sortingValue, $block2->getLimit(), $block2->getRowsLimit());
}

$block9 = new phpCollab\Block();
$block9->setLimit($blockPage->returnLimit(9));
$block9->setRowsLimit(10);
$block9->sorting(
    "home_subtasks",
    $sortingUser["home_subtasks"],
    "subtas.name ASC",
    $sortingFields = array(
        0 => "subtas.name",
        1 => "subtas.priority",
        2 => "subtas.status",
        3 => "subtas.due_date",
        4 => "mem.login",
        5 => "subtas.project",
        6 => "subtas.published"
    )
);

if ($validSubtasks == "true") {
    $block9->setRecordsTotal(count($tasks->getSearchSubTasks($searchSubtasks, $block9->sortingValue)));

    $listSubtasks = $tasks->getSearchSubTasks($searchSubtasks, $block9->sortingValue, $block9->getLimit(), $block9->getRowsLimit());
}
$block3 = new phpCollab\Block();

$block3->setLimit($blockPage->returnLimit(3));
$block3->setRowsLimit(10);

$block3->sorting(
    "users",
    $sortingUser["users"],
    "mem.name ASC",
    $sortingFields = array(
        0 => "mem.name",
        1 => "mem.login",
        2 => "mem.email_work",
        3 => "mem.phone_work",
        4 => "log.connected"
    )
);

if ($demoMode == "true") {
    $userQuery = "$searchMembers )";
} else {
    $userQuery = "$searchMembers ) AND mem.id != '2'";
}

if ($validMembers == "true") {
    $block3->setRecordsTotal(count($members->getSearchMembers($userQuery)));

    $listMembers = $members->getSearchMembers($userQuery, $block3->getLimit(), $block3->getRowsLimit());
}

$block4 = new phpCollab\Block();

$block4->setLimit($blockPage->returnLimit(4));
$block4->setRowsLimit(10);

$block4->sorting("organizations", $sortingUser["organizations"], "org.name ASC", $sortingFields = array(0 => "org.name", 1 => "org.url", 2 => "org.phone"));

if ($clientsFilter == "true" && $profilSession == "2") {
    $teamMember = "false";

    $listTeams = $teams->getTeamByMemberId($idSession);

    if (empty($listTeams)) {
        $listClients = "false";
    } else {
        $clientsOk = implode(",", array_column($listTeams, "tea_org2_id"));

        if ($clientsOk == "") {
            $listClients = "false";
        } else {
            $clientQuery = "$searchOrganizations AND org.id IN($clientsOk) AND org.id != '1'";
        }
    }
} elseif ($clientsFilter == "true" && $profilSession == "1") {
    $clientQuery = "$searchOrganizations AND org.owner = '$idSession' AND org.id != '1'";
} else {
    $clientQuery = "$searchOrganizations AND org.id != '1'";
}

if ($validOrganizations == "true" && $listClients != "false") {
    $block4->setRecordsTotal(count($organizations->getSearchOrganizations($clientQuery)));

    $listOrganizations = $organizations->getSearchOrganizations($clientQuery, $block4->sortingValue, $block4->getLimit(), $block4->getRowsLimit());
}

$block5 = new phpCollab\Block();

$block5->setLimit($blockPage->returnLimit(5));
$block5->setRowsLimit(10);

$block5->sorting("home_discussions", $sortingUser["home_discussions"], "topic.last_post DESC", $sortingFields = array(0 => "topic.subject", 1 => "mem.login", 2 => "topic.posts", 3 => "topic.last_post", 4 => "topic.status", 5 => "topic.project", 6 => "topic.published"));

if ($projectsFilter == "true") {
    if (!empty($filterResults)) {
        $topicsQuery = "$searchTopics AND topic.project IN($filterResults)";
    } else {
        $validTopics = "false";
    }
} else {
    $topicsQuery = "$searchTopics";
}

if ($validTopics == "true") {
    $block5->setRecordsTotal(count($topics->getSearchTopics($topicsQuery)));

    $listTopics = $topics->getSearchTopics($topicsQuery, $block5->sortingValue, $block5->getLimit(), $block5->getRowsLimit());
}

$block6 = new phpCollab\Block();

$comptTopic = count($topicNote);

$block6->setLimit($blockPage->returnLimit(6));
$block6->setRowsLimit(10);

if ($comptTopic != "0") {
    $block6->sorting(
        "notes",
        $sortingUser["notes"],
        "note.date DESC",
        $sortingFields = array(
            0 => "note.subject",
            1 => "note.topic",
            2 => "note.date",
            3 => "mem.login",
            4 => "note.published"
        )
    );
} else {
    $block6->sorting(
        "notes",
        $sortingUser["notes"],
        "note.date DESC",
        $sortingFields = array(
            0 => "note.subject",
            1 => "note.date",
            2 => "mem.login",
            3 => "note.published"
        )
    );
}

if ($projectsFilter == "true") {
    if (!empty($filterResults)) {
        $notesQuery = "$searchNotes AND note.project IN($filterResults)";
    } else {
        $validNotes = "false";
    }
} else {
    $notesQuery = "$searchNotes";
}

$comptListNotes = "0";
if ($validNotes == "true") {
    $block6->setRecordsTotal(count($notes->getSearchNotes($notesQuery, $block6->sortingValue)));


    $listNotes = $notes->getSearchNotes($notesQuery, $block6->sortingValue, $block6->getLimit(), $block6->getRowsLimit());

}

$comptTotal = $block1->getRecordsTotal() + $block2->getRecordsTotal() + $block3->getRecordsTotal() + $block9->getRecordsTotal() + $block4->getRecordsTotal() + $block5->getRecordsTotal() + $block6->getRecordsTotal();

$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../search/createsearch.php?", $strings["search"], "in"));
$blockPage->itemBreadcrumbs($strings["search_results"]);
$blockPage->closeBreadcrumbs();

$blockPage->setLimitsNumber(6);

$block0 = new phpCollab\Block();

$block0->openContent();
$block0->contentTitle($strings["results_for_keywords"] . " : <b>$searchfor</b>");

echo "<tr class=\"odd\"><td class=\"leftvalue\">&nbsp;</td><td>";

if ($comptTotal == "1") {
    echo "1&#160;" . $strings["match"];
}
if ($comptTotal > "1" || $comptTotal == "0") {
    echo "$comptTotal&#160;" . $strings["matches"];
}

if ($comptTotal == "0") {
    echo "<br/>" . $strings["no_results_search"];
}

echo "</td></tr>";

$block0->closeContent();

if (!empty($listProjects) && count($listProjects) > 0) {
    $block1->form = "ProjectForm";
    $block1->openForm("../search/resultssearch.php?&searchfor=$searchfor&heading=$heading#" . $block1->form . "Anchor");

    $block1->headingToggle($strings["search_results"] . " : " . $strings["projects"] . " ({$block1->getRecordsTotal()})");

    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "export", $strings["export"]);
    $block1->closePaletteIcon();

    $block1->openResults();

    $block1->labels($labels = array(0 => $strings["id"], 1 => $strings["project"], 2 => $strings["priority"], 3 => $strings["organization"], 4 => $strings["status"], 5 => $strings["owner"], 6 => $strings["published"]), "true");

    foreach ($listProjects as $listProject) {
        if ($listProject["pro_org_id"] == "1") {
            $listProject["pro_org_name"] = $strings["none"];
        }
        $idStatus = $listProject["pro_status"];
        $idPriority = $listProject["pro_priority"];
        $block1->openRow();
        $block1->checkboxRow($listProject["pro_id"]);
        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listProject["pro_id"], $listProject["pro_id"], "in"));
        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listProject["pro_id"], $listProject["pro_name"], "in"));

        $block1->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
        $block1->cellRow($listProject["pro_org_name"]);
        $block1->cellRow($status[$idStatus]);
        $block1->cellRow($blockPage->buildLink($listProject["pro_mem_email_work"], $listProject["pro_mem_login"], "mail"));
        if ($sitePublish == "true") {
            if ($listProject["pro_published"] == "1") {
                $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/addprojectsite.php?id=" . $listProject["pro_id"], $strings["create"] . "...", "in") . "&gt;");
            } else {
                $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/viewprojectsite.php?id=" . $listProject["pro_id"], $strings["details"], "in") . "&gt;");
            }
        }
        $block1->closeRow();
    }
    $block1->closeResults();

    $block1->limitsFooter("1", $blockPage->getLimitsNumber(), "", "searchfor=$searchfor&heading=$heading");

    $block1->closeToggle();
    $block1->closeFormResults();

    $block1->openPaletteScript();
    $block1->paletteScript(0, "export", "../projects/exportproject.php?languageSession={$_SESSION["languageSession"]}&type=project", "false,true,false", $strings["export"]);
    $block1->closePaletteScript(count($listProjects), array_column($listProjects, 'pro_id'));
}

if (!empty($listTasks)) {
    $block2->form = "TaskForm";
    $block2->openForm("../search/resultssearch.php?&searchfor=$searchfor&heading=$heading#" . $block2->form . "Anchor");

    $block2->headingToggle($strings["search_results"] . " : " . $strings["tasks"] . " ({$block2->getRecordsTotal()})");

    $block2->openResults();

    $block2->labels($labels = array(0 => $strings["task"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["due_date"], 4 => $strings["assigned_to"], 5 => $strings["project"], 6 => $strings["published"]), "true");

    foreach ($listTasks as $listTask) {
        if ($listTask["tas_due_date"] == "") {
            $listTask["tas_due_date"] = $strings["none"];
        }
        $idStatus = $listTask["tas_status"];
        $idPriority = $listTask["tas_priority"];
        $idPublish = $listTask["tas_published"];
        $block2->openRow();
        $block2->checkboxRow($listTask["tas_id"]);
        $block2->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $listTask["tas_id"], $listTask["tas_name"], "in"));
        $block2->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
        $block2->cellRow($status[$idStatus]);
        if ($listTask["tas_due_date"] <= $date && $listTask["tas_completion"] != "10") {
            $block2->cellRow("<b>" . $listTask["tas_due_date"] . "</b>");
        } else {
            $block2->cellRow($listTask["tas_due_date"]);
        }
        if ($listTask["tas_assigned_to"] == "0") {
            $block2->cellRow($strings["unassigned"]);
        } else {
            $block2->cellRow($blockPage->buildLink($listTask["tas_mem_email_work"], $listTask["tas_mem_login"], "mail"));
        }
        $block2->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listTask["tas_project"], $listTask["tas_pro_name"], "in"));
        if ($sitePublish == "true") {
            $block2->cellRow($statusPublish[$idPublish]);
        }
        $block2->closeRow();
    }

    $block2->closeResults();

    $block2->limitsFooter("2", $blockPage->getLimitsNumber(), "", "searchfor=$searchfor&heading=$heading");

    $block2->closeToggle();
    $block2->closeFormResults();
}

if ($listSubtasks) {
    $block9->form = "SubtaskForm";
    $block9->openForm("../search/resultssearch.php?&searchfor=$searchfor&heading=$heading#" . $block9->form . "Anchor");
    $block9->headingToggle($strings["search_results"] . " : " . $strings["subtasks"] . " ({$block9->getRecordsTotal()})");

    $block9->openResults();
    $block9->labels($labels = array(0 => $strings["subtask"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["due_date"], 4 => $strings["assigned_to"], 5 => $strings["project"], 6 => $strings["published"]), "true");

    foreach ($listSubtasks as $listSubtask) {
        if ($listSubtask["subtas_due_date"] == "") {
            $listSubtask["subtas_due_date"] = $strings["none"];
        }
        $idStatus = $listSubtask["subtas_status"];
        $idPriority = $listSubtask["subtas_priority"];
        $idPublish = $listSubtask["subtas_published"];
        $block9->openRow();
        $block9->checkboxRow($listSubtask["subtas_id"]);
        $block9->cellRow($blockPage->buildLink("../subtasks/viewsubtask.php?id=" . $listSubtask["subtas_id"] . "&task=" . $listSubtask["subtas_task"], $listSubtask["subtas_name"], "in"));
        $block9->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
        $block9->cellRow($status[$idStatus]);
        if ($listSubtask["subtas_due_date"] <= $date && $listSubtask["subtas_completion"] != "10") {
            $block9->cellRow("<b>" . $listSubtask["subtas_due_date"] . "</b>");
        } else {
            $block9->cellRow($listSubtask["subtas_due_date"]);
        }
        if ($listSubtask["subtas_assigned_to"] == "0") {
            $block9->cellRow($strings["unassigned"]);
        } else {
            $block9->cellRow($blockPage->buildLink($listSubtask["subtas_mem_email_work"], $listSubtask["subtas_mem_login"], "mail"));
        }
        $block9->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listSubtask["subtas_project"], $listSubtask["subtas_pro_name"], "in"));
        if ($sitePublish == "true") {
            $block9->cellRow($statusPublish[$idPublish]);
        }
        $block9->closeRow();
    }
    $block9->closeResults();
    $block9->limitsFooter("2", $blockPage->getLimitsNumber(), "", "searchfor=$searchfor&heading=$heading");

    $block9->closeToggle();
    $block9->closeFormResults();
}
if ($listMembers) {
    $block3->form = "UserForm";
    $block3->openForm("../search/resultssearch.php?&searchfor=$searchfor&heading=$heading#" . $block3->form . "Anchor");

    $block3->headingToggle($strings["search_results"] . " : " . $strings["users"] . " ({$block3->getRecordsTotal()})");

    $block3->openResults();

    $block3->labels($labels = array(0 => $strings["full_name"], 1 => $strings["user_name"], 2 => $strings["email"], 3 => $strings["work_phone"], 4 => $strings["connected"]), "false");

    foreach ($listMembers as $listMember) {
        $block3->openRow();
        $block3->checkboxRow($listMember["mem_id"]);
        $block3->cellRow($blockPage->buildLink("../users/viewuser.php?id=" . $listMember["mem_id"], $listMember["mem_name"], "in"));
        $block3->cellRow($listMember["mem_login"]);
        $block3->cellRow($blockPage->buildLink($listMember["mem_email_work"], $listMember["mem_email_work"], "mail"));
        $block3->cellRow($listMember["mem_phone_work"]);
        if ($listMember["mem_profil"] == "3") {
            $z = "(Client on project site)";
        } else {
            $z = "";
        }
        if ($listMember["mem_log_connected"] > $dateunix - 5 * 60) {
            $block3->cellRow($strings["yes"] . " " . $z);
        } else {
            $block3->cellRow($strings["no"]);
        }
        $block3->closeRow();
    }

    $block3->closeResults();

    $block3->limitsFooter("3", $blockPage->getLimitsNumber(), "", "searchfor=$searchfor&heading=$heading");

    $block3->closeToggle();
    $block3->closeFormResults();
}

if (!empty($listOrganizations)) {
    $block4->form = "ClientForm";
    $block4->openForm("../search/resultssearch.php?&searchfor=$searchfor&heading=$heading#" . $block4->form . "Anchor");

    $block4->headingToggle($strings["search_results"] . " : " . $strings["organizations"] . " ({$block4->getRecordsTotal()})");

    $block4->openResults();

    $block4->labels($labels = array(0 => $strings["name"], 1 => $strings["url"], 2 => $strings["phone"]), "false");

    foreach ($listOrganizations as $listOrganization) {
        $block4->openRow();
        $block4->checkboxRow($listOrganization["org_id"]);
        $block4->cellRow($blockPage->buildLink("../clients/viewclient.php?id=" . $listOrganization["org_id"], $listOrganization["org_name"], "in"));
        $block4->cellRow($blockPage->buildLink($listOrganization["org_url"], $listOrganization["org_url"], "out"));
        $block4->cellRow($listOrganization["org_phone"]);
        $block4->closeRow();
    }

    $block4->closeResults();

    $block4->limitsFooter("4", $blockPage->getLimitsNumber(), "", "searchfor=$searchfor&heading=$heading");

    $block4->closeToggle();
    $block4->closeFormResults();
}

if (!empty($listTopics)) {
    $block5->form = "ThreadTopicForm";
    $block5->openForm("../search/resultssearch.php?&searchfor=$searchfor&heading=$heading#" . $block5->form . "Anchor");

    $block5->headingToggle($strings["search_results"] . " : " . $strings["discussions"] . " ({$block5->getRecordsTotal()})");

    $block5->openResults();

    $block5->labels($labels = array(0 => $strings["topic"], 1 => $strings["owner"], 2 => $strings["posts"], 3 => $strings["latest_post"], 4 => $strings["status"], 5 => $strings["project"], 6 => $strings["published"]), "true");

    foreach ($listTopics as $listTopic) {
        $idStatus = $listTopic["top_status"];
        $idPublish = $listTopic["top_published"];
        $block5->openRow();
        $block5->checkboxRow($listTopic["top_id"]);
        $block5->cellRow($blockPage->buildLink("../topics/viewtopic.php?id=" . $listTopic["top_id"], $listTopic["top_subject"], "in"));
        $block5->cellRow($blockPage->buildLink($listTopic["top_email_work"], $listTopic["top_mem_login"], "mail"));
        $block5->cellRow($listTopic["top_posts"]);
        if ($listTopic["top_last_post"] > $_SESSION["lastvisiteSession"]) {
            $block5->cellRow("<b>" . $listTopic["top_last_post"] . "</b>");
        } else {
            $block5->cellRow($listTopic["top_last_post"]);
        }
        $block5->cellRow($statusTopic[$idStatus]);
        $block5->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listTopic["top_pro_id"], $listTopic["top_pro_name"], "in"));
        if ($sitePublish == "true") {
            $block5->cellRow($statusPublish[$idPublish]);
        }
        $block5->closeRow();
    }

    $block5->closeResults();

    $block5->limitsFooter("5", $blockPage->getLimitsNumber(), "", "searchfor=$searchfor&heading=$heading");

    $block5->closeToggle();
    $block5->closeFormResults();
}

if (!empty($listNotes)) {
    $block6->form = "notesForm";
    $block6->openForm("../search/resultssearch.php?&searchfor=$searchfor&heading=$heading#" . $block6->form . "Anchor");

    $block6->headingToggle($strings["search_results"] . " : " . $strings["notes"] . " ({$block6->getRecordsTotal()})");

    $block6->openResults();

    if ($comptTopic != "0") {
        $block6->labels($labels = array(0 => $strings["subject"], 1 => $strings["topic"], 2 => $strings["date"], 3 => $strings["owner"], 4 => $strings["published"]), "true");
    } else {
        $block6->labels($labels = array(0 => $strings["subject"], 1 => $strings["date"], 2 => $strings["owner"], 3 => $strings["published"]), "true");
    }

    foreach ($listNotes as $listNote) {
        $idPublish = $listNote["note_published"];
        $block6->openRow();
        $block6->checkboxRow($listNote["note_id"]);
        $block6->cellRow($blockPage->buildLink("../notes/viewnote.php?id=" . $listNote["note_id"], $listNote["note_subject"], "in"));
        if ($comptTopic != "0") {
            $block6->cellRow($topicNote[$listNote["note_topic"]]);
        }

        $block6->cellRow($listNote["note_date"]);
        $block6->cellRow($blockPage->buildLink($listNote["note_mem_email_work"], $listNote["note_mem_login"], "mail"));
        if ($sitePublish == "true") {
            $block6->cellRow($statusPublish[$idPublish]);
        }
        $block6->closeRow();
    }
    $block6->closeResults();

    $block6->limitsFooter("6", $blockPage->getLimitsNumber(), "", "searchfor=$searchfor&heading=$heading");

    $block6->closeToggle();
    $block6->closeFormResults();
}

$block7 = new phpCollab\Block();

$block7->form = "search";
$block7->openForm("../search/createsearch.php?action=search");

$block7->openContent();
$block7->contentTitle($strings["enter_keywords"]);

echo <<<HTML
<tr class="odd">
	<td class="leftvalue">* {$strings["search_for"]} :</td>
	<td>
		<input value="{$searchfor}" type="text" name="searchfor" style="width: 200px;"  size="30" maxlength="64" />
		<select name="heading">
				<option selected value="ALL" {$selectedAll}>{$strings["all_content"]}</option>
				<option value="notes" {$selectedNotes}>{$strings["notes"]}</option>
				<option value="organizations" {$selectedOrganizations}>{$strings["organizations"]}</option>
				<option value="projects" {$selectedProjects}>{$strings["projects"]}</option>
				<option value="tasks" {$selectedTasks}>{$strings["tasks"]}</option>
				<option value="subtasks" {$selectedSubtasks}>{$strings["subtasks"]}</option>
				<option value="discussions" {$selectedDiscussions}>{$strings["discussions"]}</option>
				<option value="members" {$selectedMembers}>{$strings["users"]}</option>
		</select>
	</td>
</tr>
<tr class="odd">
	<td class="leftvalue">&nbsp;</td>
	<td><input type="submit" name="Save" value="{$strings["search"]}" /></td>
</tr>
HTML;

$block7->closeContent();
$block7->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
