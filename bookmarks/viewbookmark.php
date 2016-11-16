<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../bookmarks/viewbookmark.php
** Authors: Ceam / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewbookmark.php
**
** DESC: Screen: show bookmark details
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**	 
**
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';

$bookmarks = new phpCollab\Bookmarks\Bookmarks();

$bookmarkDetail = $bookmarks->getBookmarkById($id);

if ($bookmarkDetail['boo_users'] != "") {
    $pieces = explode("|", $bookmarkDetail['boo_users']);
    $comptPieces = count($pieces);
    $private = "false";
    for ($i = 0; $i < $comptPieces; $i++) {
        if ($idSession == $pieces[$i]) {
            $private = "true";
        }
    }
}

if (
    ($bookmarkDetail['boo_users'] == "" && $bookmarkDetail['boo_owner'] != $idSession && $bookmarkDetail['boo_shared'] == "0")
    ||
    ($private == "false" && $bookmarkDetail['boo_owner'] != $idSession)
) {
    phpCollab\Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=bookmarkOwner");
}

$setTitle .= " : View Bookmark (" . $bookmarkDetail['boo_name'] . ")";

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=$view", $strings["bookmarks"], 'in'));
$blockPage->itemBreadcrumbs($bookmarkDetail['boo_name']);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "tdD";
$block1->openForm("../bookmarks/viewbookmark.php#" . $block1->form . "Anchor");
$block1->heading($strings["bookmark"] . " : " . $bookmarkDetail['boo_name']);
if ($bookmarkDetail['boo_owner'] == $idSession) {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);

    $block1->paletteIcon(4, "edit", $strings["edit"]);
    $block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["name"], $bookmarkDetail['boo_name']);
$block1->contentRow($strings["url"], $blockPage->buildLink($bookmarkDetail['boo_url'], $bookmarkDetail['boo_url'], 'out'));
$block1->contentRow($strings["description"], nl2br($bookmarkDetail['boo_description']));

$block1->closeContent();
$block1->closeForm();

if ($bookmarkDetail['boo_owner'] == $idSession) {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../bookmarks/deletebookmarks.php?id=" . $bookmarkDetail['boo_id'] . "", "true,true,false", $strings["delete"]);
    $block1->paletteScript(4, "edit", "../bookmarks/editbookmark.php?id=" . $bookmarkDetail['boo_id'] . "", "true,true,false", $strings["edit"]);

    $block1->closePaletteScript("", "");
}

include '../themes/' . THEME . '/footer.php';
