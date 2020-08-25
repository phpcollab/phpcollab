<?php

use phpCollab\NewsDesk\NewsDesk;

$checkSession = "true";
include_once '../includes/library.php';

$commentId = $request->query->get('id');
$postId = $request->query->get('postid');
$action = $request->request->get('action');

$newsDesk = new NewsDesk();

//case update post
if (!empty($commentId)) {
    //test exists selected client organization, redirect to list if not
    $commentDetail = $newsDesk->getNewsDeskCommentById($commentId);

    if (!$commentDetail) {
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postId&msg=blankNews");
    }

    // only comment's author, admin, prj-adm and prj-man can change the comments
    $commentAuthor = $members->getMemberById($commentDetail["newscom_name"]);

    if ($session->get("profile") != "0" && $session->get("profile") != "1" && $session->get("profile") != "5" && $session->get("idSession") != $commentDetail["newscom_name"]) {
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postId&msg=commentpermissionNews");
    }

    // Make sure the form was submitted
    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($action == "update") {
                    $comment = phpCollab\Util::convertData($request->request->get("comment"));

                    $newsDesk->setComment($commentId, $comment);

                    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postId&msg=update");
                } elseif ($action == "delete") {
                    // only admin, prj-adm and prj-man can delete a comments
                    if ($session->get("profile") != "0" && $session->get("profile") != "1" && $session->get("profile") != "5") {
                        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postId&msg=commentpermissionNews");
                    }

                    $commentId = str_replace("**", ",", $request->request->get('id'));

                    $newsDesk->deleteNewsDeskComment($commentId);
                    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postId&msg=removeComment");
                }
            }
        } catch (Exception $e) {
            $logger->critical('CSRF Token Error', [
                'edit bookmark' => $request->request->get("id"),
                '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
            ]);
            $msg = 'permissiondenied';
        }
    }


} else { // case of adding new post

    if ($action == "add") {
        if ($request->isMethod('post')) {
            try {
                if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                    //test if name blank
                    if (empty($request->request->get('comment'))) {
                        $error = $strings["blank_newsdesk_comment"];
                    } else {

                        //replace quotes by html code in name and address
                        $commenterId = phpCollab\Util::convertData($request->request->get('commenterId'));
                        $comment = phpCollab\Util::convertData($request->request->get('comment'));
                        $postId = phpCollab\Util::convertData($request->request->get('postId'));

                        //insert into organizations and redirect to new client organization detail (last id)
                        $newsDesk->addComment($postId, $commenterId, $comment);

                        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postId&msg=add");
                    }
                }
            } catch (Exception $e) {
                $logger->critical('CSRF Token Error', [
                    'edit bookmark' => $request->request->get("id"),
                    '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                    '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
                ]);
                $msg = 'permissiondenied';
            }
        }
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

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

// Add / Update
if ($action != 'remove') {


    $formTag = <<<FORMSTART
<form name="ecDForm" id="{$block1->form}Anchor" method="post" action="../newsdesk/editmessage.php?postid={$postId}
FORMSTART;

    if (empty($commentId)) {
        $block1->heading($strings["add_newsdesk_comment"]);
        $formAction = "add";
    } else {
        $formAction = "update";
        $formTag = $formTag . "&id={$commentId}";
        $block1->heading($strings["edit_newsdesk_comment"] . " : " . $newsDetail["news_title"]);
    }

    echo <<<FORM_END
{$formTag}">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
FORM_END;

    $block1->openContent();
    $block1->contentTitle($strings["details"]);

    // add or edit comment
    if (empty($commentId)) {
        $block1->contentRow($strings["author"],
            '<input type="hidden" name="commenterId" value="' . $session->get("idSession") . '"><strongb>' . $session->get("nameSession") . '</strongb>');
    } else {
        $block1->contentRow($strings["author"],
            "<input type='hidden' name='commenterId' value='" . $commentDetail["newscom_name"] . "'><strong>" . $commentAuthor["mem_name"] . "</strong>");
    }

    $block1->contentRow($strings["comment"],
        '<textarea rows="30" name="comment" style="width: 400px;">' . $commentDetail["newscom_comment"] . '</textarea>');
    $block1->contentRow($strings[""],
        '<input type="hidden" name="postId" value="' . $postId . '" />' .
        '<button type="submit" name="action" value="' . $formAction . '">' . $strings["save"] . '</button> ' .
        '<input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">'
    );

    $block1->closeContent();
    $block1->closeForm();
}

if ($action == "remove") { //remove action
    $block1->form = "saP";
    $block1->openForm("../newsdesk/editmessage.php?postId=$postId", null, $csrfHandler);

    $block1->heading($strings["del_newsdesk_comment"]);

    $block1->openContent();
    $block1->contentTitle($strings["delete_following"]);

    $old_id = $commentId;
    $commentId = str_replace("**", ",", $commentId);

    $listNews = $newsDesk->getComments($commentId);

    foreach ($listNews as $news) {
        $newsAuthor = $members->getMemberById($news["newscom_name"]);
        $block1->contentRow("#" . $news["newscom_id"], $newsAuthor["mem_name"]);
    }

    $block1->contentRow("",
        '<input type="hidden" name="id" value="' . $old_id . '"><button type="submit" name="action" value="delete">' . $strings["delete"] . '</button> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

    $block1->closeContent();
    $block1->closeForm();
}

include APP_ROOT . '/themes/' . THEME . '/footer.php';
