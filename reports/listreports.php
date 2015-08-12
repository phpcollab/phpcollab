<?php
/*
** Application name: phpCollab
** Last Edit page: 23/03/2004
** Path by root: ../reports/listreports.php
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
** FILE: listreports.php
**
** DESC: Screen: list the existing report
**
** HISTORY:
**  24/01/2005	-	fix export report
** 	23/03/2004	-	added new document info
**  23/03/2004  -	new export to pdf by Angel 
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

$setTitle .= " : List Reports";

include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/listreports.php?",$strings["reports"],in));
$blockPage->itemBreadcrumbs($strings["my_reports"]);
$blockPage->closeBreadcrumbs();

$block1 = new Block();

$block1->form = "wbSe";
$block1->openForm("../reports/listreports.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->heading($strings["my_reports"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0,"add",$strings["add"]);
$block1->paletteIcon(1,"remove",$strings["delete"]);
$block1->paletteIcon(2,"export",$strings["export"]);
$block1->closePaletteIcon();

$block1->sorting("reports",$sortingUser->sor_reports[0],"rep.name ASC",$sortingFields = array(0=>"rep.name",1=>"rep.created"));

$tmpquery = "WHERE rep.owner = '$idSession' ORDER BY ".$block1->sortingValue." ";
$listReports = new Request();
$listReports->openReports($tmpquery);
$comptListReports = count($listReports->rep_id);

if ($comptListReports != "0") 
{
	$block1->openResults();

	$block1->labels($labels = array(0=>$strings["name"],1=>$strings["created"]),"false");

	for ($i=0;$i<$comptListReports;$i++) 
	{
		$block1->openRow();
		$block1->checkboxRow($listReports->rep_id[$i]);
		$block1->cellRow($blockPage->buildLink("../reports/resultsreport.php?id=".$listReports->rep_id[$i],$listReports->rep_name[$i],in));
		$block1->cellRow(Util::createDate($listReports->rep_created[$i],$timezoneSession));
	}

	$block1->closeResults();

} else {
	$block1->noresults();
}

$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0,"add","../reports/createreport.php?","true,true,true",$strings["add"]);
$block1->paletteScript(1,"remove","../reports/deletereports.php?","false,true,true",$strings["delete"]);
$block1->paletteScript(2,"export","../reports/exportreport.php?","false,true,true",$strings["export"]);
$block1->closePaletteScript($comptListReports,$listReports->rep_id);

include '../themes/'.THEME.'/footer.php';
?>