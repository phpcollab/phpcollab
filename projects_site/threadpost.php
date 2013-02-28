<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include("../includes/library.php");

$tmpquery = "WHERE topic.id = '$id'";
$detailTopic = new request();
$detailTopic->openTopics($tmpquery);

if ($detailTopic->top_published[0] == "1" || $detailTopic->top_project[0] != $projectSession) {
headerFunction("index.php");
}

if ($action == "add") {
$detailTopic->top_posts[0] = $detailTopic->top_posts[0] + 1;
$messageField = convertData($messageField);
autoLinks($messageField);
		$tmpquery1 = "INSERT INTO ".$tableCollab["posts"]."(topic,member,created,message) VALUES('$id','$idSession','$dateheure','$newText')";
		connectSql("$tmpquery1");
		$tmpquery2 = "UPDATE ".$tableCollab["topics"]." SET last_post='$dateheure',posts='".$detailTopic->top_posts[0]."' WHERE id = '$id'";
		connectSql("$tmpquery2");

if ($notifications == "true") {
$tmpquery = "WHERE pro.id = '$projectSession'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

	include("../topics/noti_newpost.php");
}
		//headerFunction("showallthreads.php?id=$id&".session_name()."=".session_id());
}

$bouton[5] = "over";
$titlePage = $strings["post_reply"];
include ("include_header.php");

$idStatus = $detailTopic->top_status[0];

echo "<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"../projects_site/threadpost.php?".session_name()."=".session_id()."&action=add\" name=\"post\" enctype=\"application/x-www-form-urlencoded\"><input name=\"id\" type=\"hidden\" value=\"$id\">";

echo "<table cellspacing=\"0\" width=\"90%\" cellpadding=\"3\">
<tr><th colspan=\"4\">".$detailTopic->top_subject[0]."</th></tr>
<tr><th colspan=\"4\">".$strings["information"]."</th></tr>
<tr><th>".$strings["project"].":</th><td>".$projectDetail->pro_name[0]."</td><th>".$strings["posts"].":</th><td>".$detailTopic->top_posts[0]."</td></tr>
<tr><th>&nbsp;</th><td>&nbsp;</td><th>".$strings["last_post"].":</th><td>".createDate($detailTopic->top_last_post[0],$timezoneSession)."</td></tr>
<tr><th>&nbsp;</th><td>&nbsp;</td><th>".$strings["retired"].":</th><td>$statusTopicBis[$idStatus]</td></tr>
<tr><th>".$strings["owner"].":</th><td colspan=\"3\"><a href=\"mailto:".$detailTopic->top_mem_email_work[0]."\">".$detailTopic->top_mem_login[0]."</a></td></tr>
<tr><td colspan=\"4\">&nbsp;</td></tr>
<tr><th colspan=\"4\">".$strings["enter_message"]."</th></tr>
<tr><th nowrap>*&nbsp;".$strings["message"].":</th><td colspan=\"3\"><textarea cols=\"60\" name=\"messageField\" rows=\"6\"></textarea></td></tr>
<tr><td class=\"FormLabel\">&nbsp;</td><td colspan=\"3\"><input name=\"submit\" type=\"submit\" value=\"".$strings["save"]."\"></td></tr>
</form>";

$tmpquery = "WHERE pos.topic = '".$detailTopic->top_id[0]."' ORDER BY pos.created DESC";
$listPosts = new request();
$listPosts->openPosts($tmpquery);
$comptListPosts = count($listPosts->pos_id);

if ($comptListPosts != "0") {
for ($i=0;$i<$comptListPosts;$i++) {
	if (!($i%2)) {
		$class = "odd";
	} else {
		$class = "even";
	}
echo "<tr><td colspan=\"4\" class=\"$class\">&nbsp;</td></tr>
<tr class=\"$class\"><th>".$strings["posted_by"]." :</th><td>".$listPosts->pos_mem_name[$i]."</td><td colspan=\"2\" align=\"right\"><a href=\"../projects_site/showallthreads.php?id=$id&action=delete&post=".$listPosts->pos_id[$i]."\">".$strings["delete_message"]."</a></td></tr>
<tr class=\"$class\"><th>".$strings["email"]." :</th><td colspan=\"3\"><a href=\"mailto:".$listPosts->pos_mem_email_work[$i]."\">".$listPosts->pos_mem_email_work[$i]."</a></td></tr>
<tr class=\"$class\"><th nowrap>".$strings["when"]." :</th><td colspan=\"3\">".createDate($listPosts->pos_created[$i],$timezoneSession)."</td></tr>
<tr class=\"$class\"><th>".$strings["message"]." :</th><td colspan=\"3\">".nl2br($listPosts->pos_message[$i])."</td></tr>";
}
} else {
echo "<tr><td colspan=\"4\" class=\"ListOddRow\">".$strings["no_items"]."</td></tr>";
}
echo "</table>";

include ("include_footer.php");
?>