<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$commentId = empty($request->query->get('id')) ? $request->request->get('id') : $request->query->get('id');
$postId = empty($request->query->get('postId')) ? $request->request->get('postId') : $request->query->get('postId');

try {
    $newsDesk = $container->getNewsdeskLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

// Check to make sure that a non-authorized user isn't trying to delete a comment they aren't supposed to
if (!empty($commentId)) {
    // Get all the comments requested
    $commentDetail = $newsDesk->getComments(str_replace("**", ",", $commentId));

    // Only the owner, admin, pm, or PM administrator can delete
    foreach ($commentDetail as $comment) {
        if (!in_array($session->get('profile'), [0, 1, 5]) || $session->get('id') !== $comment["newscom_name"]) {
            $session->getFlashBag()->add(
                'message',
                $strings["errorpermission_newsdesk_comment"]
            );
            phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $request->request->get('postId'));
        }
    }
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            // Check again to make sure the user has authorization to delete comments
            // only admin, project admins, and project managers can delete a comments
            if (!in_array($session->get('profile'), [0, 1, 5])) {
                $session->getFlashBag()->add(
                    'message',
                    $strings["errorpermission_newsdesk"]
                );
                phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $request->request->get('postId'));
            }

            $newsDesk->deleteNewsDeskComment(str_replace("**", ",", $request->request->get('id')));
            $session->getFlashBag()->add(
                'message',
                $strings["remove_newsdesk_comment"]
            );
            phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $request->request->get('postId'));
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Newsdesk: Delete Comment' => $request->query->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
            '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
        ]);
        $msg = 'permissiondenied';
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

if (empty($commentId) || !$commentDetail) {
    $session->getFlashBag()->add(
        'message',
        $strings["newsdesk_item_blank"]
    );
    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $postId);
}

$setTitle .= " : " . $strings["del_newsdesk_comment"];

include APP_ROOT . '/views/layout/header.php';

$newsDetail = $newsDesk->getPostById($postId);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php", $strings["newsdesk"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/viewnews.php?id=$postId", $newsDetail["news_title"],
    "in"));

$blockPage->itemBreadcrumbs($commentAuthor["mem_name"]);
$blockPage->itemBreadcrumbs($strings["del_newsdesk_comment"]);

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

$block1->form = "saP";
$block1->openForm("../newsdesk/deletecomment.php?postId=$postId", null, $csrfHandler);

$block1->heading($strings["del_newsdesk_comment"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

foreach ($commentDetail as $comment) {
    $commentAuthor = $members->getMemberById($comment["newscom_name"]);
    $block1->contentRow("#" . $comment["newscom_id"], $commentAuthor["mem_name"]);
    $block1->contentRow("" . $strings["comment"], $comment["newscom_comment"]);
}

$block1->contentRow("",
    '<input type="hidden" name="postId" value="' . $postId . '">' .
    '<input type="hidden" name="id" value="' . $commentId . '">' .
    '<button type="submit" name="action" value="delete">' . $strings["delete"] . '</button> ' .
    '<input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">'
);

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
