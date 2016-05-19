<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$tmpquery = "WHERE subtas.id = '$id'";
$subtaskDetail = new Request();
$subtaskDetail->openSubtasks($tmpquery);

$tmpquery = "WHERE tas.id = '$task'";
$taskDetail = new Request();
$taskDetail->openTasks($tmpquery);

if ($subtaskDetail->subtas_published[0] == "1" || $taskDetail->tas_project[0] != $projectSession) {
Util::headerFunction("index.php");
}

$bouton[2] = "over";
$titlePage = $strings["team_subtask_details"];
include 'include_header.php';

echo "<h1 class=\"heading\">".$strings["team_subtask_details"]."</h1>";

echo "<table cellspacing=\"0\" cellpadding=\"3\">";
if ($taskDetail->tas_name[0] != "") {
echo "<tr><td>".$strings["task"]." :</td><td><a href=\"teamtaskdetail.php?id=".$taskDetail->tas_id[0]."\">".$taskDetail->tas_name[0]."</a></td></tr>";
}
if ($subtaskDetail->subtas_name[0] != "") {
echo "<tr><td>".$strings["name"]." :</td><td>".$subtaskDetail->subtas_name[0]."</td></tr>";
}
if ($subtaskDetail->subtas_description[0] != "") {
echo "<tr><td valign=\"top\">".$strings["description"]." :</td><td>".nl2br($subtaskDetail->subtas_description[0])."</td></tr>";
}
$complValue = ($subtaskDetail->subtas_completion[0]>0) ? $subtaskDetail->subtas_completion[0]."0 %": $subtaskDetail->subtas_completion[0]." %"; 
echo "<tr><td>".$strings["completion"]." :</td><td>".$complValue."</td></tr>";
if ($subtaskDetail->subtas_assigned_to[0] != "0") {
echo "<tr><td>".$strings["assigned_to"]." :</td><td>".$subtaskDetail->subtas_mem_name[0]."</td></tr>";
}
if ($subtaskDetail->subtas_comments[0] != "") {
echo "<tr><td>".$strings["comments"]." :</td><td>".$subtaskDetail->subtas_comments[0]."</td></tr>";
}
if ($subtaskDetail->subtas_start_date[0] != "") {
echo "<tr><td>".$strings["start_date"]." :</td><td>".$subtaskDetail->subtas_start_date[0]."</td></tr>";
}
if ($subtaskDetail->subtas_due_date[0] != "") {
echo "<tr><td>".$strings["due_date"]." :</td><td>".$subtaskDetail->subtas_due_date[0]."</td></tr>";
}
echo "<tr><td>".$strings["updates_subtask"]." :</td><td>";
$tmpquery = "WHERE upd.type='2' AND upd.item = '$id' ORDER BY upd.created DESC";
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

echo "</td></tr>
</table>
<hr>";

include ("include_footer.php");
?>