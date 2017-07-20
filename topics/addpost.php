<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/addpost.php

$checkSession = "true";
include_once '../includes/library.php';

$topics = new \phpCollab\Topics\Topics();
$projects = new \phpCollab\Projects\Projects();

$id = $_GET["id"];
$strings = $GLOBALS["strings"];
$action = $_GET["action"];

$detailTopic = $topics->getTopicByTopicId($id);

$projectDetail = $projects->getProjectById($detailTopic["top_project"]);

if ($action == "add") {
    $tableCollab = $GLOBALS["tableCollab"];
    $tpm = phpCollab\Util::convertData($_POST["tpm"]);
    phpCollab\Util::autoLinks($tpm);
    $detailTopic["top_posts"] = $detailTopic["top_posts"] + 1;
    $tmpquery1 = "INSERT INTO {$tableCollab["posts"]} (topic,member,created,message) VALUES (:topic,:member,:created,:message)";
    $dbParams = [];
    $dbParams['topic'] = $id;
    $dbParams['member'] = $_SESSION["idSession"];
    $dbParams['created'] = $dateheure;
    $dbParams['message'] = $GLOBALS["newText"];
    phpCollab\Util::newConnectSql($tmpquery1, $dbParams);
    unset($dbParams);

    $tmpquery2 = "UPDATE {$tableCollab["topics"]} SET last_post=:last_post,posts=:posts WHERE id = :topic_id";
    $dbParams = [];
    $dbParams['last_post'] = $dateheure;
    $dbParams['posts'] = $detailTopic["top_posts"];
    $dbParams['topic_id'] = $id;
    phpCollab\Util::newConnectSql($tmpquery2, $dbParams);
    unset($dbParams);

    if ($notifications == "true") {
        include '../topics/noti_newpost.php';
    }
    phpCollab\Util::headerFunction("../topics/viewtopic.php?id=$id&msg=add");
}

$idStatus = $detailTopic["top_status"];
$idPublish = $detailTopic["top_published"];

$listPosts = $topics->getPostsByTopicId($detailTopic["top_id"]);

if ($projectDetail["pro_org_id"] == "1") {
    $projectDetail["pro_org_name"] = $strings["none"];
}

$bodyCommand = "onLoad=\"document.ptTForm.tpm.focus();\"";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=" . $projectDetail["pro_id"], $strings["discussions"], "in"));
$blockPage->itemBreadcrumbs($detailTopic["top_subject"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "ptT";
$block1->openForm("../topics/addpost.php?action=add&id=" . $detailTopic["top_id"] . "&project=" . $detailTopic["top_project"]);

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["post_to_discussion"] . " : " . $detailTopic["top_subject"]);

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"] . " (#" . $projectDetail["pro_id"] . ")", "in"));
$block1->contentRow($strings["organization"], $projectDetail["pro_org_name"]);
$block1->contentRow($strings["owner"], $blockPage->buildLink("../users/viewuser.php?id=" . $projectDetail["pro_mem_id"], $projectDetail["pro_mem_name"], "in") . " (" . $blockPage->buildLink($projectDetail["pro_mem_email_work"], $projectDetail["pro_mem_login"], "mail") . ")");

if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $GLOBALS["statusPublish"][$idPublish]);
}

$block1->contentRow($strings["retired"], $GLOBALS["statusTopicBis"][$idStatus]);
$block1->contentRow($strings["posts"], $detailTopic["top_posts"]);
$block1->contentRow($strings["last_post"], phpCollab\Util::createDate($detailTopic["top_last_post"], $GLOBALS["timezoneSession"]));

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["message"], "<textarea rows=\"10\" style=\"width: 400px; height: 160px;\" name=\"tpm\" cols=\"47\"></textarea>");
$block1->contentRow("", "<input type=\"SUBMIT\" value=\"" . $strings["save"] . "\">");

$block1->contentTitle($strings["posts"]);

//for ($i = 0; $i < $comptListPosts; $i++) {
foreach ($listPosts as $post) {
    $block1->contentRow($strings["posted_by"], $blockPage->buildLink($post["pos_mem_email_work"], $post["pos_mem_name"], "mail"));

    if ($post["pos_created"] > $GLOBALS["lastvisiteSession"]) {
        $block1->contentRow($strings["when"], "<b>" . phpCollab\Util::createDate($post["pos_created"], $GLOBALS["timezoneSession"]) . "</b>");
    } else {
        $block1->contentRow($strings["when"], phpCollab\Util::createDate($post["pos_created"], $GLOBALS["timezoneSession"]));
    }
    $block1->contentRow("", nl2br($post["pos_message"]));
    $block1->contentRow("", "", "true");
}

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
