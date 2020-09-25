<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../general/error.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: error.php
**
** DESC: Screen: show errors page
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**
** =============================================================================
*/

include_once '../includes/library.php';

$blank = "true";
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();
$block1->heading($setTitle . " : Error");

$block1->openContent();

if ($databaseType == "mysql") {
    $block1->contentTitle("MySql Error");
}
if ($databaseType == "sqlserver") {
    $block1->contentTitle("Sql Server Error");
}

if ($type == "myserver") {
    $block1->contentRow("", $strings["error_server"]);
}
if ($type == "mydatabase") {
    $block1->contentRow("", $strings["error_database"]);
}

$block1->closeContent();

$footerDev = "false";
include APP_ROOT . '/views/layout/footer.php';
