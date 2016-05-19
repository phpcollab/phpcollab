<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$bouton[1] = "over";
$titlePage = $strings["project_team"];
include 'include_header.php';

$tmpquery = "WHERE tea.project = '$projectSession' AND tea.published = '0' ORDER BY mem.name";
$listContacts = new Request();
$listContacts->openTeams($tmpquery);
$comptListTeams = count($listContacts->tea_id);

$block1 = new Block();

$block1->heading($strings["project_team"]);

if ($comptListTeams != "0") {
echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\" class=\"listing\">
<tr><th class=\"active\">".$strings["name"]."</th><th>".$strings["title"]."</th><th>".$strings["company"]."</th><th>".$strings["email"]."</th></tr>";

for ($i=0;$i<$comptListTeams;$i++) {
if ($listContacts->tea_mem_phone_work[$i] == "") {
	$listContacts->tea_mem_phone_work[$i] = $strings["none"];
}
	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $block1->oddColor;
	} else {
		$class = "even";
		$highlightOff = $block1->evenColor;
	}
echo "<tr class=\"$class\" onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td><a href=\"contactdetail.php?id=".$listContacts->tea_mem_id[$i]."\">".$listContacts->tea_mem_name[$i]."</a></td><td>".$listContacts->tea_mem_title[$i]."</td><td>".$listContacts->tea_org_name[$i]."</td><td><a href=\"mailto:".$listContacts->tea_mem_email_work[$i]."\">".$listContacts->tea_mem_email_work[$i]."</a></td></tr>";
}
echo "</table>
<hr />\n";
} else {
echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\"><tr><td colspan=\"4\">".$strings["no_items"]."</td></tr></table><hr>";
}

include ("include_footer.php");
?>