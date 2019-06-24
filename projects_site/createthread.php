<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Projects\Projects;
use phpCollab\Topics\Topics;

$checkSession = "true";
include '../includes/library.php';

$projects = new Projects();
$topics = new Topics();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == "add") {
        $topicField = phpCollab\Util::convertData($_POST["topicField"]);
        $messageField = phpCollab\Util::convertData($_POST["messageField"]);

        $newTopic = $topics->addTopic($projectSession, $idSession, $topicField, 1, 1, 0);

        $messageField = phpCollab\Util::autoLinks($messageField);

        $newPost = $topics->addPost($newTopic["top_id"], $idSession, $messageField);

        if ($notifications == "true") {
            try {
                $topics->sendNewTopicNotification($newTopic);
            } catch (Exception $e) {
                echo 'Error sending mail, ' . $e->getMessage();
            }
        }
        phpCollab\Util::headerFunction("showallthreadtopics.php");
    }
}

$bodyCommand = "onload=\"document.createThreadTopic.topicField.focus();\"";

$bouton[5] = "over";
$titlePage = $strings["create_topic"];
include 'include_header.php';

echo <<<FORM
<form method="post" action="../projects_site/createthread.php?project={$projectSession}&action=add&id={$id}" name="createThreadTopic">
    <table style="width: 90%;" class="nonStriped">
        <tr>
            <th colspan="2">{$strings["create_topic"]}</th>
        </tr>
        <tr>
            <th>* {$strings["topic"]} :</th>
            <td><input size="35" value="$topicField" name="topicField" type="text" required></td>
        </tr>
        <tr>
            <th colspan="2">{$strings["enter_message"]}</th>
        </tr>
        <tr>
            <th>* {$strings["message"]} :</th>
            <td><textarea rows="3" name="messageField" cols="43" required></textarea></td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td><input name="submit" type="submit" value="{$strings["save"]}"></td>
        </tr>
    </table>
</form>
FORM;

include("include_footer.php");
