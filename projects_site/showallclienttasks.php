<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$bouton[3] = "over";
$titlePage = $strings["client_tasks"];
include 'include_header.php';

$tmpquery = "WHERE tas.project = '$projectSession' AND tas.assigned_to != '0' AND tas.published = '0' AND mem.profil = '3' ORDER BY tas.name";
$listTasks = new Request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

$block1 = new Block();

$block1->heading($strings["client_tasks"]);

if ($comptListTasks != "0") {
echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\" class=\"listing\">
<tr><th class=\"active\">".$strings["name"]."</th><th>".$strings["description"]."</th><th>".$strings["status"]."</th><th>".$strings["due"]."</th></tr>";

for ($i=0;$i<$comptListTasks;$i++) {
	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $block1->getHighlightOff();
	} else {
		$class = "even";
		$highlightOff = $block1->getHighlightOn();
	}
if ($listTasks->tas_due_date[$i] == "") {
	$listTasks->tas_due_date[$i] = $strings["none"];
}
$idStatus = $listTasks->tas_status[$i];
echo "<tr class=\"$class\" onmouseover=\"this.style.backgroundColor='".$block1->getHighlightOn()."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td><a href=\"clienttaskdetail.php?id=".$listTasks->tas_id[$i]."\">".$listTasks->tas_name[$i]."</a></td><td>".nl2br($listTasks->tas_description[$i])."</td><td>$status[$idStatus]</td><td>".$listTasks->tas_due_date[$i]."</td></tr>";
}
echo "</table>
<hr />\n";
} else {
echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\"><tr><td colspan=\"4\">".$strings["no_items"]."</td></tr></table><hr>";
}

include ("include_footer.php");
?>