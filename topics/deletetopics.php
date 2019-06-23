<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/deletetopics.php

use phpCollab\Projects\Projects;
use phpCollab\Topics\Topics;

$checkSession = "true";
include_once '../includes/library.php';

$projects = new Projects();
$topics = new Topics();

$action = $_GET["action"];
$id = $_GET["id"];

if ($_GET["action"] == "delete") {
    $id = str_replace("**", ",", $id);
    $pieces = explode(",", $id);
    $num = count($pieces);

    try {
        $topics->deleteTopics($pieces);
        $topics->deletePostsFromTopics($pieces);

    } catch (Exception $e) {
        // handle exception
    }

    if ($project != "") {
        phpCollab\Util::headerFunction("../projects/viewproject.php?num=$num&msg=deleteTopic&id=$project");
    } else {
        phpCollab\Util::headerFunction("../general/home.php?num=$num&msg=deleteTopic");
    }
}
if ($_GET['project']) {
    $project = $_GET['project'];
} else {
    unset($project);
}
$projectDetail = $projects->getProjectById($project);

$setTitle .= " : " . $strings["delete_discussions"];

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($project != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=" . $projectDetail["pro_id"], $strings["discussions"], "in"));
} else {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php?", $strings["home"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?", $strings["my_discussions"], "in"));
}
$blockPage->itemBreadcrumbs($strings["delete_discussions"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../topics/deletetopics.php?project=$project&action=delete&id=$id");

$block1->heading($strings["delete_discussions"]);

$block1->openContent();

$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$listTopics = $topics->getTopicsIn($id);

foreach ($listTopics as $listTopic) {
    echo <<<TR
        <tr class="odd">
            <td class="leftvalue">&nbsp;</td>
            <td>{$listTopic["top_subject"]}</td>
        </tr>
TR;
}

echo <<<TR
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
    </tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
