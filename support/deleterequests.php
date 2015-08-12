<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include_once '../includes/library.php';

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

if ($action == "deleteRequest") {
	$id = str_replace("**",",",$id);
	$tmpquery1 = "DELETE FROM ".$tableCollab["support_requests"]." WHERE id IN($id)";
	$tmpquery2 = "DELETE FROM ".$tableCollab["support_posts"]." WHERE request_id IN($id)";
	$pieces = explode(",",$id);
	$num = count($pieces);
	Util::connectSql("$tmpquery1");
	Util::connectSql("$tmpquery2");
	
	Util::headerFunction("../support/support.php?msg=delete&action=$sendto&project=$project&".session_name()."=".session_id());
	exit;	
}

if ($action == "deletePost") {
	$id = str_replace("**",",",$id);
	$tmpquery3 = "DELETE FROM ".$tableCollab["support_posts"]." WHERE id IN($id)";
	$pieces = explode(",",$id);
	$num = count($pieces);
	Util::connectSql("$tmpquery3");
	
	Util::headerFunction("../support/viewrequest.php?msg=delete&id=$sendto&".session_name()."=".session_id());
	exit;	
}


if ($action == "deleteR") {
	$id = str_replace("**",",",$id);
	$tmpquery = "WHERE sr.id IN($id) ORDER BY sr.subject";
	$listRequest = new Request();
	$listRequest->openSupportRequests($tmpquery);
	$comptListRequest = count($listRequest->sr_id);
}elseif ($action == "deleteP") {
	$id = str_replace("**",",",$id);
	$tmpquery = "WHERE sp.id IN($id) ORDER BY sp.id";
	$listPost = new Request();
	$listPost->openSupportPosts($tmpquery);
	$comptListPost = count($listPost->sp_id);

	$tmpquery2 = "WHERE sr.id IN(".$listPost->sp_request_id[0].") ORDER BY sr.subject";
	$listRequest = new Request();
	$listRequest->openSupportRequests($tmpquery2);
	$comptListRequest = count($listRequest->sr_id);
}

include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$listRequest->sr_project[0],$listRequest->sr_pro_name[0],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=".$listRequest->sr_project[0],$strings["support_requests"],in));
	if ($action == "deleteR") {
		$blockPage->itemBreadcrumbs($strings["delete_request"]);
	} else if ($action == "deleteP") {
		$blockPage->itemBreadcrumbs($strings["delete_support_post"]);
	}
}elseif($supportType == "admin"){
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?",$strings["support_management"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=".$listRequest->sr_project[0],$strings["support_requests"],in));
	if ($action == "deleteR") {
		$blockPage->itemBreadcrumbs($strings["delete_request"]);
	} else if ($action == "deleteP") {
		$blockPage->itemBreadcrumbs($strings["delete_support_post"]);
	}
}
$blockPage->closeBreadcrumbs();


if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "saP";
if ($action == "deleteR") {
	$block1->openForm("../support/deleterequests.php?action=deleteRequest&id=$id&sendto=$sendto&project=".$listRequest->sr_project[0]."&".session_name()."=".session_id());
}elseif($action == "deleteP"){
	$block1->openForm("../support/deleterequests.php?action=deletePost&id=$id&sendto=".$listRequest->sr_id[0]."&".session_name()."=".session_id());
}

if ($action == "deleteR") {
	$block1->heading($strings["delete_request"]);
}elseif($action == "deleteP"){
	$block1->heading($strings["delete_support_post"]);
}

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

if ($action == "deleteR") {
for ($i=0;$i<$comptListRequest;$i++) {
	echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$listRequest->sr_subject[$i]."</td></tr>";
}
echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\"></td></tr>";
}elseif ($action == "deleteP"){
	for ($i=0;$i<$comptListPost;$i++) {
		echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$listPost->sp_id[$i]."</td></tr>";
	}
	echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\"></td></tr>";
}

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>