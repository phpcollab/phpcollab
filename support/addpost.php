<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include_once('../includes/library.php');

if ($enableHelpSupport != "true") {
	Util::headerFunction('../general/permissiondenied.php?'.session_name().'='.session_id());
	exit;
}

if ($supportType == "admin") {
	if ($profilSession != "0") {
		Util::headerFunction('../general/permissiondenied.php?'.session_name().'='.session_id());
		exit;
	}
}

$tmpquery = "WHERE sr.id = '$id'";
$requestDetail = new Request();
$requestDetail->openSupportRequests($tmpquery);

if($action == "edit"){

	if ($sta == 2){
		$tmpquery2 = "UPDATE ".$tableCollab["support_requests"]." SET status='$sta',date_close='$dateheure' WHERE id = '$id'";
		Util::connectSql("$tmpquery2");
	} else {
		$tmpquery2 = "UPDATE ".$tableCollab["support_requests"]." SET status='$sta',date_close='--' WHERE id = '$id'";
		Util::connectSql($tmpquery2);
	}
	
	if ($notifications == "true") {
		if ($requestDetail->sr_status[0] != $sta) {
			$num = $id;
			include("../support/noti_statusrequestchange.php");
		}
	}

	Util::headerFunction("../support/viewrequest.php?id=$id&".session_name()."=".session_id());
	exit;
}

if($action == "add"){
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
	
	Util::headerFunction("../support/viewrequest.php?id=$id&".session_name()."=".session_id());
	exit;
}


include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();

if ($supportType == "team") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$requestDetail->sr_project[0],$requestDetail->sr_pro_name[0],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=".$requestDetail->sr_project[0],$strings["support_requests"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/viewrequest.php?id=".$requestDetail->sr_id[0],$requestDetail->sr_subject[0],in));
	if ($action == "status") {
		$blockPage->itemBreadcrumbs($strings["edit_status"]);
	} else {
		$blockPage->itemBreadcrumbs($strings["add_support_response"]);
	}
} else if ($supportType == "admin") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?",$strings["support_management"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=".$requestDetail->sr_project[0],$strings["support_requests"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/viewrequest.php?id=".$requestDetail->sr_id[0],$requestDetail->sr_subject[0],in));
	if ($action == "status"){
		$blockPage->itemBreadcrumbs($strings["edit_status"]);
	} else {
		$blockPage->itemBreadcrumbs($strings["add_support_response"]);
	}
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}


$block2 = new Block();

	$block2->form = "sr";
	if ($action == "status"){
		$block2->openForm("../support/addpost.php?action=edit&id=$id&".session_name()."=".session_id()."#".$block2->form."Anchor");
	}else{

		$block2->openForm("../support/addpost.php?action=add&id=$id&".session_name()."=".session_id()."#".$block2->form."Anchor");
	}
if ($error != "") {            
	$block2->headingError($strings["errors"]);
	$block2->contentError($error);
}

$block2->heading($strings["add_support_respose"]);

$block2->openContent();
$block2->contentTitle($strings["details"]);
if ($action == "status"){
echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["status"]." :</td><td><select name=\"sta\">";

$comptSta = count($requestStatus);
for ($i=0;$i<$comptSta;$i++) {
	if ($requestDetail->sr_status[0] == $i) {
		echo "<option value=\"$i\" selected>$requestStatus[$i]</option>";
	}else{
		echo "<option value=\"$i\">$requestStatus[$i]</option>";
	}
}
echo "</select></td></tr>";
}else{
echo"<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["message"]."</td><td><textarea rows=\"3\" style=\"width: 400px; height: 200px;\" name=\"mes\" cols=\"43\">$mes</textarea></td></tr>
<input type=\"hidden\" name=\"user\" value=\"$idSession\">";
}
echo"<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"".$strings["submit"]."\"></td></tr>";

$block2->closeContent();

include '../themes/'.THEME.'/footer.php';
?>