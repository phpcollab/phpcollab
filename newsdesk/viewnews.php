<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../newsdesk/viewnews.php

use phpCollab\NewsDesk\NewsDesk;
use phpCollab\Projects\Projects;

$checkSession = "true";
include_once '../includes/library.php';

$projects = new Projects();
$news = new NewsDesk();

$newsDetail = $news->getPostById($request->query->get("id"));

if (!$newsDetail) {
    phpCollab\Util::headerFunction("../newsdesk/listnews.php?msg=blankNews");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], "in"));
$blockPage->itemBreadcrumbs($newsDetail->news_title[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->setLimitsNumber(1);


$block1 = new phpCollab\Block();

$block1->form = "clPr";
$block1->openForm("../newsdesk/viewnews.php?&id=" . $request->query->get("id"). "#" . $block1->form . "Anchor");

$block1->headingToggle($strings["newsdesk"]);


if ($session->get("profilSession") == "0" || $session->get("profilSession") == "1" || $session->get("profilSession") == "5") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "add", $strings["add_newsdesk"]);
    $block1->paletteIcon(1, "remove", $strings["del_newsdesk"]);
    $block1->paletteIcon(3, "edit", $strings["edit_newsdesk"]);
    $block1->closePaletteIcon();
}


if ($newsDetail) {

    // take the news author
    $newsAuthor = $members->getMemberById($newsDetail['news_author']);

    $block1->openContent();
    $block1->contentTitle($strings["details"]);
    $block1->contentRow("<b>" . $strings["title"] . "</b>", $escaper->escapeHtml($newsDetail['news_title']));
    $block1->contentRow("<b>" . $strings["author"] . "</b>", $escaper->escapeHtml($newsAuthor["mem_name"]));
    $block1->contentRow("<b>" . $strings["date"] . "</b>", $escaper->escapeHtml($newsDetail['news_date']));

    if ($newsDetail['news_related'] != 'g') {
        $projectDetail = $projects->getProjectById($newsDetail['news_related']);
        $article_related = "<a href='../projects/viewproject.php?id=" . $projectDetail["pro_id"] . "' title='" . $projectDetail["pro_name"] . "'>" . $projectDetail["pro_name"] . "</a>";
    } else {
        $article_related = $strings["newsdesk_related_generic"];
    }

    $block1->contentRow("<b>" . $strings["newsdesk_related"] . "</b>", $escaper->escapeHtml($article_related));
    $block1->contentRow("<b>" . stripslashes($strings["article_newsdesk"]) . "</b>", $escaper->escapeHtml($newsDetail['news_content']));

    $newsLinksArray = explode(";", trim($newsDetail['news_links']));
    foreach ($newsLinksArray as $item) {
        $item = $escaper->escapeHtml($item);
        $article_links .= "<a href='" . trim($item) . "' title='$item' target='_blank'>$item</a><br/>";
    }

    $block1->contentRow("<b>" . $strings["newsdesk_related_links"] . "</b>", $article_links);

    if ($newsDetail['news_rss'] != '0') {
        $article_rss = $strings["yes"];
    } else {
        $article_rss = $strings["no"];
    }

    $block1->contentRow("<b>" . $strings["newsdesk_rss_enabled"] . "</b>", $article_rss);
    $block1->closeContent();
} else {
    $block1->noresults();
}

$block1->closeToggle();
$block1->closeFormResults();

$block1->openPaletteScript();

if ($session->get("profilSession") == "0" || $session->get("profilSession") == "1" || $session->get("profilSession") == "5") {
    $block1->paletteScript(0, "add", "../newsdesk/editnews.php?", "true,true,true", $strings["add_newsdesk"]);
    $block1->paletteScript(1, "remove", "../newsdesk/editnews.php?action=remove&id=" . $request->query->get("id"), "true,false,true", $strings["del_newsdesk"]);
    $block1->paletteScript(3, "edit", "../newsdesk/editnews.php?id=" . $request->query->get("id"), "true,true,true", $strings["edit_newsdesk"]);
}

$block1->closePaletteScript(count($newsDetail), $newsDetail['news_id']);

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// comments block //////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

$newsComments = $news->getCommentsByPostId($request->query->get("id"));

$block2 = new phpCollab\Block();

$block2->form = "clPrc";
$block2->openForm("../newsdesk/viewnews.php?&id=" . $request->query->get("id") . "#" . $block2->form . "Anchor");

$block2->headingToggle($strings["comments"]);

$block2->openPaletteIcon();
$block2->paletteIcon(0, "add", $strings["add_newsdesk_comment"]);
$block2->paletteIcon(1, "edit", $strings["edit_newsdesk_comment"]);
if ($session->get("profilSession") == "0" || $session->get("profilSession") == "1" || $session->get("profilSession") == "5") {
    $block2->paletteIcon(2, "remove", $strings["del_newsdesk_comment"]);
}

$block2->closePaletteIcon();

$block1->setLimit($blockPage->returnLimit(2));
$block2->openContent();

if ($newsComments) {
    $block2->openResults();
    $block2->labels($labels = array(0 => $strings["name"], 1 => $strings["comment"]), "true");

    foreach ($newsComments as $comment) {
        $newsAuthor = $members->getMemberById($comment['newscom_name']);

        $block2->openRow();
        $block2->checkboxRow($escaper->escapeHtml($comment['newscom_id']));
        $block2->cellRow($escaper->escapeHtml($newsAuthor["mem_name"]));
        $block2->cellRow($escaper->escapeHtml($comment['newscom_comment']));
        $block2->closeRow();
    }

    $block2->closeResults();
    $block2->limitsFooter("1", $blockPage->getLimitsNumber(), "", "");
} else {
    $block2->noresults();
}

$block2->closeToggle();
$block2->closeFormResults();

$block2->closeContent();

$block2->openPaletteScript();

$block2->paletteScript(0, "add", "../newsdesk/editmessage.php?postid=" . $request->query->get("id"), "true,false,false", $strings["add_newsdesk_comment"]);
$block2->paletteScript(1, "edit", "../newsdesk/editmessage.php?postid=" . $request->query->get("id"), "false,true,false", $strings["edit_newsdesk_comment"]);

if ($session->get("profilSession") == "0" || $session->get("profilSession") == "1" || $session->get("profilSession") == "5") {
    $block2->paletteScript(2, "remove", "../newsdesk/editmessage.php?postid=" . $request->query->get("id") . "&action=remove&", "false,true,true", $strings["del_newsdesk_comment"]);
}

$block2->closePaletteScript(count($newsComments), array_column($newsComments, 'newscom_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
