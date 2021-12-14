<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$commentId = empty($request->query->get('id')) ? $request->request->get('id') : $request->query->get('id');
$postId = $request->query->get('postid');
$action = empty($request->request->get('action')) ? $request->query->get('action') : $request->request->get('action');

try {
    $newsDesk = $container->getNewsdeskLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

//case update post
if (!empty($commentId)) {
    //test exists selected client organization, redirect to list if not
    $commentDetail = $newsDesk->getNewsDeskCommentById($commentId);

    if (!$commentDetail) {
        $session->getFlashBag()->add(
            'message',
            $strings["newsdesk_item_blank"]
        );
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $postId);
    }

    // only comment's author, admin, prj-adm and prj-man can change the comments
    $commentAuthor = $members->getMemberById($commentDetail["newscom_name"]);

    if (!in_array($session->get('profile'), [0, 1, 5])
        && $session->get("id") != $commentDetail["newscom_name"]
    ) {
        $session->getFlashBag()->add(
            'message',
            $strings["errorpermission_newsdesk_comment"]
        );
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $postId);
    }

    // Make sure the form was submitted
    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($action == "update") {

                    $newsDesk->setComment($commentId, $request->request->get("comment"));

                    $session->getFlashBag()->add(
                        'message',
                        $strings["newsdesk_comment_updated"]
                    );

                    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $postId);
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Newsdesk: Edit Message' => $request->query->get("id"),
                '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
                '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $session->getFlashBag()->add(
                'message',
                $strings["no_permissions"]
            );
        }
    }
}

$setTitle .= " : " . $strings["edit_newsdesk_comment"];

include APP_ROOT . '/views/layout/header.php';

$newsDetail = $newsDesk->getPostById($postId);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/viewnews.php?id=$postId", $newsDetail["news_title"],
    "in"));

$blockPage->itemBreadcrumbs($commentAuthor["mem_name"]);
$blockPage->itemBreadcrumbs($strings["edit_newsdesk_comment"]);

$blockPage->closeBreadcrumbs();


if ($session->getFlashBag()->has('message')) {
    $blockPage->messageBox( $session->getFlashBag()->get('message')[0] );
} else if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

if (isset($error) && !empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1 = new phpCollab\Block();

echo <<<FORMSTART
<form name="ecDForm" id="{$block1->form}Anchor" method="post" action="../newsdesk/editcomment.php?postid=$postId&id=$commentId">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
    <input type="hidden" name="postId" value="$postId" />
    <input type="hidden" name="commenterId" value="{$commentDetail["newscom_name"]}">
FORMSTART;

$block1->heading($strings["edit_newsdesk_comment"] . " : " . $newsDetail["news_title"]);

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["author"],
    "<strong>" . $commentAuthor["mem_name"] . "</strong>");

$block1->contentRow($strings["comment"],
    '<textarea rows="30" name="comment" style="width: 400px;">' . $commentDetail["newscom_comment"] . '</textarea>');
$block1->contentRow('',
    '<button type="submit" name="action" value="update">' . $strings["save"] . '</button> ' .
    '<input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">'
);

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
