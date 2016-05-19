<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../administration/support.php
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
** FILE: support.php
**
** DESC: Screen: manage requests
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
** 	
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

if ($profilSession != "0" || $enableHelpSupport != "true") {
	Util::headerFunction('../general/permissiondenied.php');
	exit;
}

include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($strings["support_management"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") 
{
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

if ($enableHelpSupport == "true")
{
	$tmpquery = "WHERE sr.status = '0'";
	$listNewRequests = new Request();
	$listNewRequests->openSupportRequests($tmpquery);
	$comptListNewRequests = count($listNewRequests->sr_id);
	
	$tmpquery = "WHERE sr.status = '1'";
	$listOpenRequests = new Request();
	$listOpenRequests->openSupportRequests($tmpquery);
	$comptListOpenRequests = count($listOpenRequests->sr_id);
	
	$tmpquery = "WHERE sr.status = '2'";
	$listCompleteRequests = new Request();
	$listCompleteRequests->openSupportRequests($tmpquery);
	$comptListCompleteRequests = count($listCompleteRequests->sr_id);
	
	$block1 = new Block();
	$block1->form = "help";
	
	if ($error != "") 
	{            
		$block1->headingError($strings["errors"]);
		$block1->contentError($error);
	}
	$block1->heading($strings["support_requests"]);
	
	$block1->openContent();
	$block1->contentTitle($strings["information"]);	
	$block1->contentRow($strings["new_requests"],"$comptListNewRequests - ".$blockPage->buildLink("../support/support.php?action=new",$strings["manage_new_requests"],in)."<br/><br/>");
	$block1->contentRow($strings["open_requests"],"$comptListOpenRequests - ".$blockPage->buildLink("../support/support.php?action=open",$strings["manage_open_requests"],in)."<br/><br/>");
	$block1->contentRow($strings["closed_requests"],"$comptListCompleteRequests - ".$blockPage->buildLink("../support/support.php?action=complete",$strings["manage_closed_requests"],in)."<br/><br/>");
	$block1->closeContent();
}

include '../themes/'.THEME.'/footer.php';
?>