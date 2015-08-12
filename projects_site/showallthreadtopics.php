<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include("../includes/library.php");

$bouton[5] = "over";
$titlePage = $strings["bulletin_board"];
include ("include_header.php");

$tmpquery = "WHERE topic.project = '$projectSession' AND topic.published = '0' ORDER BY topic.last_post DESC";
$listTopics = new request();
$listTopics->openTopics($tmpquery);
$comptListTopics = count($listTopics->top_id);

$block1 = new Block();

$block1->heading($strings["bulletin_board"]);

if ($comptListTopics != "0") {
echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\" class=\"listing\">
<tr><th>".$strings["topic"]."</th><th>".$strings["posts"]."</th><th>".$strings["owner"]."</th><th class=\"active\">".$strings["last_post"]."</th></tr>";

for ($i=0;$i<$comptListTopics;$i++) {
	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $block1->oddColor;
	} else {
		$class = "even";
		$highlightOff = $block1->evenColor;
	}
echo "<tr class=\"$class\" onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td><a href=\"showallthreads.php?$transmitSid&id=".$listTopics->top_id[$i]."\">".$listTopics->top_subject[$i]."</a></td><td>".$listTopics->top_posts[$i]."</td><td>".$listTopics->top_mem_name[$i]."</td><td>".Util::createDate($listTopics->top_last_post[$i],$timezoneSession)."</td></tr>";
}
echo "</table>
<hr />\n";
} else {
echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\"><tr><td colspan=\"4\">".$strings["no_items"]."</td></tr></table><hr>";
}

echo "<br/><br/>
<a href=\"createthread.php?$transmitSid\" class=\"FooterCell\">".$strings["create_topic"]."</a>";

include ("include_footer.php");
?>