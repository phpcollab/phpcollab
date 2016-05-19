<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

	if ($action == "add") {
		$topicField = Util::convertData($topicField);
		$messageField = Util::convertData($messageField);
		$tmpquery1 = "INSERT INTO ".$tableCollab["topics"]."(project,owner,subject,status,last_post,posts,published) VALUES('$projectSession','$idSession','$topicField','1','$dateheure','1','0')";
		Util::connectSql("$tmpquery1");
$tmpquery = $tableCollab["topics"];
Util::getLastId($tmpquery);
$num = $lastId[0];
unset($lastId);
Util::autoLinks($messageField);
	$tmpquery2 = "INSERT INTO ".$tableCollab["posts"]."(topic,member,created,message) VALUES('$num','$idSession','$dateheure','$newText')";
	Util::connectSql("$tmpquery2");

if ($notifications == "true") {
$tmpquery = "WHERE pro.id = '$projectSession'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

	include '../topics/noti_newtopic.php';
}
		Util::headerFunction("showallthreadtopics.php");
	}

$bodyCommand="onload=\"document.createThreadTopic.topicField.focus();\"";

$bouton[5] = "over";
$titlePage = $strings["create_topic"];
include 'include_header.php';

echo "<form accept-charset=\"UNKNOWN\" method=\"post\" action=\"../projects_site/createthread.php?project=$projectSession&action=add&id=$id\" name=\"createThreadTopic\" enctype=\"application/x-www-form-urlencoded\">";

echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\">
<tr><th colspan=\"2\">".$strings["create_topic"]."</th></tr>
<tr><th>*&nbsp;".$strings["topic"]." :</th><td><input size=\"35\" value=\"$topicField\" name=\"topicField\" type=\"text\"></td></tr>
<tr><th colspan=\"2\">".$strings["enter_message"]."</th></tr>
<tr><th>*&nbsp;".$strings["message"]." :</th><td colspan=\"2\"><textarea rows=\"3\" name=\"messageField\" cols=\"43\"></textarea></td></tr>
<tr><th>&nbsp;</th><td colspan=\"2\"><input name=\"submit\" type=\"submit\" value=\"".$strings["save"]."\"></td></tr>
</table>
</form>";

include ("include_footer.php");
?>