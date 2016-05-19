<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

$tmpquery = "WHERE mem.id = '$idSession'";
$userDetail = new Request();
$userDetail->openMembers($tmpquery);

$tmpquery = "WHERE sr.member = '$idSession' AND sr.project = '$project'";
$listRequests = new Request();
$listRequests->openSupportRequests($tmpquery);
$comptListRequests = count($listRequests->sr_id);

$block1 = new Block();

$block1->heading($strings["my_support_request"]);

if ($comptListRequests != "0") {
echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\" class=\"listing\">
<tr><th class=\"active\">".$strings["id"]."</th><th>".$strings["subject"]."</th><th>".$strings["priority"]."</th><th>".$strings["status"]."</th><th>".$strings["project"]."</th><th>".$strings["date_open"]."</th><th>".$strings["date_close"]."</th></tr>";

for ($i=0;$i<$comptListRequests;$i++) {

	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $block1->oddColor;
	} else {
		$class = "even";
		$highlightOff = $block1->evenColor;
	}
	
	$comptSta = count($requestStatus);
	for ($sr=0;$sr<$comptSta;$sr++) {
		if ($listRequests->sr_status[$i] == $sr) {
			$currentStatus = $requestStatus[$sr];
		}
	}

	$comptPri = count($priority);
	for ($rp=0;$rp<$comptPri;$rp++) {
		if ($listRequests->sr_priority[$i] == $rp) {
			$requestPriority = $priority[$rp];
		}
	}		

echo "<tr class=\"$class\" onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\">
<td>".$listRequests->sr_id[$i]."</td>
<td><a href=\"suprequestdetail.php?id=".$listRequests->sr_id[$i]."\">".$listRequests->sr_subject[$i]."</a></td>
<td>$requestPriority</td>
<td>$currentStatus</td>
<td>".$listRequests->sr_project[$i]."</td>
<td>".$listRequests->sr_date_open[$i]."</td>
<td>".$listRequests->sr_date_close[$i]."</td>
</tr>";
}
echo "</table>
<hr />\n";
} else {
echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\"><tr><td colspan=\"4\">".$strings["no_items"]."</td></tr></table><hr>";
}
echo "<br/><br/>
<a href=\"addsupport.php?$transmitSid\" class=\"FooterCell\">".$strings["add_support_request"]."</a>";

include ("include_footer.php");
?>