<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/addpost.php

use phpCollab\Notifications\Notifications;
use phpCollab\Notifications\TopicNewPost;
use phpCollab\Projects\Projects;
use phpCollab\Topics\Topics;

$checkSession = "true";
include_once '../includes/library.php';

$topics = new Topics();
$projects = new Projects();
$sendNotifications = new Notifications();

$topic_id = $_GET["id"];
$strings = $GLOBALS["strings"];
$action = $_GET["action"];

$detailTopic = $topics->getTopicByTopicId($topic_id);

$projectDetail = $projects->getProjectById($detailTopic["top_project"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == "add") {
        $post_message = phpCollab\Util::convertData($_POST["post_message"]);
        $post_message = phpCollab\Util::autoLinks($post_message);

        // Increment the local copy of detailTopic instead of making another DB call to update and retrieve the count
        $detailTopic["top_posts"] = $detailTopic["top_posts"] + 1;

        // Add new post
        $newPost = $topics->addPost($topic_id, $_SESSION["idSession"], $post_message);

        // Increment the
        $topics->incrementTopicPostsCount($topic_id);

        if ($notifications == "true") {
            $listPosts = $topics->getPostsByTopicIdAndNotOwner($detailTopic["top_id"], $_SESSION["idSession"]);

            $distinct = '';

            foreach ($listPosts as $post) {
                if ($post["pos_mem_id"] != $distinct) {
                    $posters .= $post["pos_mem_id"] . ",";
                }
                $distinct = $post["pos_mem_id"];
            }
            if (substr($posters, -1) == ",") {
                $posters = substr($posters, 0, -1);
            }

            if ($posters != "") {


                $newPostNotice = new TopicNewPost();

                try {
                    $notificationList = $sendNotifications->getNotificationsWhereMemberIn($posters);

                    $newPostNotice->generateEmail($detailTopic, $projectDetail, $notificationList);

                } catch (Exception$e) {
                    // Log exception
                }
            }
        }
        phpCollab\Util::headerFunction("../topics/viewtopic.php?id=$topic_id&msg=add");
    }
}

$idStatus = $detailTopic["top_status"];
$idPublish = $detailTopic["top_published"];

$listPosts = $topics->getPostsByTopicId($detailTopic["top_id"]);

if ($projectDetail["pro_org_id"] == "1") {
    $projectDetail["pro_org_name"] = $strings["none"];
}

$setTitle .= " : " . $strings["post_to_discussion"];

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

$block1->contentRow($strings["message"], '<textarea rows="10" style="width: 400px; height: 160px;" name="post_message" cols="47"></textarea>');
$block1->contentRow("", '<input type="SUBMIT" value="' . $strings["save"] . '"">');

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
