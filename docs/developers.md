DEVELOPERS

# Request data since 2.2 release (update)

### new class sample ###
...
if ($comptListOrganizations != "0") {
	$block1->openResults();
	$block1->labels($labels = array(0=>$strings["name"],1=>$strings["phone"],2=>$strings["url"]),"false");

for ($i=0;$i<$comptListOrganizations;$i++) {
	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $oddColor;
	} else {
		$class = "even";
		$highlightOff = $evenColor;
	}
$block1->openRow($class);
$block1->checkboxRow($listOrganizations->org_id[$i]);
$block1->cellRow($blockPage->buildLink("../clients/viewclient.php?id=".$listOrganizations->org_id[$i],$listOrganizations->org_name[$i],in));
$block1->cellRow($listOrganizations->org_phone[$i]);
$block1->cellRow($blockPage->buildLink($listOrganizations->org_url[$i],$listOrganizations->org_url[$i],out));
$block1->closeRow();
}
}
...


# Request data since 1.6 release
Request functions in library are replaced by a new class request.class.php
There is no more unset to write !!

### new class sample ###
...
if ($comptListOrganizations != "0") {
for ($i=0;$i<$comptListOrganizations;$i++) {
	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $oddColor;
	} else {
		$class = "even";
		$highlightOff = $evenColor;
	}
echo "<tr class=\"$class\" onmouseover=\"this.style.backgroundColor='".$highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\">
<td align=\"center\"><a href=\"javascript:MM_toggleItem(document.".$block1->form."Form, '".$listOrganizations->org_id[$i]."', '".$block1->form."cb".$listOrganizations->org_id[$i]."','".THEME."')\"><img name=\"".$block1->form."cb".$listOrganizations->org_id[$i]."\" border=\"0\" src=\"themes/".THEME."/checkbox_off_16.gif\" alt=\"\" vspace=\"3\"></a></td>
<td><a href=\"clientdetail.php?id=".$listOrganizations->org_id[$i]."\">".$listOrganizations->org_name[$i]."</a></td>
<td>".$listOrganizations->org_phone[$i]."&nbsp;</td>
<td><a target=\"_blank\" href=\"".$listOrganizations->org_url[$i]."\">".$listOrganizations->org_url[$i]."</a>&nbsp;</td></tr>";
}
}
...

### old function sample ###
...
if ($comptOrg != "0") {
for ($i=0;$i<$comptOrg;$i++) {
	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $oddColor;
	} else {
		$class = "even";
		$highlightOff = $evenColor;
	}
echo "<tr class=\"$class\" onmouseover=\"this.style.backgroundColor='".$highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\">
<td align=\"center\"><a href=\"javascript:MM_toggleItem(document.".$block1->form."Form, '$org_id[$i]', '".$block1->form."cb$org_id[$i]','".THEME."')\"><img name=\"".$block1->form."cb$org_id[$i]\" border=\"0\" src=\"themes/".THEME."/checkbox_off_16.gif\" alt=\"\" vspace=\"3\"></a></td>
<td><a href=\"clientdetail.php?id=$org_id[$i]\">$org_name[$i]</a></td>
<td>$org_phone[$i]&nbsp;</td>
<td><a target=\"_blank\" href=\"$org_url[$i]\">$org_url[$i]</a>&nbsp;</td></tr>";
}
}
...
