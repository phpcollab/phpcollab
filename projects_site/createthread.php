<?php
#Application name: PhpCollab
#Status page: 0

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include '../includes/library.php';

$projects = $container->getProjectsLoader();
$topics = $container->getTopicsLoader();

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->query->get("action") == "add") {
                $topicField = phpCollab\Util::convertData($request->request->get('topicField'));
                $messageField = phpCollab\Util::convertData($request->request->get('messageField'));

                $newTopic = $topics->addTopic($session->get("project"), $session->get("id"), $topicField, 1, 1, 0);

                $messageField = phpCollab\Util::autoLinks($messageField);

                $newPost = $topics->addPost($newTopic["top_id"], $session->get("id"), $messageField);

                if ($notifications == "true") {
                    try {
                        $topics->sendNewTopicNotification($newTopic, $session);
                    } catch (Exception $e) {
                        $logger->error('Project Site (create thread)', ['Exception message', $e->getMessage()]);
                        $error = $strings["action_not_allowed"];
                    }
                }
                phpCollab\Util::headerFunction("showallthreadtopics.php");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->critical('CSRF Token Error', [
            'Project Site: Create thread',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

$bodyCommand = "onload=\"document.createThreadTopic.topicField.focus();\"";

$bouton[5] = "over";
$titlePage = $strings["create_topic"];
include 'include_header.php';

echo <<<FORM
<form method="post" action="../projects_site/createthread.php?project={$session->get("project")}&action=add&id={$request->query->get("id")}" name="createThreadTopic">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
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
