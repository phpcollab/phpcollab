<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../topics/deletetopics.php

$checkSession = "true";
include_once('../includes/library.php');

if ($action == "delete") {
	$id = str_replace("**",",",$id);
	//$tmpquery1 = "DELETE FROM ".$tableCollab["topics"]." WHERE id IN($id)";
	$tmpquery1 = "DELETE FROM ".$tableCollab["topics"]." WHERE id = $id";
	//$tmpquery2 = "DELETE FROM ".$tableCollab["posts"]." WHERE topic IN($id)";
	$tmpquery2 = "DELETE FROM ".$tableCollab["posts"]." WHERE topic = $id";
	$pieces = explode(",",$id);
	$num = count($pieces);
	connectSql("$tmpquery1");
	connectSql("$tmpquery2");
		if ($project != "") {
			headerFunction("../projects/viewproject.php?num=$num&msg=deleteTopic&id=$project&".session_name()."=".session_id());
			exit;
		} else {
			headerFunction("../general/home.php?num=$num&msg=deleteTopic&".session_name()."=".session_id());
			exit;
		}
}
if($_GET['project']){
	$project = $_GET['project'];
} else {
	unset($project);
}
$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
if ($project != "") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=".$projectDetail->pro_id[0],$strings["discussions"],in));
} else {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php?",$strings["home"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?",$strings["my_discussions"],in));
}
$blockPage->itemBreadcrumbs($strings["delete_discussions"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new block();

$block1->form = "saP";
$block1->openForm("../topics/deletetopics.php?project=$project&action=delete&id=$id&".session_name()."=".session_id());

$block1->heading($strings["delete_discussions"]);

$block1->openContent();

$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE topic.id IN($id) ORDER BY topic.subject";
$listTopics = new request();
$listTopics->openTopics($tmpquery);
$comptListTopics = count($listTopics->top_id);

for ($i=0;$i<$comptListTopics;$i++) {
echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$listTopics->top_subject[$i]."</td></tr>";
}

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>