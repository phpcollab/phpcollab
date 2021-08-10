<?php
/*
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
*/

$checkSession = "true";
require_once '../includes/library.php';

$bookmarkService = $container->getBookmarksLoader();

$view = $request->query->get('view');

// ** Do the title stuff here **
switch ($view) {
    case 'all':
        $setTitle .= " : " . $strings["bookmark_view_all"];
        break;
    case 'my':
        $setTitle .= " : " . $strings["bookmark_view_my"];
        break;
    case 'private':
        $setTitle .= " : " . $strings["bookmark_view_private"];
        break;
}
// END
include APP_ROOT . '/views/layout/header.php';
$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=all", $strings["bookmarks"],
    'in'));
if ($view == "all") {
    $blockPage->itemBreadcrumbs($strings["bookmarks_all"] . " | " . $blockPage->buildLink("../bookmarks/listbookmarks.php?view=my",
            $strings["my"], 'in') . " | " . $blockPage->buildLink("../bookmarks/listbookmarks.php?view=private",
            $strings["bookmarks_private"], 'in'));
}
if ($view == "my") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=all",
            $strings["bookmarks_all"],
            'in') . " | " . $strings["my"] . " | " . $blockPage->buildLink("../bookmarks/listbookmarks.php?view=private",
            $strings["bookmarks_private"], 'in'));
}
if ($view == "private") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=all",
            $strings["bookmarks_all"], 'in') . " | " . $blockPage->buildLink("../bookmarks/listbookmarks.php?view=my",
            $strings["my"], 'in') . " | " . $strings["bookmarks_private"]);
}
$blockPage->closeBreadcrumbs();


if ($session->getFlashBag()->has('message')) {
    $blockPage->messageBox( $session->getFlashBag()->get('message')[0] );
} else if (!empty($msg)) {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "saJ";
$block1->openForm("../bookmarks/listbookmarks.php?view=$view&project=$project#" . $block1->form . "Anchor", null,
    $csrfHandler);
$block1->heading($strings["bookmarks"]);
$block1->openPaletteIcon();
$block1->paletteIcon(0, "add", $strings["add"]);

if ($view == "my") {
    $block1->paletteIcon(1, "remove", $strings["delete"]);
}

$block1->paletteIcon(5, "info", $strings["view"]);
if ($view == "my") {
    $block1->paletteIcon(6, "edit", $strings["edit"]);
}

$block1->closePaletteIcon();

if ($view == "my") {
    $block1->sorting(
        "bookmarks",
        $sortingUser["bookmarks"],
        "boo.name ASC",
        $sortingFields = [
            0 => "boo.name",
            1 => "boo.category",
            2 => "boo.shared"
        ]
    );
} else {
    $block1->sorting(
        "bookmarks",
        $sortingUser["bookmarks"],
        "boo.name ASC",
        $sortingFields = [
            0 => "boo.name",
            1 => "boo.category",
            2 => "mem.login"
        ]
    );
}

$sorting = $block1->sortingValue;

$bookmarksList = $bookmarkService->getBookmarks($session->get('id'), $view, $sorting);

$bookmarkCount = count($bookmarksList);

if ($bookmarkCount > 0) {
    $block1->openResults();

    if ($view == "my") {
        $block1->labels(
            $labels = [
                0 => $strings["name"],
                1 => $strings["bookmark_category"],
                2 => $strings["shared"]
            ],
            "false"
        );
    } else {
        $block1->labels(
            $labels = [
                0 => $strings["name"],
                1 => $strings["bookmark_category"],
                2 => $strings["owner"]
            ],
            "false"
        );
    }

    foreach ($bookmarksList as $data) {
        $block1->openRow();
        $block1->checkboxRow($data["boo_id"]);

        $block1->cellRow(
            $blockPage->buildLink(
                "../bookmarks/viewbookmark.php?view=$view&id=" . $data["boo_id"],
                $data["boo_name"],
                'in'
            ) . " (" . $blockPage->buildLink(
                $data["boo_url"],
                $strings["url"],
                'out') . ")"
        );
        $block1->cellRow($data["boo_boocat_name"]);

        if ($view == "my") {
            if ($data["boo_shared"] == "1") {
                $printShared = $strings["yes"];
            } else {
                $printShared = $strings["no"];
            }
            $block1->cellRow($printShared);
        } else {
            $block1->cellRow($blockPage->buildLink('../users/viewuser.php?id=' . $data["boo_mem_id"],
                $data["boo_mem_login"], 'in'));
        }
        $block1->closeRow();
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}

$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0, "add", "../bookmarks/editbookmark.php?", "true,false,false", $strings["add"]);
if ($view == "my") {
    $block1->paletteScript(1, "remove", "../bookmarks/deletebookmarks.php?", "false,true,true", $strings["delete"]);
}

$block1->paletteScript(5, "info", "../bookmarks/viewbookmark.php?", "false,true,false", $strings["view"]);
if ($view == "my") {
    $block1->paletteScript(6, "edit", "../bookmarks/editbookmark.php?", "false,true,false", $strings["edit"]);
}
$block1->closePaletteScript(count($bookmarksList), array_column($bookmarksList, 'boo_id'));

include APP_ROOT . '/views/layout/footer.php';

$session->getFlashBag()->clear();
