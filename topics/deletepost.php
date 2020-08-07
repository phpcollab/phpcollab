<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/deletepost.php

use phpCollab\Topics\Topics;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

$topicId = $request->query->get('topic');

$topics = new Topics();
$postId = $request->query->get('id');

$detailTopic = $topics->getTopicByTopicId($topicId);

if ($request->isMethod('post')) {
    if ($request->request->get("action") == "delete") {
        try {
            $topics->deletePost($postId);

            if ($detailTopic["top_posts"] != 0) {
                $topics->decrementTopicPostsCount($topicId);
            }

            Util::headerFunction("../topics/viewtopic.php?msg=delete&id=$topicId");

        } catch (Exception$exception) {
            error_log('Error deleting post', 0);
            $error = $strings["error_delete_post"];
        }
    }

}


$detailPost = $topics->getPostById($postId);

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
$block1->openForm("../topics/deletepost.php?id=$postId&topic=$topicId");

$block1->heading($strings["delete_messages"]);

if (isset($error) && !empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}
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
        <td><button type="submit" name="action" value="delete">{$strings["delete"]}</button> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
    </tr>
POST;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
