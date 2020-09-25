<?php
/*
** Application name: phpCollab
** Path by root: ../newsdesk/editnews.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: deletenews.php
**
** =============================================================================
*/

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

if ($session->get("profile") != "0" && $session->get("profile") != "1" && $session->get("profile") != "5") {
    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$id&msg=permissionNews");
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            $id = $request->request->get("id");
            $news->deleteCommentByPostId($id);
            $news->deleteNewsDeskPost($id);
            phpCollab\Util::headerFunction("../newsdesk/listnews.php?msg=removeNews");
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Newsdesk: Edit News' => $request->query->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

$news = $container->getNewsdeskLoader();
$projects = $container->getProjectsLoader();

$id = $request->query->get('id');

if (!empty($id)) {
    $newsDetail = $news->getPostById($id);

    if ($session->get("profile") != "0" && $session->get("id") != $newsDetail['news_author']) {
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id={$n['id']}&msg=permissionNews");
    }
}

if (empty($id) || !$newsDetail) {
    phpCollab\Util::headerFunction("../newsdesk/listnews.php?msg=blankNews");
}

//** Title stuff here.. **
$setTitle .= " : Remove News Item";

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], 'in'));

$blockPage->itemBreadcrumbs($strings["edit_newsdesk"]);

$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

/**
 * remove action
 */
$block1->form = "saP";
$block1->openForm("../newsdesk/deletenews.php", null, $csrfHandler);

$block1->heading($strings["del_newsdesk"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$block1->contentRow("#" . $newsDetail['news_id'], $newsDetail['news_title']);

$block1->contentRow("",
    "<input type='hidden' name='id' value='" . $newsDetail['news_id'] . "'><input type='submit' name='delete' value='" . $strings["delete"] . "'> <input type='button' name='cancel' value='" . $strings["cancel"] . "' onClick='history.back();'>");

$block1->closeContent();
$block1->closeForm();

$block1->note($strings["delete_news_note"]);


include APP_ROOT . '/views/layout/footer.php';
