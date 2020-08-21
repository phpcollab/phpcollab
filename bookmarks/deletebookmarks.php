<?php
/*
** Application name: phpCollab
** Path by root: ../bookmarks/deletebookmarks.php
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: deletebookmarks.php
**
** DESC: Screen: remove bookmark from db
**
** =============================================================================
*/

use phpCollab\Bookmarks\DeleteBookmarks;

$checkSession = "true";
include_once '../includes/library.php';


$id = $request->query->get('id');

if (empty($id)) {
    header("Location:../general/permissiondenied.php");
}

if ($request->isMethod('post')) {
    try {

        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "delete") {
                $id = str_replace("**", ",", $id);

                $deleteBookmarks = new DeleteBookmarks();
                try {
                    $deleteBookmarks->delete($id);

                    phpCollab\Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=delete");
                } catch (Exception $exception) {
                    $error = $strings["error_delete_bookmark"];
                }
            }
        }
    } catch (Exception $e) {
        $logger->critical('CSRF Token Error', [
            'delete bookmark' => $request->request->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
        $msg = 'permissiondenied';
    }
}

$bookmarks = new phpCollab\Bookmarks\Bookmarks();

$setTitle .= " : Delete ";

if (strpos($id, "**") !== false) {
    $setTitle .= "Entries";
} else {
    $setTitle .= "Entry";
}
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=all", $strings["bookmarks"], 'in'));
$blockPage->itemBreadcrumbs($strings["delete_bookmarks"]);
$blockPage->closeBreadcrumbs();

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "saP";
$block1->openForm("../bookmarks/deletebookmarks.php?id=$id", null, $csrfHandler);

$block1->heading($strings["delete_bookmarks"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = explode(',', str_replace("**", ",", $id));

$bookmarkList = $bookmarks->getBookmarksInRange($id);

foreach ($bookmarkList as $bookmark) {
    $block1->contentRow("#" . $bookmark['boo_id'], $bookmark['boo_name']);
}

$block1->contentRow("", '<input type="hidden" name="action" value="delete" /><input type="submit" name="delete" value="' . $strings["delete"] . '"> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
