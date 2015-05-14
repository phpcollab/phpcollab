<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/addpost.php

$checkSession = "true";
include_once('../includes/library.php');

$tmpquery = "WHERE topic.id = '$id'";
$detailTopic = new request();
$detailTopic->openTopics($tmpquery);

$tmpquery = "WHERE pro.id = '".$detailTopic->top_project[0]."'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

if ($action == "add") {
	$tpm = Util::convertData($tpm);
	Util::autoLinks($tpm);
	$detailTopic->top_posts[0] = $detailTopic->top_posts[0] + 1;
	$tmpquery1 = "INSERT INTO ".$tableCollab["posts"]."(topic,member,created,message) VALUES('$id','$idSession','$dateheure','$newText')";
	Util::connectSql("$tmpquery1");
	$tmpquery2 = "UPDATE ".$tableCollab["topics"]." SET last_post='$dateheure',posts='".$detailTopic->top_posts[0]."' WHERE id = '$id'";
	Util::connectSql("$tmpquery2");

if ($notifications == "true") {
	include("../topics/noti_newpost.php");
}
	Util::headerFunction("../topics/viewtopic.php?id=$id&msg=add&".session_name()."=".session_id());
}

$idStatus = $detailTopic->top_status[0];
$idPublish = $detailTopic->top_published[0];

$tmpquery = "WHERE pos.topic = '".$detailTopic->top_id[0]."' ORDER BY pos.created DESC";
$listPosts = new request();
$listPosts->openPosts($tmpquery);
$comptListPosts = count($listPosts->pos_id);

if ($projectDetail->pro_org_id[0] == "1") {
	$projectDetail->pro_org_name[0] = $strings["none"];
}

$bodyCommand = "onLoad=\"document.ptTForm.tpm.focus();\"";
include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=".$projectDetail->pro_id[0],$strings["discussions"],in));
$blockPage->itemBreadcrumbs($detailTopic->top_subject[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new block();

$block1->form = "ptT";
$block1->openForm("../topics/addpost.php?action=add&id=".$detailTopic->top_id[0]."&project=".$detailTopic->top_project[0]."&".session_name()."=".session_id());

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["post_to_discussion"]." : ".$detailTopic->top_subject[0]);

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"],$blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0]." (#".$projectDetail->pro_id[0].")",in));
$block1->contentRow($strings["organization"],$projectDetail->pro_org_name[0]);
$block1->contentRow($strings["owner"],$blockPage->buildLink("../users/viewuser.php?id=".$projectDetail->pro_mem_id[0],$projectDetail->pro_mem_name[0],in)." (".$blockPage->buildLink($projectDetail->pro_mem_email_work[0],$projectDetail->pro_mem_login[0],mail).")");

if ($sitePublish == "true") {
	$block1->contentRow($strings["published"],$statusPublish[$idPublish]);
}

$block1->contentRow($strings["retired"],$statusTopicBis[$idStatus]);
$block1->contentRow($strings["posts"],$detailTopic->top_posts[0]);
$block1->contentRow($strings["last_post"],Util::createDate($detailTopic->top_last_post[0],$timezoneSession));

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["message"],"<textarea rows=\"10\" style=\"width: 400px; height: 160px;\" name=\"tpm\" cols=\"47\"></textarea>");
$block1->contentRow("","<input type=\"SUBMIT\" value=\"".$strings["save"]."\">");

$block1->contentTitle($strings["posts"]);

for ($i=0;$i<$comptListPosts;$i++) {
$block1->contentRow($strings["posted_by"],$blockPage->buildLink($listPosts->pos_mem_email_work[$i],$listPosts->pos_mem_name[$i],mail));

if ($listPosts->pos_created[$i] > $lastvisiteSession) {
	$block1->contentRow($strings["when"],"<b>".Util::createDate($listPosts->pos_created[$i],$timezoneSession)."</b>");
} else {
	$block1->contentRow($strings["when"],Util::createDate($listPosts->pos_created[$i],$timezoneSession));
}
$block1->contentRow("",nl2br($listPosts->pos_message[$i]));
$block1->contentRow("","","true");
}
	
$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>