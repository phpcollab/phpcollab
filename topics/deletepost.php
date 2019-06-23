<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/deletepost.php

use phpCollab\Topics\Topics;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

$topic = $_GET["topic"];
$topics = new Topics();

$detailTopic = $topics->getTopicByTopicId($topic);

if ($_GET["action"] == "delete") {
    $detailTopic["top_posts"]--;
    Util::newConnectSql("DELETE FROM {$tableCollab["posts"]} WHERE id = :post_id", ["post_id" => $id]);

    Util::newConnectSql(
        "UPDATE {$tableCollab["topics"]} SET posts=:posts WHERE id = :topic_id",
        ["posts" => $detailTopic->top_posts[0], "topic_id" => $topic]
    );
    Util::headerFunction("../topics/viewtopic.php?msg=delete&id=$topic");
}

$detailPost = $topics->getPostById($id);

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $detailTopic["top_pro_id"], $detailTopic["top_pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?topic=" . $detailTopic["top_id"], $strings["discussion"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/viewtopic.php?id=" . $detailTopic["top_id"], $detailTopic["top_subject"], "in"));
$blockPage->itemBreadcrumbs($strings["delete_messages"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../topics/deletepost.php?id=$id&topic=$topic&action=delete");

$block1->heading($strings["delete_messages"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$postMessage = nl2br($detailPost["pos_message"]);
echo <<<POST
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td>{$postMessage}</td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
    </tr>
POST;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
