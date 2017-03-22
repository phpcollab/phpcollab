<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$id = $_GET["id"];
$strings = $GLOBALS["strings"];
$tableCollab = $GLOBALS["tableCollab"];
$statusTopicBis = $GLOBALS["statusTopicBis"];
$idSession = $_SESSION["idSession"];
$timezoneSession = $_SESSION["timezoneSession"];

$topics = new \phpCollab\Topics\Topics();

$detailTopic = $topics->getTopicByTopicId($id);

if ($detailTopic["top_published"] == "1" || $detailTopic["top_project"] != $projectSession) {
    phpCollab\Util::headerFunction("index.php");
}

if ($_GET["action"] == "add") {
    $detailTopic["top_posts"] = $detailTopic["top_posts"] + 1;
    $messageField = phpCollab\Util::convertData($_POST["messageField"]);
    phpCollab\Util::autoLinks($messageField);
    phpCollab\Util::newConnectSql(
        "INSERT INTO {$tableCollab["posts"]} (topic,member,created,message) VALUES (:topic,:member,:created,:message)",
        ["topic" => $id,"member" => $idSession,"created" => $dateheure,"message" => $GLOBALS["newText"]]
    );

    phpCollab\Util::newConnectSql(
        "UPDATE {$tableCollab["topics"]} SET last_post=:last_post,posts=:posts WHERE id = :topic_id",
        ["last_post" => $dateheure, "posts" => $detailTopic["top_posts"], "topic_id" => $id]
    );

    if ($notifications == "true") {
        $tmpquery = "WHERE pro.id = '$projectSession'";
        $projectDetail = new phpCollab\Request();
        $projectDetail->openProjects($tmpquery);

        include '../topics/noti_newpost.php';
    }
}

$bouton[5] = "over";
$titlePage = $strings["post_reply"];
include 'include_header.php';

$idStatus = $detailTopic["top_status"];

echo <<<FORM
 <form accept-charset="UNKNOWN" method="POST" action="../projects_site/threadpost.php?action=add" name="post" enctype="application/x-www-form-urlencoded">
    <input name="id" type="hidden" value="{$id}">
FORM;

echo '<table cellspacing="0" width="90%" cellpadding="3">';
echo '<tr><th colspan="4">' . $detailTopic["top_subject"] . '</th></tr>';
echo '<tr><th colspan="4">' . $strings["information"] . '</th></tr>';
echo '<tr><th>' . $strings["project"] . ':</th><td>' . $projectDetail->pro_name[0] . '</td><th>' . $strings["posts"] . ':</th><td>' . $detailTopic["top_posts"] . '</td></tr>';
echo '<tr><th>&nbsp;</th><td>&nbsp;</td><th>' . $strings["last_post"] . ':</th><td>' . phpCollab\Util::createDate($detailTopic["top_last_post"], $timezoneSession) . '</td></tr>';
echo '<tr><th>&nbsp;</th><td>&nbsp;</td><th>' . $strings["retired"] . ':</th><td>$statusTopicBis[$idStatus]</td></tr>';
echo '<tr><th>' . $strings["owner"] . ':</th><td colspan="3"><a href="mailto:' . $detailTopic["top_mem_email_work"] . '">' . $detailTopic["top_mem_login"] . '</a></td></tr>';
echo '<tr><td colspan="4">&nbsp;</td></tr>';
echo '<tr><th colspan="4">' . $strings["enter_message"] . '</th></tr>';
echo '<tr><th nowrap>*&nbsp;' . $strings["message"] . ':</th><td colspan="3"><textarea cols="60" name="messageField" rows="6"></textarea></td></tr>';
echo '<tr><td class="FormLabel">&nbsp;</td><td colspan="3"><input name="submit" type="submit" value="' . $strings["save"] . '"></td></tr>';
echo '</form>';

$tmpquery = "WHERE pos.topic = '" . $detailTopic["top_id"] . "' ORDER BY pos.created DESC";
$listPosts = new phpCollab\Request();
$listPosts->openPosts($tmpquery);

$listPosts = $topics->getPostsByTopicId($detailTopic["top_id"]);

if ($listPosts) {
    foreach ($listPosts as $post) {
        if (!($i % 2)) {
            $class = "odd";
        } else {
            $class = "even";
        }
        echo '<tr><td colspan="4" class="'.$class.'">&nbsp;</td></tr>';
        echo '<tr class="$class"><th>' . $strings["posted_by"] . ' :</th><td>' . $post["pos_mem_name"] . '</td><td colspan="2" align="right"><a href="../projects_site/showallthreads.php?id=$id&action=delete&post=' . $post["pos_id"] . '">' . $strings["delete_message"] . '</a></td></tr>';
        echo '<tr class="$class"><th>' . $strings["email"] . ' :</th><td colspan="3"><a href="mailto:' . $post["pos_mem_email_work"] . '">' . $post["pos_mem_email_work"] . '</a></td></tr>';
        echo '<tr class="$class"><th nowrap>' . $strings["when"] . ' :</th><td colspan="3">' . phpCollab\Util::createDate($post["pos_created"], $timezoneSession) . '</td></tr>';
        echo '<tr class="$class"><th>' . $strings["message"] . ' :</th><td colspan="3">' . nl2br($post["pos_message"]) . '</td></tr>';
    }
} else {
    echo "<tr><td colspan=\"4\" class=\"ListOddRow\">" . $strings["no_items"] . "</td></tr>";
}
echo "</table>";

include("include_footer.php");
