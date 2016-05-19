<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$tmpquery = "WHERE tas.id = '$id'";
$taskDetail = new Request();
$taskDetail->openTasks($tmpquery);

if ($taskDetail->tas_published[0] == "1" || $taskDetail->tas_project[0] != $projectSession) {
Util::headerFunction("index.php");
}

$bouton[2] = "over";
$titlePage = $strings["team_task_details"];
include 'include_header.php';

$block1 = new Block();

$block1->heading($strings["team_task_details"]);

echo "<table cellspacing=\"0\" cellpadding=\"3\">";
if ($taskDetail->tas_name[0] != "") {
echo "<tr><td>".$strings["name"]." :</td><td>".$taskDetail->tas_name[0]."</td></tr>";
}
if ($taskDetail->tas_description[0] != "") {
echo "<tr><td valign=\"top\">".$strings["description"]." :</td><td>".nl2br($taskDetail->tas_description[0])."</td></tr>";
}
$complValue = ($taskDetail->tas_completion[0]>0) ? $taskDetail->tas_completion[0]."0 %": $taskDetail->tas_completion[0]." %"; 
echo "<tr><td>".$strings["completion"]." :</td><td>".$complValue."</td></tr>";
if ($taskDetail->tas_assigned_to[0] != "0") {
echo "<tr><td>".$strings["assigned_to"]." :</td><td>".$taskDetail->tas_mem_name[0]."</td></tr>";
}
if ($taskDetail->tas_comments[0] != "") {
echo "<tr><td>".$strings["comments"]." :</td><td>".nl2br($taskDetail->tas_comments[0])."</td></tr>";
}
if ($taskDetail->tas_start_date[0] != "") {
echo "<tr><td>".$strings["start_date"]." :</td><td>".$taskDetail->tas_start_date[0]."</td></tr>";
}
if ($taskDetail->tas_due_date[0] != "") {
echo "<tr><td>".$strings["due_date"]." :</td><td>".$taskDetail->tas_due_date[0]."</td></tr>";
}
echo "<tr><td>".$strings["updates_task"]." :</td><td>";
$tmpquery = "WHERE upd.type='1' AND upd.item = '$id' ORDER BY upd.created DESC";
$listUpdates = new Request();
$listUpdates->openUpdates($tmpquery);
$comptListUpdates=count($listUpdates->upd_id);

if ($comptListUpdates != "0") {
$j = 1;
for ($i=0;$i<$comptListUpdates;$i++) {
	echo "<b>".$j.".</b> <i>".Util::createDate($listUpdates->upd_created[$i],$timezoneSession)."</i><br/>".nl2br($listUpdates->upd_comments[$i]);
	echo "<br/>";
$j++;
}
} else {
echo $strings["no_items"];
}

echo "</td></tr> </table> <hr>";

$tmpquery = "WHERE subtas.task = '$id' AND subtas.published = '0' ORDER BY subtas.name";
$listSubtasks = new Request();
$listSubtasks->openSubtasks($tmpquery);
$comptListSubtasks = count($listSubtasks->subtas_id);

echo "<br/>";

$block2 = new Block();

$block2->heading($strings["subtasks"]);

if ($comptListSubtasks != "0") {

if ($activeJpgraph == "true") {
echo "<img src=\"graphsubtasks.php?task=".$taskDetail->tas_id[0]."\" alt=\"\">
<span class=\"listEvenBold\">[<a href=\"http://www.aditus.nu/jpgraph/\" target=\"_blank\">JpGraph</a>]</span><br/><br/>";
}

echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\" class=\"listing\">
<tr><th class=\"active\">".$strings["name"]."</th><th>".$strings["description"]."</th><th>".$strings["status"]."</th><th>".$strings["due"]."</th></tr>";

for ($i=0;$i<$comptListSubtasks;$i++) {
	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $block2->oddColor;
	} else {
		$class = "even";
		$highlightOff = $block2->evenColor;
	}
$idStatus = $listSubtasks->subtas_status[$i];
echo "<tr class=\"$class\" onmouseover=\"this.style.backgroundColor='".$block2->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td><a href=\"teamsubtaskdetail.php?task=$id&id=".$listSubtasks->subtas_id[$i]."\">".$listSubtasks->subtas_name[$i]."</a></td><td>".nl2br($listSubtasks->subtas_description[$i])."</td><td>$status[$idStatus]</td><td>".$listSubtasks->subtas_due_date[$i]."</td></tr>";
}
echo "</table> <hr>\n";

} else {
echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\"><tr><td colspan=\"4\" class=\"listOddBold\">".$strings["no_items"]."</td></tr></table><hr>";
}

echo "<br/><br/>
<a href=\"showallteamtasks.php?$transmitSid\">".$strings["show_all"]."</a>";

include ("include_footer.php");
?>