<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

if ($action == "update") {
$comments = Util::convertData($comments);
if ($checkbox != "") {
	$tmpquery = "UPDATE ".$tableCollab["subtasks"]." SET comments='$comments',status='0',modified='$dateheure' WHERE id = '$id'";
} else {
	$tmpquery = "UPDATE ".$tableCollab["subtasks"]." SET comments='$comments',status='3',modified='$dateheure' WHERE id = '$id'";
}
	Util::connectSql("$tmpquery");
	Util::headerFunction("clienttaskdetail.php?id=$task");
	exit;
}

$tmpquery = "WHERE subtas.id = '$id'";
$subtaskDetail = new Request();
$subtaskDetail->openSubtasks($tmpquery);

$tmpquery = "WHERE tas.id = '$task'";
$taskDetail = new Request();
$taskDetail->openTasks($tmpquery);

if ($subtaskDetail->subtas_published[0] == "1" || $taskDetail->tas_project[0] != $projectSession) {
Util::headerFunction("index.php");
}

$bouton[3] = "over";
$titlePage = $strings["client_subtask_details"];
include 'include_header.php';

echo "<h1 class=\"heading\">".$strings["client_subtask_details"]."</h1>";

echo "<table cellspacing=\"0\" cellpadding=\"3\">";
if ($taskDetail->tas_name[0] != "") {
echo "<tr><td>".$strings["task"]." :</td><td><a href=\"clienttaskdetail.php?id=".$taskDetail->tas_id[0]."\">".$taskDetail->tas_name[0]."</a></td></tr>";
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

echo "<form accept-charset=\"UNKNOWN\" method=\"post\" action=\"../projects_site/clientsubtaskdetail.php?action=update\" name=\"clientTaskUpdate\" enctype=\"multipart/form-data\"><input name=\"id\" type=\"HIDDEN\" value=\"$id\"><input name=\"task\" type=\"HIDDEN\" value=\"$task\">";

echo "<table cellspacing=\"0\" cellpadding=\"3\">
<tr><th colspan=\"2\">".$strings["client_change_status_subtask"]."</th></tr>
<tr><td>".$strings["status"]." :</td><td>";

if ($subtaskDetail->subtas_status[0] == "0") {
	echo "<input checked value=\"checkbox\" name=\"checkbox\" type=\"checkbox\">";
} else {
	echo "<input value=\"checkbox\" name=\"checkbox\" type=\"checkbox\">";
}

echo "&nbsp;$status[0]</td></tr>
<tr valign=\"top\"><td>".$strings["comments"]." :</td><td><textarea cols=\"40\" name=\"comments\" rows=\"5\">".$subtaskDetail->subtas_comments[0]."</textarea></td></tr><tr align=\"top\"><td>&#160;</td><td><input name=\"submit\" type=\"submit\" value=\"".$strings["save"]."\"></td></tr>
</table>
</form>";

include ("include_footer.php");
?>