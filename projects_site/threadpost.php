<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Projects\Projects;
use phpCollab\Topics\Topics;

$checkSession = "true";
include '../includes/library.php';

$id = $request->query->get('id');
$strings = $GLOBALS["strings"];
$tableCollab = $GLOBALS["tableCollab"];
$statusTopicBis = $GLOBALS["statusTopicBis"];
$idSession = $_SESSION["idSession"];
$timezoneSession = $_SESSION["timezoneSession"];

$topics = new Topics();
$projects = new Projects();

$detailTopic = $topics->getTopicByTopicId($id);

if ($detailTopic["top_published"] == "1" || $detailTopic["top_project"] != $projectSession) {
    phpCollab\Util::headerFunction("index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST["action"] == "add") {
        $detailTopic["top_posts"] = $detailTopic["top_posts"] + 1;
        $messageField = phpCollab\Util::convertData($_POST["messageField"]);
        $messageField = phpCollab\Util::autoLinks($messageField);

        $newPost = $topics->addPost($id, $idSession, $messageField);

        $topics->incrementTopicPostsCount($id);

        if ($notifications == "true") {
            try {
                $topics->sendNewPostNotification($newPost, $detailTopic);
            } catch (Exception $e) {
                echo 'Error sending mail, ' . $e->getMessage();
            }
        }
    }
}

$bouton[5] = "over";
$titlePage = $strings["post_reply"];
include 'include_header.php';

$idStatus = $detailTopic["top_status"];

$topicLastPostDate = phpCollab\Util::createDate($detailTopic["top_last_post"], $timezoneSession);
echo <<<FORM
 <form method="POST" action="../projects_site/threadpost.php?id={$id}" name="post">
    <input name="id" type="hidden" value="{$id}">
    <input name="action" type="hidden" value="add">

<table style="width: 50%" class="nonStriped">
    <tr>
        <th colspan="4">{$detailTopic["top_subject"]}</th>
    </tr>
    <tr>
        <th colspan="4">{$strings["information"]}</th>
    </tr>
    <tr>
        <th>{$strings["project"]}:</th>
        <td>{$detailTopic["top_pro_name"]}</td>
        <th>{$strings["posts"]}:</th>
        <td>{$detailTopic["top_posts"]}</td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
        <th>{$strings["last_post"]}:</th>
        <td>{$topicLastPostDate}</td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
        <th>{$strings["retired"]}:</th>
        <td>$statusTopicBis[$idStatus]</td>
    </tr>
    <tr>
        <th>{$strings["owner"]}:</th>
        <td colspan="3"><a href="mailto:{$detailTopic["top_mem_email_work"]}">{$detailTopic["top_mem_login"]}</a></td>
    </tr>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <th colspan="4">{$strings["enter_message"]}</th>
    </tr>
    <tr>
        <th nowrap>*&nbsp;{$strings["message"]}:</th>
        <td colspan="3"><textarea cols="60" name="messageField" rows="6" required></textarea></td>
    </tr>
    <tr>
        <td class="FormLabel">&nbsp;</td>
        <td colspan="3"><input name="submit" type="submit" value="{$strings["save"]}"></td>
    </tr>
</table>
FORM;

$listPosts = $topics->getPostsByTopicId($detailTopic["top_id"]);

if ($listPosts) {
    echo '<table style="width: 90%" class="nonStriped">';
    foreach ($listPosts as $post) {
        $postCreatedDate = phpCollab\Util::createDate($post["pos_created"], $timezoneSession);
        $postMessage = nl2br($post["pos_message"]);
        echo <<<TR
        <tr class="even">
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr class="even">
            <th>{$strings["posted_by"]} :</th>
            <td>{$post["pos_mem_name"]}</td>
            <td colspan="2" style="text-align: right"><a href="../projects_site/showallthreads.php?id={$id}&action=delete&post={$post["pos_id"]}">{$strings["delete_message"]}</a></td>
        </tr>
        <tr class="even">
            <th>{$strings["email"]} :</th>
            <td colspan="3"><a href="mailto:{$post["pos_mem_email_work"]}">{$post["pos_mem_email_work"]}</a></td>
        </tr>
        <tr class="even">
            <th nowrap>{$strings["when"]} :</th>
            <td colspan="3">{$postCreatedDate}</td>
        </tr>
        <tr class="even">
            <th>{$strings["message"]} :</th>
            <td colspan="3">{$postMessage}</td>
        </tr>
        <tr>
        <td colspan="4" class="odd"></td>
        </tr>
TR;
    }
        echo "</table>";
} else {
    echo "<div class='no-records'>{$strings["no_items"]}</div>";
}
echo "</form>";

include("include_footer.php");
