<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../administration/listlogs.php
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
** FILE: listlogs.php
**
** DESC: Screen: users log
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

if ($profilSession != "0")
{
	phpCollab\Util::headerFunction('../general/permissiondenied.php');
	exit;
}

$setTitle .= " : Logs";

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($strings["logs"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();
$block1->form = "adminD";
$block1->openForm("../administration/listlogs.php?action=delete&&id=$id#".$block1->form."Anchor");
$block1->heading($strings["logs"]);
$block1->openResults($checkbox="false");
$block1->labels($labels = array(0=>$strings["user_name"],1=>$strings["ip"],2=>$strings["session"],3=>$strings["compteur"],4=>$strings["last_visit"],5=>$strings["connected"]),"false",$sorting="false",$sortingOff = array(0=>"4",1=>"DESC"));

$tmpquery = "ORDER BY last_visite DESC";

$listLogs = new phpCollab\Request();
$listLogs->openLogs($tmpquery);
$comptListLogs = count($listLogs->log_id);

$dateunix=date("U");

for ($i=0;$i<$comptListLogs;$i++) 
{
	$block1->openRow();
	$block1->checkboxRow($listLogs->log_id[$i],$checkbox="false");
	$block1->cellRow($listLogs->log_login[$i]);
	$block1->cellRow($listLogs->log_ip[$i]);
	$block1->cellRow($listLogs->log_session[$i]);
	$block1->cellRow($listLogs->log_compt[$i]);
	$block1->cellRow(phpCollab\Util::createDate($listLogs->log_last_visite[$i],$timezoneSession));
	
	if ($listLogs->log_mem_profil[$i] == "3") 
	{
		$z = "(Client on project site)";
	} 
	else 
	{
		$z = "";
	}

	if ($listLogs->log_connected[$i] > $dateunix-5*60) 
	{
		$block1->cellRow($strings["yes"]." ".$z);
	} 
	else 
	{
		$block1->cellRow($strings["no"]);
	}

	$block1->closeRow();
}

$block1->closeResults();

include '../themes/'.THEME.'/footer.php';
