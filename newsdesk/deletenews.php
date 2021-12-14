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
require_once '../includes/library.php';

if ($session->get("profile") != "0" && $session->get("profile") != "1" && $session->get("profile") != "5") {
    $session->getFlashBag()->add(
        'message',
        $strings["errorpermission_newsdesk"]
    );

    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $id);
}

try {
    $news = $container->getNewsdeskLoader();
    $projects = $container->getProjectsLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            $id = $request->request->get("id");
            $news->deleteCommentByPostId($id);
            $news->deleteNewsDeskPost($id);

            $session->getFlashBag()->add(
                'message',
                $strings["newsdesk_item_remove"]
            );

            phpCollab\Util::headerFunction("../newsdesk/listnews.php");
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Newsdesk: Edit News' => $request->query->get("id"),
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


$id = $request->query->get('id');

if (!empty($id)) {
    $newsDetail = $news->getPostById($id);

    if ($session->get("profile") != "0" && $session->get("id") != $newsDetail['news_author']) {
        $session->getFlashBag()->add(
            'message',
            $strings["errorpermission_newsdesk"]
        );
        phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $newsDetail['news_id']);
    }
}

if (empty($id) || !$newsDetail) {
    $session->getFlashBag()->add(
        'message',
        $strings["newsdesk_item_blank"]
    );
    phpCollab\Util::headerFunction("../newsdesk/listnews.php");
}

//** Title stuff here.. **
$setTitle .= " : " . $strings["del_newsdesk"];

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], 'in'));

$blockPage->itemBreadcrumbs($strings["edit_newsdesk"]);

$blockPage->closeBreadcrumbs();


if ($session->getFlashBag()->has('message')) {
    $blockPage->messageBox( $session->getFlashBag()->get('message')[0] );
} else if ($msg != "") {
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
