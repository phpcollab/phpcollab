<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../bookmarks/listbookmarks.php
** Authors: Ceam / Fullo 
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: listbookmarks.php
**
** DESC: Screen: main bookmark page, show allo the bookmarks and categories
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

// ** Do the title stuff here **
switch ($view) {
    case 'all':
        $setTitle .= " : View All Bookmarks";
        break;
    case 'my':
        $setTitle .= " : View My Bookmarks";
        break;
    case 'private':
        $setTitle .= " : View Private Bookmarks";
        break;
}
// END
include '../themes/' . THEME . '/header.php';
$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=all", $strings["bookmarks"], in));
if ($view == "all") {
    $blockPage->itemBreadcrumbs($strings["bookmarks_all"] . " | " . $blockPage->buildLink("../bookmarks/listbookmarks.php?view=my", $strings["my"], in) . " | " . $blockPage->buildLink("../bookmarks/listbookmarks.php?view=private", $strings["bookmarks_private"], in));
}
if ($view == "my") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=all", $strings["bookmarks_all"], in) . " | " . $strings["my"] . " | " . $blockPage->buildLink("../bookmarks/listbookmarks.php?view=private", $strings["bookmarks_private"], in));
}
if ($view == "private") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=all", $strings["bookmarks_all"], in) . " | " . $blockPage->buildLink("../bookmarks/listbookmarks.php?view=my", $strings["my"], in) . " | " . $strings["bookmarks_private"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "saJ";
$block1->openForm("../bookmarks/listbookmarks.php?view=$view&project=$project#" . $block1->form . "Anchor");
$block1->heading($strings["bookmarks"]);
$block1->openPaletteIcon();
$block1->paletteIcon(0, "add", $strings["add"]);

if ($view == "my") {
    $block1->paletteIcon(1, "remove", $strings["delete"]);
}
/*if ($sitePublish == "true") {
    $block1->paletteIcon(3,"add_projectsite",$strings["add_project_site"]);
    $block1->paletteIcon(4,"remove_projectsite",$strings["remove_project_site"]);
}*/
$block1->paletteIcon(5, "info", $strings["view"]);
if ($view == "my") {
    $block1->paletteIcon(6, "edit", $strings["edit"]);
}

$block1->closePaletteIcon();

if ($view == "my") {
    $block1->sorting("bookmarks", $sortingUser->sor_bookmarks[0], "boo.name ASC", $sortingFields = array(0 => "boo.name", 1 => "boo.category", 2 => "boo.shared"));
} else {
    $block1->sorting("bookmarks", $sortingUser->sor_bookmarks[0], "boo.name ASC", $sortingFields = array(0 => "boo.name", 1 => "boo.category", 2 => "mem.login"));
}

if ($view == "my") {
    $tmpquery = "WHERE boo.owner = '$idSession' ORDER BY $block1->sortingValue";
} else if ($view == "private") {
    $tmpquery = "WHERE boo.users LIKE '%|$idSession|%' ORDER BY $block1->sortingValue";
} else {
    $tmpquery = "WHERE boo.shared = '1' OR boo.owner = '$idSession' ORDER BY $block1->sortingValue";
}

$listBookmarks = new phpCollab\Request();
$listBookmarks->openBookmarks($tmpquery);

$comptListBookmarks = count($listBookmarks->boo_id);

if ($comptListBookmarks != "0") {
    $block1->openResults();

    if ($view == "my") {
        $block1->labels($labels = array(0 => $strings["name"], 1 => $strings["bookmark_category"], 2 => $strings["shared"]), "false");
    } else {
        $block1->labels($labels = array(0 => $strings["name"], 1 => $strings["bookmark_category"], 2 => $strings["owner"]), "false");
    }
    for ($i = 0; $i < $comptListBookmarks; $i++) {
        $block1->openRow();
        $block1->checkboxRow($listBookmarks->boo_id[$i]);
        $block1->cellRow($blockPage->buildLink("../bookmarks/viewbookmark.php?view=$view&id=" . $listBookmarks->boo_id[$i], $listBookmarks->boo_name[$i], in) . " " . $blockPage->buildLink($listBookmarks->boo_url[$i], "(" . $strings["url"] . ")", out));
        $block1->cellRow($listBookmarks->boo_boocat_name[$i]);
        if ($view == "my") {
            if ($listBookmarks->boo_shared[$i] == "1") {
                $printShared = $strings["yes"];
            } else {
                $printShared = $strings["no"];
            }
            $block1->cellRow($printShared);
        } else {
            $block1->cellRow($blockPage->buildLink($listBookmarks->boo_mem_email_work[$i], $listBookmarks->boo_mem_login[$i], mail));
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
/*$if ($sitePublish == "true") {
    $block1->paletteScript(3,"add_projectsite","../bookmarks/listbookmarks.php?addToSite=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
    $block1->paletteScript(4,"remove_projectsite","../bookmarks/listbookmarks.php?removeToSite=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
}*/

$block1->paletteScript(5, "info", "../bookmarks/viewbookmark.php?", "false,true,false", $strings["view"]);
if ($view == "my") {
    $block1->paletteScript(6, "edit", "../bookmarks/editbookmark.php?", "false,true,false", $strings["edit"]);
}
$block1->closePaletteScript($comptListBookmarks, $listBookmarks->boo_id);


include '../themes/' . THEME . '/footer.php';

?>