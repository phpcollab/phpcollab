<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../newsdesk/listnews.php

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : News List";

$projects = $container->getProjectsLoader();
$newsDesk = $container->getNewsdeskLoader();
$strings = $GLOBALS['strings'];

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], 'in'));
$blockPage->itemBreadcrumbs($strings["newsdesk_list"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS['msgLabel']);
}

$blockPage->setLimitsNumber(1);

$block1 = new phpCollab\Block();

$block1->form = "newsdeskList";
$block1->openForm("../newsdesk/listnews.php", null, $csrfHandler);

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["newsdesk"]);

$block1->openPaletteIcon();

if ($session->get("profile") == "0" || $session->get("profile") == "1" || $session->get("profile") == "5") {
    $block1->paletteIcon(0, "add", $strings["add_newsdesk"]);
    $block1->paletteIcon(1, "remove", $strings["del_newsdesk"]);
    $block1->paletteIcon(2, "edit", $strings["edit_newsdesk"]);
}
$block1->paletteIcon(3, "info", $strings["view_newsdesk"]);

$block1->closePaletteIcon();

$block1->setLimit($blockPage->returnLimit(1));
$block1->setRowsLimit(40);

$block1->sorting("newsdesk", $sortingUser["newsdesk"], "news.pdate DESC",
    $sortingFields = [0 => "news.title", 1 => "news.pdate", 2 => "news.author"]);

$listPosts = $newsDesk->getAllNewsdeskPosts($block1->sortingValue);
$block1->setRecordsTotal(count($listPosts));

if ($listPosts) {
    $block1->openResults();
    $block1->labels($labels = [
        0 => $strings["topic"],
        1 => $strings["date"],
        2 => $strings["author"],
        3 => $strings["newsdesk_related"]
    ], "true");

    foreach ($listPosts as $post) {
        // take the news author
        $newsAuthor = $members->getMemberById($post['news_author']);

        // take the name of the related article
        if ($post['news_related'] != 'g') {
            $projectDetail = $projects->getProjectById($post['news_related']);
            $article_related = "<a href='../projects/viewproject.php?id=" . $projectDetail["pro_id"] . "' title='" . $projectDetail["pro_name"] . "'>" . $escaper->escapeHtml($projectDetail["pro_name"]) . "</a>";
        } else {
            $article_related = $strings["newsdesk_related_generic"];
        }

        $block1->openRow();
        $block1->checkboxRow($post['news_id']);
        $block1->cellRow($blockPage->buildLink("../newsdesk/viewnews.php?id=" . $post['news_id'],
            $escaper->escapeHtml($post['news_title']), 'in'));
        $block1->cellRow($post['news_date']);
        $block1->cellRow($newsAuthor["mem_name"]);
        $block1->cellRow($article_related);
        $block1->closeRow();
    }

    $block1->closeResults();

    $block1->limitsFooter("1", $blockPage->getLimitsNumber(), "", "");
} else {
    $block1->noresults();
}

$block1->closeFormResults();

$block1->openPaletteScript();
if ($session->get("profile") == "0" || $session->get("profile") == "1" || $session->get("profile") == "5") {
    $block1->paletteScript(0, "add", "../newsdesk/addnews.php", "true,false,false", $strings["add_newsdesk"]);
    $block1->paletteScript(1, "remove", "../newsdesk/deletenews.php?", "false,true,true",
        $strings["del_newsdesk"]);
    $block1->paletteScript(2, "edit", "../newsdesk/editnews.php?", "false,true,false", $strings["edit_newsdesk"]);
}
$block1->paletteScript(3, "info", "../newsdesk/viewnews.php?", "false,true,false", $strings["view_newsdesk"]);

$block1->closePaletteScript(count($listPosts), array_column($listPosts, 'news_id'));
include APP_ROOT . '/views/layout/footer.php';
