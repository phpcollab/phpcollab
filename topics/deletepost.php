<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/deletepost.php

$checkSession = "true";
include_once '../includes/library.php';

$tmpquery = "WHERE topic.id = '$topic'";
$detailTopic = new Request();
$detailTopic->openTopics($tmpquery);

if ($action == "delete") {
	$detailTopic->top_posts[0] = $detailTopic->top_posts[0] - 1;
	$tmpquery = "DELETE FROM ".$tableCollab["posts"]." WHERE id = '$id'";
	Util::connectSql("$tmpquery");
	$tmpquery2 = "UPDATE ".$tableCollab["topics"]." SET posts='".$detailTopic->top_posts[0]."' WHERE id = '$topic'";
	Util::connectSql("$tmpquery2");
	Util::headerFunction("../topics/viewtopic.php?msg=delete&id=$topic&".session_name()."=".session_id());
	exit;
}

$tmpquery = "WHERE pos.id = '$id'";
$detailPost = new Request();
$detailPost->openPosts($tmpquery);

include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$detailTopic->top_pro_id[0],$detailTopic->top_pro_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?topic=".$detailTopic->top_id[0],$strings["discussion"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/viewtopic.php?id=".$detailTopic->top_id[0],$detailTopic->top_subject[0],in));
$blockPage->itemBreadcrumbs($strings["delete_messages"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "saP";
$block1->openForm("../topics/deletepost.php?id=$id&topic=$topic&action=delete&".session_name()."=".session_id());

$block1->heading($strings["delete_messages"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".nl2br($detailPost->pos_message[0])."</td>

<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>