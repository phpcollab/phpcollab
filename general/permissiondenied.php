<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../general/permissiondenied.php
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
** FILE: permissiondenied.php
**
** DESC: Screen: permission denied page
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

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();

$msg = "permissiondenied";
if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

include APP_ROOT . '/views/layout/footer.php';
