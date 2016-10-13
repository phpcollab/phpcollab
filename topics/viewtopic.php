<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/viewtopic.php

$checkSession = "true";
include_once '../includes/library.php';

if ($_GET['action'] == "closeTopic") {
    $tmpquery1 = "UPDATE " . $tableCollab["topics"] . " SET status='0' WHERE id = '" . $_GET['id'] . "'";
    $num = "1";
    phpCollab\Util::connectSql("$tmpquery1");
    $msg = "closeTopic";
}

if ($_GET['action'] == "addToSite") {
    $tmpquery1 = "UPDATE " . $tableCollab["topics"] . " SET published='0' WHERE id = '" . $_GET['id'] . "'";
    phpCollab\Util::connectSql("$tmpquery1");
    $msg = "addToSite";
}

if ($_GET['action'] == "removeToSite") {
    $tmpquery1 = "UPDATE " . $tableCollab["topics"] . " SET published='1' WHERE id = '" . $_GET['id'] . "'";
    phpCollab\Util::connectSql("$tmpquery1");
    $msg = "removeToSite";
}

$tmpquery = "WHERE topic.id = '$id'";
$detailTopic = new phpCollab\Request();
$detailTopic->openTopics($tmpquery);

$tmpquery = "WHERE pos.topic = '" . $detailTopic->top_id[0] . "' ORDER BY pos.created DESC";
$listPosts = new phpCollab\Request();
$listPosts->openPosts($tmpquery);
$comptListPosts = count($listPosts->pos_id);

$tmpquery = "WHERE pro.id = '" . $detailTopic->top_project[0] . "'";
$detailProject = new phpCollab\Request();
$detailProject->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '" . $detailTopic->top_project[0] . "' AND tea.member = '$idSession'";
$memberTest = new phpCollab\Request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);
if ($comptMemberTest == "0") {
    $teamMember = "false";
} else {
    $teamMember = "true";
}

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

if ($detailProject->pro_org_id[0] == "1") {
    $detailProject->pro_org_name[0] = $strings["none"];
}

$idStatus = $detailTopic->top_status[0];
$idPublish = $detailTopic->top_published[0];

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $detailProject->pro_id[0], $detailProject->pro_name[0], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=" . $detailProject->pro_id[0], $strings["discussions"], in));
$blockPage->itemBreadcrumbs($detailTopic->top_subject[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "tdP";
$block1->openForm("");

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["discussion"] . " : " . $detailTopic->top_subject[0]);

if ($idSession == $detailTopic->top_owner[0]) {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["remove"]);
    $block1->paletteIcon(1, "lock", $strings["close"]);
    $block1->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
    $block1->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
    $block1->closePaletteIcon();
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $detailProject->pro_id[0], $detailProject->pro_name[0] . " (#" . $detailProject->pro_id[0] . ")", in));
$block1->contentRow($strings["organization"], $detailProject->pro_org_name[0]);
$block1->contentRow($strings["owner"], $blockPage->buildLink("../users/viewuser.php?id=" . $detailProject->pro_mem_id[0], $detailProject->pro_mem_name[0], in) . " (" . $blockPage->buildLink($detailProject->pro_mem_email_work[0], $detailProject->pro_mem_login[0], mail) . ")");

if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $statusPublish[$idPublish]);
}

$block1->contentRow($strings["retired"], $statusTopicBis[$idStatus]);
$block1->contentRow($strings["posts"], $detailTopic->top_posts[0]);
$block1->contentRow($strings["last_post"], phpCollab\Util::createDate($detailTopic->top_last_post[0], $timezoneSession));

$block1->contentTitle($strings["posts"]);

if ($detailTopic->top_status[0] == "1" && $teamMember == "true") {
    $block1->contentRow("", $blockPage->buildLink("../topics/addpost.php?id=" . $detailTopic->top_id[0], $strings["post_reply"], in));
}

for ($i = 0; $i < $comptListPosts; $i++) {
    $block1->contentRow($strings["posted_by"], $blockPage->buildLink($listPosts->pos_mem_email_work[$i], $listPosts->pos_mem_name[$i], mail));

    if ($listPosts->pos_created[$i] > $lastvisiteSession) {
        $block1->contentRow($strings["when"], "<b>" . phpCollab\Util::createDate($listPosts->pos_created[$i], $timezoneSession) . "</b>");
    } else {
        $block1->contentRow($strings["when"], phpCollab\Util::createDate($listPosts->pos_created[$i], $timezoneSession));
    }
    if ($detailProject->pro_owner[0] == $idSession || $profileSession == "0" || $listPosts->pos_member[$i] == $idSession) {
        $block1->contentRow($blockPage->buildLink("../topics/deletepost.php?topic=" . $detailTopic->top_id[0] . "&id=" . $listPosts->pos_id[$i], $strings["delete_message"], in), nl2br($listPosts->pos_message[$i]));
    } else {
        $block1->contentRow("", nl2br($listPosts->pos_message[$i]));
    }
    $block1->contentRow("", "", "true");
}

$block1->closeContent();
$block1->closeForm();

if ($idSession == $detailTopic->top_owner[0]) {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../topics/deletetopics.php?project=" . $detailTopic->top_project[0] . "&id=" . $detailTopic->top_id[0] . "", "true,true,false", $strings["remove"]);
    $block1->paletteScript(1, "lock", "../topics/viewtopic.php?id=" . $detailTopic->top_id[0] . "&action=closeTopic", "true,true,false", $strings["close"]);
    $block1->paletteScript(2, "add_projectsite", "../topics/viewtopic.php?id=" . $detailTopic->top_id[0] . "&action=addToSite", "true,true,false", $strings["add_project_site"]);
    $block1->paletteScript(3, "remove_projectsite", "../topics/viewtopic.php?id=" . $detailTopic->top_id[0] . "&action=removeToSite", "true,true,false", $strings["remove_project_site"]);
    $block1->closePaletteScript("", "");
}

include '../themes/' . THEME . '/footer.php';
?>