<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/deletetopics.php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

$projects = $container->getProjectsLoader();
$topics = $container->getTopicsLoader();

$action = $request->query->get('action');
$id = $request->query->get('id');

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "delete") {
                $id = str_replace("**", ",", $id);
                $pieces = explode(",", $id);
                $num = count($pieces);

                try {
                    $topics->deleteTopics($pieces);
                    $topics->deletePostsFromTopics($pieces);

                } catch (Exception $e) {
                    $logger->error($e->getMessage());
                    $error = $strings["action_not_allowed"];
                }

                if ($project != "") {
                    phpCollab\Util::headerFunction("../projects/viewproject.php?num={$num}&msg=deleteTopic&id=" . $project);
                } else {
                    phpCollab\Util::headerFunction("../general/home.php?msg=deleteTopic&num=" . $num);
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->critical('CSRF Token Error', [
            'Topics: Delete topic',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}


if ($request->query->get('project')) {
    $project = $request->query->get('project');
} else {
    unset($project);
}

$projectDetail = $projects->getProjectById($project);

$setTitle .= " : " . $strings["delete_discussions"];

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($project != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=" . $projectDetail["pro_id"], $strings["discussions"], "in"));
} else {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php?", $strings["home"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?", $strings["my_discussions"], "in"));
}
$blockPage->itemBreadcrumbs($strings["delete_discussions"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../topics/deletetopics.php?project=$project&id=" . $id, null, $csrfHandler);

$block1->heading($strings["delete_discussions"]);

$block1->openContent();

$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$listTopics = $topics->getTopicsIn($id);

foreach ($listTopics as $listTopic) {
    echo <<<TR
        <tr class="odd">
            <td class="leftvalue">&nbsp;</td>
            <td>{$listTopic["top_subject"]}</td>
        </tr>
TR;
}

echo <<<TR
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td><button type="submit" name="action" value="delete">{$strings["delete"]}</button> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
    </tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
