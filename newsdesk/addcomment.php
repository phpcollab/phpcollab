<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $newsDesk = $container->getNewsdeskLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$postId = $request->query->get('postid');

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            //test if name blank
            if (empty($request->request->get('comment'))) {
                $error = $strings["blank_newsdesk_comment"];
            } else {
                //insert into organizations and redirect to new client organization detail (last id)
                $newsDesk->addComment(
                    $request->request->get('postId'),
                    $request->request->get('commenterId'),
                    $request->request->get('comment')
                );

                $session->getFlashBag()->add(
                    'message',
                    $strings["newsdesk_comment_added"]
                );

                phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postId");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Newsdesk: Add Message',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

$setTitle .= " : " . $strings["add_newsdesk_comment"];

include APP_ROOT . '/views/layout/header.php';

$newsDetail = $newsDesk->getPostById($postId);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/viewnews.php?id=$postId", $newsDetail["news_title"],
    "in"));

if (empty($commentId)) {
    $blockPage->itemBreadcrumbs($strings["add_newsdesk_comment"]);
} elseif ($action == "remove") {
    $blockPage->itemBreadcrumbs($commentAuthor["mem_name"]);
    $blockPage->itemBreadcrumbs($strings["del_newsdesk_comment"]);
} else {
    $blockPage->itemBreadcrumbs($commentAuthor["mem_name"]);
    $blockPage->itemBreadcrumbs($strings["edit_newsdesk_comment"]);
}

$blockPage->closeBreadcrumbs();

if (!empty($msg)) {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

if (isset($error) && !empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1 = new phpCollab\Block();

echo <<<FORM
<form name="ecDForm" method="post" action="../newsdesk/addcomment.php?postid=$postId">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
    <input type="hidden" name="commenterId" value="{$session->get("id")}">
    <input type="hidden" name="postId" value="$postId" />
FORM;

$block1->heading($strings["add_newsdesk_comment"]);

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["author"], '<strong>' . $session->get("name") . '</strong>');

$block1->contentRow($strings["comment"],
    '<textarea rows="30" name="comment" style="width: 400px;">' . $commentDetail["newscom_comment"] . '</textarea>');
$block1->contentRow('',
    '<input type="submit" value="' . $strings["save"] . '"> ' .
    '<input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">'
);

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
