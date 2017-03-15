<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../newsdesk/listnews.php

$checkSession = "true";
include_once '../includes/library.php';

$setTitle .= " : News List";

$members = new \phpCollab\Members\Members();
$projects = new \phpCollab\Projects\Projects();

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], in));
$blockPage->itemBreadcrumbs($strings["newsdesk_list"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->limitsNumber = "1";

$block1 = new phpCollab\Block();

$block1->form = "newsdeskList";
$block1->openForm("../newsdesk/listnews.php#" . $block1->form . "Anchor");

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["newsdesk"]);

$block1->openPaletteIcon();

if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteIcon(0, "add", $strings["add_newsdesk"]);
    $block1->paletteIcon(1, "remove", $strings["del_newsdesk"]);
    $block1->paletteIcon(2, "edit", $strings["edit_newsdesk"]);
}
$block1->paletteIcon(3, "info", $strings["view_newsdesk"]);

$block1->closePaletteIcon();

$block1->limit = $blockPage->returnLimit("1");
$block1->rowsLimit = "40";

$block1->sorting("newsdesk", $sortingUser->sor_newsdesk[0], "news.pdate DESC", $sortingFields = array(0 => "news.title", 1 => "news.pdate"));

$block1->openContent();

$tmpquery = "WHERE news.id != '0' ORDER BY $block1->sortingValue ";
$block1->recordsTotal = phpCollab\Util::computeTotal($initrequest["newsdeskposts"] . " " . $tmpquery);

$listPosts = new phpCollab\Request();
$listPosts->openNewsDesk($tmpquery, $block1->limit, $block1->rowsLimit);
$comptPosts = count($listPosts->news_id);

if ($comptPosts != "0") {
    $block1->openResults();
    $block1->labels($labels = array(0 => $strings["topic"], 1 => $strings["date"], 2 => $strings["author"], 3 => $strings["newsdesk_related"]), "true");

    for ($i = 0; $i < $comptPosts; $i++) {
        // take the news author
        $newsAuthor = $members->getMemberById($listPosts->news_author[$i]);

        // take the name of the related article
        if ($listPosts->news_related[$i] != 'g') {
            $projectDetail = $projects->getProjectById($listPosts->news_related[$i]);
            $article_related = "<a href='../projects/viewproject.php?id=" . $projectDetail["pro_id"] . "' title='" . $projectDetail["pro_name"] . "'>" . $projectDetail["pro_name"] . "</a>";
        } else {
            $article_related = $strings["newsdesk_related_generic"];
        }


        $block1->openRow();
        $block1->checkboxRow($listPosts->news_id[$i]);
        $block1->cellRow($blockPage->buildLink("../newsdesk/viewnews.php?id=" . $listPosts->news_id[$i], $listPosts->news_title[$i], in));
        $block1->cellRow($listPosts->news_date[$i]);
        $block1->cellRow($newsAuthor["mem_name"]);
        $block1->cellRow($article_related);
        $block1->closeRow();
    }

    $block1->closeResults();

    $block1->limitsFooter("1", $blockPage->limitsNumber, "", "");
} else {
    $block1->noresults();
}

$block1->closeFormResults();

$block1->openPaletteScript();
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteScript(0, "add", "../newsdesk/editnews.php?", "true,false,false", $strings["add_newsdesk"]);
    $block1->paletteScript(1, "remove", "../newsdesk/editnews.php?action=remove&", "false,true,true", $strings["del_newsdesk"]);
    $block1->paletteScript(2, "edit", "../newsdesk/editnews.php?", "false,true,false", $strings["edit_newsdesk"]);
}
$block1->paletteScript(3, "info", "../newsdesk/viewnews.php?", "false,true,false", $strings["view_newsdesk"]);

$block1->closePaletteScript($comptPosts, $listPosts->news_id);
include '../themes/' . THEME . '/footer.php';


?>