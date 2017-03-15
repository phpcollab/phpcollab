<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../newsdesk/viewnews.php

$checkSession = "true";
include_once '../includes/library.php';

$members = new \phpCollab\Members\Members();
$projects = new \phpCollab\Projects\Projects();

$tmpquery = "WHERE news.id = '" . phpCollab\Util::fixInt($id) . "'";
$newsDetail = new phpCollab\Request();
$newsDetail->openNewsDesk($tmpquery);
$comptNewsDetail = count($newsDetail->news_id);

if ($comptNewsDetail == "0") {
    phpCollab\Util::headerFunction("../newsdesk/listnews.php?msg=blankNews");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], in));
$blockPage->itemBreadcrumbs($newsDetail->news_title[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->limitsNumber = "1";


$block1 = new phpCollab\Block();

$block1->form = "clPr";
$block1->openForm("../newsdesk/viewnews.php?&id=$id#" . $block1->form . "Anchor");

$block1->headingToggle($strings["newsdesk"]);


if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "add", $strings["add_newsdesk"]);
    $block1->paletteIcon(1, "remove", $strings["del_newsdesk"]);
    $block1->paletteIcon(3, "edit", $strings["edit_newsdesk"]);
    $block1->closePaletteIcon();
}


if ($comptNewsDetail != "0") {

    // take the news author
    $newsAuthor = $members->getMemberById($newsDetail->news_author[0]);

    $block1->openContent();
    $block1->contentTitle($strings["details"]);
    $block1->contentRow("<b>" . $strings["title"] . "</b>", $newsDetail->news_title[0]);
    $block1->contentRow("<b>" . $strings["author"] . "</b>", $newsAuthor["mem_name"]);
    $block1->contentRow("<b>" . $strings["date"] . "</b>", $newsDetail->news_date[0]);

    if ($newsDetail->news_related[0] != 'g') {
        $projectDetail = $projects->getProjectById($newsDetail->news_related[0]);
        $article_related = "<a href='../projects/viewproject.php?id=" . $projectDetail["pro_id"] . "' title='" . $projectDetail["pro_name"] . "'>" . $projectDetail["pro_name"] . "</a>";
    } else {
        $article_related = $strings["newsdesk_related_generic"];
    }

    $block1->contentRow("<b>" . $strings["newsdesk_related"] . "</b>", $article_related);
    $block1->contentRow("<b>" . stripslashes($strings["article_newsdesk"]) . "</b>", $newsDetail->news_content[0]);

    $newsLinksArray = explode(";", trim($newsDetail->news_links[0]));
    foreach ($newsLinksArray as $item) {
        $article_links .= "<a href='" . trim($item) . "' title='$item' target='_blank'>$item</a><br/>";
    }

    $block1->contentRow("<b>" . $strings["newsdesk_related_links"] . "</b>", $article_links);

    if ($newsDetail->news_rss[0] != '0') {
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

if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteScript(0, "add", "../newsdesk/editnews.php?", "true,true,true", $strings["add_newsdesk"]);
    $block1->paletteScript(1, "remove", "../newsdesk/editnews.php?action=remove&id=$id", "true,false,true", $strings["del_newsdesk"]);
    $block1->paletteScript(3, "edit", "../newsdesk/editnews.php?id=$id", "true,true,true", $strings["edit_newsdesk"]);
}

$block1->closePaletteScript($comptNewsDetail, $newsDetail->news_id);

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// comments block //////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

$tmpquery = "WHERE newscom.post_id = '" . phpCollab\Util::fixInt($id) . "'";
$newsComments = new phpCollab\Request();
$newsComments->openNewsDeskComments($tmpquery);
$comptCommentsDetail = count($newsComments->newscom_id);


$block2 = new phpCollab\Block();

$block2->form = "clPrc";
$block2->openForm("../newsdesk/viewnews.php?&id=$id#" . $block2->form . "Anchor");

$block2->headingToggle($strings["comments"]);

$block2->openPaletteIcon();
$block2->paletteIcon(0, "add", $strings["add_newsdesk_comment"]);
$block2->paletteIcon(1, "edit", $strings["edit_newsdesk_comment"]);
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block2->paletteIcon(2, "remove", $strings["del_newsdesk_comment"]);
}

$block2->closePaletteIcon();

$block1->limit = $blockPage->returnLimit("2");
$block2->openContent();

if ($comptCommentsDetail != "0") {
    $block2->openResults();
    $block2->labels($labels = array(0 => $strings["name"], 1 => $strings["comment"]), "true");

    for ($i = 0; $i < $comptCommentsDetail; $i++) {
        $newsAuthor = $members->getMemberById($newsDetail->news_author[$i]);

        $block2->openRow();
        $block2->checkboxRow($newsComments->newscom_id[$i]);
        $block2->cellRow($newsAuthor["mem_name"]);
        $block2->cellRow($newsComments->newscom_comment[$i]);
        $block2->closeRow();
    }

    $block2->closeResults();
    $block2->limitsFooter("1", $blockPage->limitssNumber, "", "");

} else {
    $block2->noresults();
}

$block2->closeToggle();
$block2->closeFormResults();

$block2->closeContent();

$block2->openPaletteScript();

$block2->paletteScript(0, "add", "../newsdesk/editmessage.php?postid=$id&", "true,false,false", $strings["add_newsdesk_comment"]);
$block2->paletteScript(1, "edit", "../newsdesk/editmessage.php?postid=$id&", "false,true,false", $strings["edit_newsdesk_comment"]);

if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block2->paletteScript(2, "remove", "../newsdesk/editmessage.php?postid=$id&action=remove&", "false,true,true", $strings["del_newsdesk_comment"]);
}

$block2->closePaletteScript($comptCommentsDetail, $newsComments->newscom_id);

include APP_ROOT . '/themes/' . THEME . '/footer.php';
