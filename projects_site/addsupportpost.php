<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include("../includes/library.php");

$tmpquery = "WHERE sr.id = '$id'";
$requestDetail = new request();
$requestDetail->openSupportRequests($tmpquery);

if ($requestDetail->sr_project[0] != $projectSession || $requestDetail->sr_user[0] != $idSession) {
Util::headerFunction("index.php");
}

if ($action == "add") {
	$mes = Util::convertData($mes);

	$tmpquery1 = "INSERT INTO ".$tableCollab["support_posts"]."(request_id,message,date,owner,project) VALUES('$id','$mes','$dateheure','$idSession','".$requestDetail->sr_project[0]."')";
	Util::connectSql("$tmpquery1");
	$tmpquery = $tableCollab["support_posts"];
	Util::getLastId($tmpquery);
	$num = $lastId[0];
	unset($lastId);
	
		if ($notifications == "true") {
			if ($mes != ""){
				include("../support/noti_newpost.php");
			}
		}
	
	Util::headerFunction("suprequestdetail.php?id=$id&".session_name()."=".session_id());
	exit;
}


$bouton[6] = "over";
$titlePage = $strings["support"];
include ("include_header.php");

echo "<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"../projects_site/addsupportpost.php?id=$id&".session_name()."=".session_id()."&action=add&project=$projectSession#filedetailsAnchor\" name=\"addsupport\" enctype=\"multipart/form-data\">";

echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\">
<tr><th colspan=\"2\">".$strings["add_support_response"]."</th></tr>
<tr><th>".$strings["message"]."</th><td><textarea rows=\"3\" style=\"width: 400px; height: 200px;\" name=\"mes\" cols=\"43\">$mes</textarea></td></tr>
<input type=\"hidden\" name=\"user\" value=\"$idSession\">";

echo "<tr><th>&nbsp;</th><td><input type=\"SUBMIT\" value=\"".$strings["submit"]."\"></td></tr>
</table>
</form>";

include ("include_footer.php");
?>