<?php

use phpCollab\Members\Members;
use phpCollab\NewsDesk\NewsDesk;

$checkSession = "true";
include_once '../includes/library.php';

$commentId = $_GET["id"];
$postid = $_GET["postid"];
$action = $_GET["action"];
$idSession = $_SESSION["idSession"];

$newsDesk = new NewsDesk();
$members = new Members();

//case update post
if ($commentId != "") {
    //test exists selected client organization, redirect to list if not
    $commentDetail = $newsDesk->getNewsDeskCommentById($commentId);

    if (!$commentDetail) {
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=blankNews");
    }

    // only comment's author, admin, prj-adm and prj-man can change the comments
    $commentAuthor = $members->getMemberById($commentDetail["newscom_name"]);

    if ($profilSession != "0" && $profilSession != "1" && $profilSession != "5" && $idSession != $commentDetail["newscom_name"]) {
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=commentpermissionNews");
    }

    if ($action == "update") {
        $comment = phpCollab\Util::convertData($request->request->get("comment"));

        $newsDesk->setComment($commentId, $comment);

        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=update");
    } elseif ($action == "delete") {
        // only admin, prj-adm and prj-man can delete a comments
        if ($profilSession != "0" && $profilSession != "1" && $profilSession != "5") {
            phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=commentpermissionNews");
        }

        $commentId = str_replace("**", ",", $request->request->get('id'));

        $newsDesk->deleteNewsDeskComment($commentId);
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=removeComment");
    }
} else { // case of adding new post

    if ($action == "add") {
        //test if name blank
        if (empty($request->request->get('comment'))) {
            $error = $strings["blank_newsdesk_comment"];
        } else {

            //replace quotes by html code in name and address
            $commenterId = phpCollab\Util::convertData($request->request->get('commenterId'));
            $comment = phpCollab\Util::convertData($request->request->get('comment'));
            $postid = phpCollab\Util::convertData($request->request->get('postid'));

            //insert into organizations and redirect to new client organization detail (last id)
            $newsDesk->addComment($postid, $commenterId, $comment);

            phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=add");
        }
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$newsDetail = $newsDesk->getPostById($postid);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/viewnews.php?id=$postid", $newsDetail["news_title"],
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

if ($action != 'remove') {
    if (empty($commentId)) {
        echo <<<FORMSTART
<form id="{$block1->form}Anchor" method="POST" action="../newsdesk/editmessage.php?action=add&" name="ecDForm">
FORMSTART;
        $block1->heading($strings["add_newsdesk_comment"]);
    } else {
        echo <<<FORMSTART
<form id="{$block1->form}Anchor" method="POST" action="../newsdesk/editmessage.php?id={$commentId}&postid={$postid}&action=update&" name="ecDForm">
FORMSTART;
        $block1->heading($strings["edit_newsdesk_comment"] . " : " . $newsDetail["news_title"]);
    }


    $block1->openContent();
    $block1->contentTitle($strings["details"]);

    // add or edit comment
    if (empty($commentId)) {
        $block1->contentRow($strings["author"],
            "<input type='hidden' name='commenterId' value='$idSession'><b>$nameSession</b>");
    } else {
        $block1->contentRow($strings["author"],
            "<input type='hidden' name='commenterId' value='" . $commentDetail["newscom_name"] . "'><b>" . $commentAuthor["mem_name"] . "</b>");
    }

    $block1->contentRow($strings["comment"],
        '<textarea rows="30" name="comment" style="width: 400px;">' . $commentDetail["newscom_comment"] . '</textarea>');
    $block1->contentRow($strings[""],
        '<input type="hidden" name="postid" value="$postid" /><input type="submit" name="submit" value="' . $strings["save"] . '" />  <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

    $block1->closeContent();
    $block1->closeForm();
} else { //remove action

    $block1->form = "saP";
    $block1->openForm("../newsdesk/editmessage.php?action=delete&postid=$postid");

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
        '<input type="hidden" name="id" value="' . $old_id . '"><input type="submit" name="delete" value="' . $strings["delete"] . '"> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

    $block1->closeContent();
    $block1->closeForm();
}

include APP_ROOT . '/themes/' . THEME . '/footer.php';
