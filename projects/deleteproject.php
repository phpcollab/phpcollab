<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/deleteproject.php

$checkSession = "true";
include_once('../includes/library.php');

if ($enable_cvs == "true") {
include("../includes/cvslib.php");
}

$id = str_replace("**",",",$id);
$tmpquery = "WHERE pro.id IN($id) ORDER BY pro.name";
$listProjects = new request();
$listProjects->openProjects($tmpquery);
$comptListProjects = count($listProjects->pro_id);

if ($comptListProjects == "0") {
    Util::headerFunction("../projects/listprojects.php?msg=blankProject&".session_name()."=".session_id());
    exit;
}
if ($idSession != $listProjects->pro_owner[0] && $profilSession != "5") {
    Util::headerFunction("../projects/listprojects.php?msg=projectOwner&".session_name()."=".session_id());
    exit;
}

if ($action == "delete") {
    $id = str_replace("**",",",$id);
    $tmpquery1 = "DELETE FROM ".$tableCollab["projects"]." WHERE id IN($id)";
    $pieces = explode(",",$id);
    $comptPro = count($pieces);
    for ($i=0;$i<$comptPro;$i++) {
        if ($fileManagement == "true") {
            Util::deleteDirectory("../files/$pieces[$i]");
        }
        if ($sitePublish == "true") {
            Util::deleteDirectory("project_sites/$pieces[$i]");
        }

//if CVS repository enabled
        if ($enable_cvs == "true") {
            cvs_delete_repository($pieces[$i]);
        }
    }

    $tmpquery = "WHERE tas.project IN($id)";
    $listTasks = new request();
    $listTasks->openTasks($tmpquery);
    $comptListTasks = count($listTasks->tas_id);
    for ($i=0;$i<$comptListTasks;$i++) {
        if ($fileManagement == "true") {
            Util::deleteDirectory("../files/$id/".$listTasks->tas_id[$i]);
        }
        $tasks .= $listTasks->tas_id[$i];
        if ($i != $comptListTasks-1) {
            $tasks .= ",";
        }
    }

    $tmpquery = "WHERE topic.project IN($id)";
    $listTopics = new request();
    $listTopics->openTopics($tmpquery);
    $comptListTopics = count($listTopics->top_id);
    for ($i=0;$i<$comptListTopics;$i++) {
        $topics .= $listTopics->top_id[$i];
        if ($i != $comptListTopics-1) {
            $topics .= ",";
        }
    }

    $tmpquery2 = "DELETE FROM ".$tableCollab["tasks"]." WHERE project IN($id)";
    $tmpquery3 = "DELETE FROM ".$tableCollab["teams"]." WHERE project IN($id)";
    $tmpquery4 = "DELETE FROM ".$tableCollab["topics"]." WHERE project IN($id)";
    $tmpquery5 = "DELETE FROM ".$tableCollab["files"]." WHERE project IN($id)";
    $tmpquery6 = "DELETE FROM ".$tableCollab["assignments"]." WHERE task IN($tasks)";
    $tmpquery7 = "DELETE FROM ".$tableCollab["posts"]." WHERE topic IN($topics)";
    $tmpquery8 = "DELETE FROM ".$tableCollab["notes"]." WHERE project IN($id)";
    $tmpquery9 = "DELETE FROM ".$tableCollab["support_requests"]." WHERE project IN($id)";
    $tmpquery10 = "DELETE FROM ".$tableCollab["support_posts"]." WHERE project IN($id)";
    $tmpquery11 = "DELETE FROM ".$tableCollab["phases"]." WHERE project_id IN($id)";
    $tmpquery12 = "DELETE FROM ".$tableCollab["subtasks"]." WHERE task IN($tasks)";
    
    Util::connectSql($tmpquery1);
    Util::connectSql($tmpquery2);
    Util::connectSql($tmpquery3);
    Util::connectSql($tmpquery4);
    Util::connectSql($tmpquery5);
if ($tasks != "") {
    Util::connectSql($tmpquery6);
    Util::connectSql($tmpquery12);
}
if ($topics != "") {
    Util::connectSql($tmpquery7);
}
    Util::connectSql($tmpquery8);
    Util::connectSql($tmpquery9);
    Util::connectSql($tmpquery10);
    Util::connectSql($tmpquery11);
//if mantis bug tracker enabled
    if ($enableMantis == "true") {
// call mantis function to delete project
        include("../mantis/proj_delete.php");
    }
    Util::headerFunction("../projects/listprojects.php?msg=delete&".session_name()."=".session_id());
}

include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($strings["delete_projects"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include('../includes/messages.php');
    $blockPage->messagebox($msgLabel);
}

$block1 = new block();

$block1->form = "saP";
$block1->openForm("../projects/deleteproject.php?action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["delete_projects"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

for ($i=0;$i<$comptListProjects;$i++) {
$block1->contentRow("#".$listProjects->pro_id[$i],$listProjects->pro_name[$i]);
}

$block1->contentRow("","<input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\">");

$block1->closeContent();
$block1->closeForm();


include('../themes/'.THEME.'/footer.php');
?>