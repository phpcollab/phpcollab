<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include_once '../includes/library.php';

if ($supportType == "team") {
	$teamMember = "false";
	$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
	$memberTest = new Request();
	$memberTest->openTeams($tmpquery);
	$comptMemberTest = count($memberTest->tea_id);
	if ($comptMemberTest == "0") {
		$teamMember = "false";
	} else {
		$teamMember = "true";
	}
}

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

if ($supportType == "team") {
	$tmpquery = "WHERE pro.id = '$project'";
	$requestProject = new Request();
	$requestProject->openProjects($tmpquery);
}

include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$requestProject->pro_id[0],$requestProject->pro_name[0],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=".$requestProject->pro_id[0],$strings["support_requests"],in));
} else if ($supportType == "admin") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?",$strings["support_management"],in));
}

if ($action == "new"){
	$blockPage->itemBreadcrumbs($strings["new_requests"]);
} else if ($action == "open") {
	$blockPage->itemBreadcrumbs($strings["open_requests"]);
} else if ($action == "complete") {
	$blockPage->itemBreadcrumbs($strings["closed_requests"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();
$block1->form = "srs";
$block1->openForm("../support/support.php?action=$action&project=$project&".session_name()."=".session_id()."#".$block1->form."Anchor");
	
if ($action == "new"){
	$block1->heading($strings["new_requests"]);
}elseif ($action == "open"){
	$block1->heading($strings["open_requests"]);
}elseif ($action == "complete"){
	$block1->heading($strings["closed_requests"]);
}

if($teamMember == "true" || $profilSession == "0"){
	$block1->openPaletteIcon();
	//$block1->paletteIcon(0,"add",$strings["add"]);
	$block1->paletteIcon(1,"edit",$strings["edit_status"]);
	$block1->paletteIcon(2,"remove",$strings["delete"]);
	$block1->paletteIcon(3,"info",$strings["view"]);
	$block1->closePaletteIcon();
}
$block1->sorting("support_requests",$sortingUser->sor_support_requests[0],"sr.id ASC",$sortingFields = array(0=>"sr.id",1=>"sr.subject",2=>"sr.member",3=>"sr.project",4=>"sr.priority",5=>"sr.status",6=>"sr.date_open",7=>"sr.date_close"));

if($supportType == "team") {
	if ($action == "new"){
		$tmpquery = "WHERE sr.status = '0' AND sr.project = '$project' ORDER BY $block1->sortingValue";
	}elseif ($action == "open"){
		$tmpquery = "WHERE sr.status = '1' AND sr.project = '$project' ORDER BY $block1->sortingValue";
	}elseif ($action == "complete"){
		$tmpquery = "WHERE sr.status = '2' AND sr.project = '$project' ORDER BY $block1->sortingValue";
	}
}elseif($supportType == "admin"){
	if ($action == "new"){
		$tmpquery = "WHERE sr.status = '0' ORDER BY $block1->sortingValue";
	}elseif ($action == "open"){
		$tmpquery = "WHERE sr.status = '1' ORDER BY $block1->sortingValue";
	}elseif ($action == "complete"){
		$tmpquery = "WHERE sr.status = '2' ORDER BY $block1->sortingValue";
	}
}

if($action != "" || $action != " "){
	$listRequests = new Request();
	$listRequests->openSupportRequests($tmpquery);
	$comptListRequests = count($listRequests->sr_id);
}


if ($comptListRequests != "0") {
	$block1->openResults();
	$block1->labels($labels = array(0=>$strings["id"],1=>$strings["subject"],2=>$strings["owner"],3=>$strings["project"],4=>$strings["priority"],5=>$strings["status"],6=>$strings["date_open"],7=>$strings["date_close"]),"false");

for ($i=0;$i<$comptListRequests;$i++) {
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
$block1->openRow();
$block1->checkboxRow($listRequests->sr_id[$i]);
$block1->cellRow($listRequests->sr_id[$i]);
$block1->cellRow($blockPage->buildLink("../support/viewrequest.php?id=".$listRequests->sr_id[$i],$listRequests->sr_subject[$i],in));
$block1->cellRow($listRequests->sr_mem_name[$i]);
$block1->cellRow($listRequests->sr_project[$i]);
$block1->cellRow($requestPriority);
$block1->cellRow($currentStatus);
$block1->cellRow($listRequests->sr_date_open[$i]);
$block1->cellRow($listRequests->sr_date_close[$i]);
$block1->closeRow();
}
$block1->closeResults();
} else {
$block1->noresults();
}
$block1->closeFormResults();
if($teamMember == "true" || $profilSession == "0"){
	$block1->openPaletteScript();
	//$block1->paletteScript(0,"add","../support/addpost.php?","false,true,true",$strings["respond"]);
	$block1->paletteScript(1,"edit","../support/addpost.php?action=status","false,true,false",$strings["edit_status"]);
	$block1->paletteScript(2,"remove","../support/deleterequests.php?sendto=$action&action=deleteR","false,true,true",$strings["delete"]);
	$block1->paletteScript(3,"info","../support/viewrequest.php?","false,true,false",$strings["view"]);
	$block1->closePaletteScript($comptListRequests,$listRequests->sr_id);
}

include '../themes/'.THEME.'/footer.php';
?>