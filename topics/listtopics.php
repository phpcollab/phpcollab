<?php

use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;
use phpCollab\Topics\Topics;

$checkSession = "true";
include_once '../includes/library.php';

$project = $request->query->get('project');
$id = $request->query->get('id');
$action = $request->query->get('action');
$pub = $request->query->get('pub');
$closeTopic = $request->query->get('closeTopic');
$addToSite = $request->query->get('addToSite');
$removeToSite = $request->query->get('removeToSite');

$strings = $GLOBALS["strings"];

$idSession = $_SESSION["idSession"];


$topics = new Topics();
$projects = new Projects();
$teams = new Teams();

if ($action == "publish") {
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
    }

    if ($addToSite == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $topics->publishTopic($id);
        $msg = "addToSite";
    }

    if ($removeToSite == "true") {
        $multi = strstr($id, "**");
        if ($multi != "") {
            $id = str_replace("**", ",", $id);
        }
        $topics->unPublishTopic($id);
        $msg = "removeToSite";
    }
}

$setTitle .= " : " . $strings["discussions"];

include APP_ROOT . '/themes/' . THEME . '/header.php';

$projectDetail = $projects->getProjectById($project);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();

$teamMember = "false";
if ($project != "") {
    $teamMember = $teams->isTeamMember($project, $idSession);

    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
    $blockPage->itemBreadcrumbs($strings["discussions"]);
} else {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php?", $strings["home"], "in"));
    $blockPage->itemBreadcrumbs($strings["my_discussions"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "saH";
$block1->openForm("../topics/listtopics.php?project=$project#" . $block1->form . "Anchor");

if ($project != "") {
    $block1->heading($strings["discussions"]);
} else {
    $block1->heading($strings["my_discussions"]);
}

$block1->openPaletteIcon();

if ($teamMember == "true") {
    $block1->paletteIcon(0, "add", $strings["add"]);
}
if ($idSession == $projectDetail["pro_owner"]) {
    $block1->paletteIcon(1, "remove", $strings["delete"]);
    $block1->paletteIcon(2, "lock", $strings["close"]);
    if ($sitePublish == "true") {
        $block1->paletteIcon(3, "add_projectsite", $strings["add_project_site"]);
        $block1->paletteIcon(4, "remove_projectsite", $strings["remove_project_site"]);
    }
}
$block1->paletteIcon(5, "info", $strings["view"]);
$block1->closePaletteIcon();

$block1->sorting("discussions", $sortingUser["discussions"], "topic.last_post DESC", $sortingFields = [0 => "topic.subject", 1 => "mem.login", 2 => "topic.posts", 3 => "topic.last_post", 4 => "topic.status", 5 => "topic.published"]);

if ($project != "") {
    $listTopics = $topics->getTopicsByProjectId($project, $block1->sortingValue);
} else {
    $listTopics = $topics->getTopicsByTopicOwner($idSession, $block1->sortingValue);
}

if ($listTopics) {
    $block1->openResults();

    $block1->labels($labels = [0 => $strings["topic"], 1 => $strings["owner"], 2 => $strings["posts"], 3 => $strings["last_post"], 4 => $strings["status"], 5 => $strings["published"]], "true");


    foreach ($listTopics as $topic) {
        $idStatus = $topic["top_status"];
        $idPublish = $topic["top_published"];
        $block1->openRow();
        $block1->checkboxRow($topic["top_id"]);
        $block1->cellRow($blockPage->buildLink("../topics/viewtopic.php?id=" . $topic["top_id"], $topic["top_subject"], "in"));
        $block1->cellRow($blockPage->buildLink($topic["top_mem_email_work"], $topic["top_mem_login"], "mail"));
        $block1->cellRow($topic["top_posts"]);
        if ($topic["top_last_post"] > $GLOBALS["lastvisiteSession"]) {
            $block1->cellRow("<b>" . phpCollab\Util::createDate($topic["top_last_post"], $_SESSION["timezoneSession"]) . "</b>");
        } else {
            $block1->cellRow(phpCollab\Util::createDate($topic["top_last_post"], $_SESSION["timezoneSession"]));
        }
        $block1->cellRow($statusTopic[$idStatus]);
        if ($sitePublish == "true") {
            $block1->cellRow($statusPublish[$idPublish]);
        }
        $block1->closeRow();
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
if ($teamMember == "true") {
    $block1->paletteScript(0, "add", "../topics/addtopic.php?project=" . $projectDetail["pro_id"] . "", "true,true,true", $strings["add"]);
}
if ($idSession == $projectDetail["pro_owner"]) {
    $block1->paletteScript(1, "remove", "../topics/deletetopics.php?project=" . $projectDetail["pro_id"] . "", "false,true,true", $strings["delete"]);
    $block1->paletteScript(2, "lock", "../topics/listtopics.php?closeTopic=true&project=$project&action=publish", "false,true,true", $strings["close"]);
    if ($sitePublish == "true") {
        $block1->paletteScript(3, "add_projectsite", "../topics/listtopics.php?addToSite=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["add_project_site"]);
        $block1->paletteScript(4, "remove_projectsite", "../topics/listtopics.php?removeToSite=true&project=" . $projectDetail["pro_id"] . "&action=publish", "false,true,true", $strings["remove_project_site"]);
    }
}
$block1->paletteScript(5, "info", "../topics/viewtopic.php?", "false,true,false", $strings["view"]);
$block1->closePaletteScript(count($listTopics), array_column($listTopics, 'top_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
