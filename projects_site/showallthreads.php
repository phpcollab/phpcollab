<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Topics\Topics;

$checkSession = "true";
include '../includes/library.php';

$topics = new Topics();

$id = $_GET["id"];
$strings = $GLOBALS["strings"];
$tableCollab = $GLOBALS["tableCollab"];
$timezoneSession = $_SESSION["timezoneSession"];
$idSession = $_SESSION["idSession"];

$detailTopic = $topics->getTopicByTopicId($id);

if ($detailTopic["top_published"] == "1" || $detailTopic["top_project"] != $projectSession) {
    phpCollab\Util::headerFunction("index.php");
}

if ($_GET["action"] == "delete") {
    $detailTopic["top_posts"] = $detailTopic["top_posts"] - 1;
    phpCollab\Util::newConnectSql(
        "DELETE FROM {$tableCollab["posts"]} WHERE id = :post_id",
        ["post_id" => $_GET["post"]]
    );

    phpCollab\Util::newConnectSql(
        "UPDATE {$tableCollab["topics"]} SET posts=:posts WHERE id = :topic_id",
        ["posts" => $detailTopic["top_posts"], "topic_id" => $id]
    );
    phpCollab\Util::headerFunction("showallthreads.php?id=$id");
}

$bouton[5] = "over";
$titlePage = $strings["bulletin_board_topic"];
include 'include_header.php';

$listPosts = $topics->getPostsByTopicId($detailTopic["top_id"]);

$idStatus = $detailTopic["top_status"];

$topicDate = phpCollab\Util::createDate($detailTopic["top_last_post"], $timezoneSession);

echo <<<TABLE
<table style="width: 90%;" class="nonStriped">
    <tr class="lightHighlight">
        <th colspan="4">{$strings["information"]}:</th>
    </tr>
    <tr class="lightHighlight">
        <th>{$strings["subject"]}:</th>
        <td>{$detailTopic["top_subject"]}</td>
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
            <td colspan="4" style="text-align: right;"><a href="threadpost.php?id={$id}">{$strings["post_reply"]}</a></td>
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

        if ($detailProject["pro_owner"] == $idSession || $profilSession == "0" || $post["pos_member"] == $idSession) {
            echo <<<LINK
                <a href="../projects_site/showallthreads.php?id={$id}&action=delete&post={$post["pos_id"]}">{$strings["delete_message"]}</a>
LINK;
        } else {
            echo "&nbsp";
        }

        $createdDate = phpCollab\Util::createDate($post["pos_created"], $timezoneSession);
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

include("include_footer.php");
