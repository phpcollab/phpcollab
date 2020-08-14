<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Projects\Projects;
use phpCollab\Topics\Topics;

$checkSession = "true";
include '../includes/library.php';

$projects = new Projects();
$topics = new Topics();

if ($request->isMethod('post')) {
    if ($action == "add") {
        $topicField = phpCollab\Util::convertData($request->request->get('topicField'));
        $messageField = phpCollab\Util::convertData($request->request->get('messageField'));

        $newTopic = $topics->addTopic($session->get("projectSession"), $session->get("idSession"), $topicField, 1, 1, 0);

        $messageField = phpCollab\Util::autoLinks($messageField);

        $newPost = $topics->addPost($newTopic["top_id"], $session->get("idSession"), $messageField);

        if ($notifications == "true") {
            try {
                $topics->sendNewTopicNotification($newTopic, $session);
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
<form method="post" action="../projects_site/createthread.php?project={$session->get("projectSession")}&action=add&id={$request->query->get("id")}" name="createThreadTopic">
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
