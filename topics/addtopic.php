<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$project = $request->query->get('project');

$strings = $GLOBALS["strings"];

$projects = $container->getProjectsLoader();
$teams = $container->getTeams();
$topics = $container->getTopicsLoader();

$projectDetail = $projects->getProjectById($project);


if ($projectDetail["pro_org_id"] == "1") {
    $projectDetail["pro_org_name"] = $strings["none"];
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "add") {
                $pub = $request->request->get('pub');
                if ($pub == "") {
                    $pub = "1";
                }

                $topic_subject = phpCollab\Util::convertData($request->request->get('topic_subject'));
                $topic_message = phpCollab\Util::convertData($request->request->get('topic_message'));

                $newTopic = $topics->addTopic($project, $session->get("id"), $topic_subject, 1, 1, $pub, $dateheure);


                $topic_message = phpCollab\Util::autoLinks($topic_message);

                $newPost = $topics->addPost($newTopic["top_id"], $session->get("id"), $topic_message, $dateheure);

                if ($notifications == "true") {
                    $listPosts = $topics->getPostsByTopicIdAndNotOwner($detailTopic["top_id"], $session->get("id"));

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
                        $newTopicNotice = $container->getNotificationNewTopicManager();

                        try {
                            $listPosts = $topics->getPostsByTopicIdAndNotOwner($detailTopic["top_id"],
                                $session->get("id"));

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

                            $notificationList = $sendNotifications->getNotificationsWhereMemberIn($posters);

                            $newTopicNotice->generateEmail($detailTopic, $projectDetail, $notificationList, $session);
                        } catch (Exception$e) {
                            $logger->error($e->getMessage());
                            $error = $strings["action_not_allowed"];
                        }
                    }
                }

                phpCollab\Util::headerFunction("../topics/viewtopic.php?project={$project}&id={$newTopic["top_id"]}&msg=add");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Topics: Add topic',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}


$teamMember = "false";
$teamMember = $teams->isTeamMember($projectDetail["pro_id"], $session->get("id"));

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

$bodyCommand = 'onLoad="document.ctTForm.topic_subject.focus();"';
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
    $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=" . $projectDetail["pro_id"],
    $strings["discussions"], "in"));
$blockPage->itemBreadcrumbs($strings["add_discussion"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "ctT";
$block1->openForm("../topics/addtopic.php?project=" . $projectDetail["pro_id"] . "", null, $csrfHandler);

if ((isset($error) && $error != "")) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["add_discussion"]);

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"],
    $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
        $projectDetail["pro_name"] . " (#" . $projectDetail["pro_id"] . ")", "in"));
$block1->contentRow($strings["organization"], $projectDetail["pro_org_name"]);
$block1->contentRow($strings["owner"],
    $blockPage->buildLink("../users/viewuser.php?id=" . $projectDetail["pro_mem_id"], $projectDetail["pro_mem_name"],
        "in") . " (" . $blockPage->buildLink($projectDetail["pro_mem_email_work"], $projectDetail["pro_mem_login"],
        "mail") . ")");

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["topic"],
    '<input size="44" value="' . $topic_subject . '" style="width: 400px" name="topic_subject" maxlength="64" type="TEXT">');
$block1->contentRow($strings["message"],
    '<textarea rows="10" style="width: 400px; height: 160px;" name="topic_message" cols="47">' . $topic_message . '</textarea>');
$block1->contentRow($strings["published"], '<input size="32" value="1" name="pub" type="checkbox">');
$block1->contentRow("", '<button type="submit" name="action" value="add">' . $strings["save"] . '</button>');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
