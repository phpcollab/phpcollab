<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../general/license.php
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
** FILE: license.php
**
** DESC: Screen: print GNU/GPL license
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**
** =============================================================================
*/


$checkSession = "false";
include_once '../includes/library.php';

$notLogged = "true";
include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();

$block1 = new Block();
$block1->heading($setTitle . " : License");

$block1->openContent();
$block1->contentTitle("License");

$block1->contentRow("","<pre>".Util::getFileContents("../docs/copying.txt")."</pre>");

$block1->closeContent();

include '../themes/'.THEME.'/footer.php';
?>