<?php
/*
** Application name: phpCollab
** Last Edit page: 30/05/2005
** Path by root: ../project_site/suprequestdestail.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: suprequestdestail.php
**
** DESC: Screen: manage the support request
**
** HISTORY:
** 30/05/2005	-	fix for [ 1210293 ] Login fails when last_page=non-existent support req
** -----------------------------------------------------------------------------
** TO-DO:
**
** =============================================================================
*/

$checkSession = "true";
include '../includes/library.php';

$tmpquery = "WHERE sr.id = '$id'";
$requestDetail = new phpCollab\Request();
$requestDetail->openSupportRequests($tmpquery);

if ($requestDetail->sr_project[0] != $projectSession || $requestDetail->sr_user[0] != $idSession) 
{
	if (!isset($requestDetail->sr_id[0]))
	{
		// The support request wasn't found. This can happen if the lastvisited page for a user is for
		// a request that no longer exists. If this happens the user gets stuck in a login loop and can't
		// login.
		$tmpquery = "UPDATE ".$tableCollab["members"]." SET last_page='' WHERE login = '{$_SESSION['loginSession']}'";
		phpCollab\Util::connectSql("$tmpquery");
	}
	phpCollab\Util::headerFunction("index.php");
}


$tmpquery = "WHERE sp.request_id = '$id' ORDER BY sp.date";
$postDetail = new phpCollab\Request();
$postDetail->openSupportPosts($tmpquery);
$comptPostDetail = count($postDetail->sp_id);

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

echo "<table cellspacing='0' width='90%' cellpadding='3'><tr><th colspan='4'>".$strings["information"].":</th></tr>";

$comptSupStatus = count($requestStatus);
for ($i=0;$i<$comptSupStatus;$i++) 
{
	if ($requestDetail->sr_status[0] == $i) 
	{
		$requestStatus = $requestStatus[$i];
	}
}

$comptPri = count($priority);
for ($i=0;$i<$comptPri;$i++) 
{
	if ($requestDetail->sr_priority[0] == $i) 
	{
		$requestPriority = $priority[$i];
	}
}

echo "<tr><th>".$strings["support_id"].":</th><td>".$requestDetail->sr_id[0]."</td><th>".$strings["status"].":</th><td>$requestStatus</td></tr>
<tr><th>".$strings["subject"].":</th><td>".$requestDetail->sr_subject[0]."</td><th>".$strings["priority"].":</th><td>$requestPriority</td></tr>
<tr><th>".$strings["message"].":</th><td>".$requestDetail->sr_message[0]."</td><th>&nbsp;</th><td>&nbsp;</td></tr>
<tr><th>".$strings["date_open"]." :</th><td>".$requestDetail->sr_date_open[0]."</td><th>&nbsp;</th><td>&nbsp;</td></tr>";

if ($requestDetail->sr_status[0] == "2") 
{
	echo "<tr><th>".$strings["date_close"]." :</th><td>".$requestDetail->sr_date_close[0]."</td><th>&nbsp;</th><td>&nbsp;</td></tr>";
}

echo "<tr><td colspan=\"4\">&nbsp;</td></tr>
<tr><th colspan=\"4\">".$strings["responses"].":</th></tr>
<tr><td colspan=\"4\" align=\"right\"><a href=\"addsupportpost.php?id=$id\" class=\"FooterCell\">".$strings["add_support_response"]."</a></td></tr>";

if ($comptPostDetail != "0") 
{
	for ($i=0;$i<$comptPostDetail;$i++) 
	{
		if (!($i%2)) 
		{
			$class = "odd";
			$highlightOff = $block1->oddColor;
		} 
		else 
		{
			$class = "even";
			$highlightOff = $block1->evenColor;
		}

		echo "	<tr><td colspan='4' class='$class'>&nbsp;</td></tr><tr class='$class'><th>".$strings["date"]." :</th><td colspan='3'>".$postDetail->sp_date[$i]."</td></tr>";

		$tmpquery = "WHERE mem.id = '".$postDetail->sp_owner[$i]."'";
		$ownerDetail = new phpCollab\Request();
		$ownerDetail->openMembers($tmpquery);

		echo "<tr class='$class'><th>".$strings["posted_by"]." :</th><td colspan='3'>".$ownerDetail->mem_name[0]."</td></tr><tr class='$class'><th>".$strings["message"]." :</th><td colspan='3'>".nl2br($postDetail->sp_message[$i])."</td></tr>";
	}
} 
else 
{
	echo "<tr><td colspan='4' class='ListOddRow'>".$strings["no_items"]."</td></tr>";
}
echo "</table>";

include ("include_footer.php");
?>