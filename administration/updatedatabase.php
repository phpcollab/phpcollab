<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../administration/updatedatabase.php
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
** FILE: updatedatabase.php
**
** DESC: Screen: System information and php library
**
** HISTORY:
** 	2003-10-23	-	update db to the new version
** -----------------------------------------------------------------------------
** TO-DO:
** 	 
**
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';
$setTitle .= " : Edit Database";

if ($profilSession != "0") {
	phpCollab\Util::headerFunction('../general/permissiondenied.php');
	exit;
}

$versionNew = "2.5";

if ($action == "printSetup") {
	include '../includes/db_var.inc.php';
	include '../includes/setup_db.php';
	for($con = 0; $con < count($SQL); $con++){
		echo $SQL[$con] . ';<br/>';
	}
}
if ($action == "printUpdate") {
	include '../includes/db_var.inc.php';
	include '../includes/update_db.php';
	for($con = 0; $con < count($SQL); $con++){
		echo $SQL[$con] . '<br/>';
	}
}

if ($action == "generate") {
	include '../includes/db_var.inc.php';
	include '../includes/update_db.php';
	if ($databaseType == "mysql") {
	$my = @mysql_connect(MYSERVER, MYLOGIN, MYPASSWORD);
	if (mysql_errno() != 0){ exit('<br/><b>PANIC! <br/> Error during connection on server MySQL.</b><br/>'); }
	mysql_select_db(MYDATABASE, $my);
	if (mysql_errno() != 0){ exit('<br/><b>PANIC! <br/> Error during selection database.</b><br/>'); }
	for($con = 0; $con < count($SQL); $con++){
	    mysql_query($SQL[$con]);
	    //echo $SQL[$con] . '<br/>';
	    if (mysql_errno() != 0){ exit('<br/><b>PANIC! <br/> Error during the update of the database.</b><br/> Error: '. mysql_error()); }
	}
	}
	if ($databaseType == "sqlserver") {
	$my = @mssql_connect(MYSERVER, MYLOGIN, MYPASSWORD);
	if (mssql_get_last_message() != 0){ exit('<br/><b>PANIC! <br/> Error during connection on server SQl Server.</b><br/>'); }
	mssql_select_db(MYDATABASE, $my);
	if (mssql_get_last_message() != 0){ exit('<br/><b>PANIC! <br/> Error during selection database.</b><br/>'); }
	for($con = 0; $con < count($SQL); $con++){
	    mssql_query($SQL[$con]);
	    //echo $SQL[$con] . '<br/>';
	    if (mssql_get_last_message() != 0){ exit('<br/><b>PANIC! <br/> Error during the update of the database.</b><br/> Error: '. mssql_get_last_message()); }
	}
	}
	phpCollab\Util::headerFunction("../administration/admin.php?msg=update");
}



include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($strings["edit_database"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->heading($strings["edit_database"]);

$block1->openContent();
$block1->contentTitle("Details");
$block1->form = "settings";
$block1->openForm("../administration/updatedatabase.php?action=generate");


if ($version == $versionNew) {
	if ($versionOld == "") {
		$versionOld = $version;
	}
	echo "<input value=\"$versionOld\" name=\"versionOldNew\" type=\"hidden\">";
} else {
	echo "<input value=\"$version\" name=\"versionOldNew\" type=\"hidden\">";
}

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>Old version $versionOld<br/>";
$comptUpdateDatabase = count($updateDatabase);
for ($i=0;$i<$comptUpdateDatabase;$i++) {
	if ($versionOld < $updateDatabase[$i]) {
		echo "<input type=\"checkbox\" value=\"1\" name=\"dumpVersion[$updateDatabase[$i]]\" checked>$updateDatabase[$i]";
		$submit = "true";
	}
}

echo "<br/>New version $version</td></tr>";

if ($submit == "true") {
echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"".$strings["save"]."\"></td></tr>";
}

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
