<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
require_once '../includes/library.php';

$topics = $container->getTopicsLoader();

$topicId = $request->query->get("topic");

$postId = $request->query->get("post");

$strings = $GLOBALS["strings"];

$detailTopic = $topics->getTopicByTopicId($topicId);

if ($detailTopic["top_published"] == "1" || $detailTopic["top_project"] != $session->get("project")) {
    phpCollab\Util::headerFunction("index.php");
}

if (!empty($postId) && $request->query->get('action') == "delete") {
    $topics->deletePost($postId);

    if ($detailTopic["top_posts"] != 0) {
        $topics->decrementTopicPostsCount($topicId);
    }

    phpCollab\Util::headerFunction("showallthreads.php?topic={$topicId}&msg=postDeleted");
}

$bouton[5] = "over";
$titlePage = $strings["bulletin_board_topic"];

include APP_ROOT . '/projects_site/include_header.php';

$listPosts = $topics->getPostsByTopicId($detailTopic["top_id"]);

$idStatus = $detailTopic["top_status"];

$topicDate = phpCollab\Util::createDate($detailTopic["top_last_post"], $session->get("timezone"));

if ($request->query->get("msg") != "") {
    include '../includes/messages.php';
    if ($msgLabel) {
        echo '<table class="message"><tr><td>' . $msgLabel . '</td></tr></table>';
    }
}

echo <<<TABLE
<table style="width: 90%;" class="nonStriped">
    <tr class="lightHighlight">
        <th colspan="4">{$strings["information"]}:</th>
    </tr>
    <tr class="lightHighlight">
        <th>{$strings["subject"]}:</th>
        <td>{$detailTopic["top_id"]} - {$detailTopic["top_subject"]}</td>
        <th>{$strings["posts"]}:</th>
        <td>{$detailTopic["top_posts"]}</td>
    </tr>
    <tr class="lightHighlight">
        <th>{$strings["project"]}:</th>
        <td>{$projectDetail["pro_name"]}</td>
        <th>{$strings["last_post"]}:</th>
        <td>{$topicDate}</td>
    </tr>
    <tr class="lightHighlight">
        <th>&nbsp;</th>
        <td>&nbsp;</td>
        <th>{$strings["retired"]}:</th>
        <td>{$statusTopicBis[$idStatus]}</td>
    </tr>
    <tr class="lightHighlight">
        <th>{$strings["owner"]}:</th>
        <td colspan="3"><a href="mailto:{$detailTopic["top_mem_email_work"]}">{$detailTopic["top_mem_login"]}</a></td>
    </tr>
    <tr class="lightHighlight">
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr class="even">
        <th colspan="4">{$strings["discussion"]}:</th>
    </tr>
TABLE;


if ($detailTopic["top_status"] == "1") {
    echo <<<TR
        <tr class="even">
            <td colspan="4" style="text-align: right;"><a href="threadpost.php?topic={$topicId}">{$strings["post_reply"]}</a></td>
        </tr>
TR;
}

if ($listPosts) {
    foreach ($listPosts as $post) {
        echo <<<TR
        <tr class="odd"><td colspan="4"><hr /></td></tr>
        <tr class="even">
            <th>{$strings["posted_by"]} :</th>
            <td>{$post["pos_mem_name"]}</td>
            <td colspan="2" style="text-align: right;">
TR;

        if ($detailProject["pro_owner"] == $session->get("id") || $session->get("profile") == "0" || $post["pos_member"] == $session->get("id")) {
            echo <<<LINK
                <a href="../projects_site/showallthreads.php?topic={$topicId}&action=delete&post={$post["pos_id"]}">({$post["pos_id"]}) {$strings["delete_message"]}</a>
LINK;
        } else {
            echo "&nbsp";
        }

        $createdDate = phpCollab\Util::createDate($post["pos_created"], $session->get("timezone"));
        $postMessage = nl2br($post["pos_message"]);
        echo <<<TR
            </td>
        </tr>
        <tr class="even">
            <th>{$strings["email"]} :</th>
            <td colspan="3"><a href="mailto:{$post["pos_mem_email_work"]}">{$post["pos_mem_email_work"]}</a></td>
        </tr>
        <tr class="even">
            <th nowrap>{$strings["when"]} :</th>
            <td colspan="3">{$createdDate}</td>
        </tr>
        <tr class="even">
            <th>{$strings["message"]} :</th>
            <td colspan="3">{$postMessage}</td>
        </tr>
        <tr class="odd"><td colspan="4"><hr /></td></tr>
TR;
    }
} else {
    echo '<tr><td colspan="4" class="ListOddRow">' . $strings["no_items"] . '</td></tr>';
}
echo "</table>";

include APP_ROOT . "/projects_site/include_footer.php";
