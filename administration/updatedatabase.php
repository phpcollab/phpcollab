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

if ($session->get('profilSession') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$versionNew = "2.5";

if ($action == "printSetup") {
    include '../includes/db_var.inc.php';
    include '../includes/setup_db.php';
    $sqlCount = count($SQL);
    for ($con = 0; $con < $sqlCount; $con++) {
        echo $SQL[$con] . ';<br/>';
    }
}
if ($action == "printUpdate") {
    include '../includes/db_var.inc.php';
    include '../includes/update_db.php';
    $sqlCount = count($SQL);
    for ($con = 0; $con < $sqlCount; $con++) {
        echo $SQL[$con] . '<br/>';
    }
}

if ($action == "generate") {
    try {
        include '../includes/db_var.inc.php';
        include '../includes/update_db.php';
        if ($databaseType == "mysql") {
            $my = mysqli_connect(MYSERVER, MYLOGIN, MYPASSWORD);
            if (mysqli_errno($my) != 0) {
                echo "<br/><b>PANIC! <br/> Error during connection on server MySQL.</b><br/>";
            }
            mysqli_select_db($my, MYDATABASE);
            if (mysqli_errno($my) != 0) {
                echo "<br/><b>PANIC! <br/> Error during selection database.</b><br/>";
            }
            $sqlCount = count($SQL);
            for ($con = 0; $con < $sqlCount; $con++) {
                mysqli_query($my, $SQL[$con]);
                if (mysqli_errno($my) != 0) {
                    echo "<br/><b>PANIC! <br/> Error during the update of the database.</b><br/> Error: " . mysqli_error($my);
                }
            }
        }
        if ($databaseType == "sqlserver") {
            $my = mssql_connect(MYSERVER, MYLOGIN, MYPASSWORD);
            if (mssql_get_last_message() != 0) {
                echo "<br/><b>PANIC! <br/> Error during connection on server SQl Server.</b><br/>";
            }
            mssql_select_db(MYDATABASE, $my);
            if (mssql_get_last_message() != 0) {
                echo "<br/><b>PANIC! <br/> Error during selection database.</b><br/>";
            }
            $sqlCount = count($SQL);
            for ($con = 0; $con < $sqlCount; $con++) {
                mssql_query($SQL[$con]);
                if (mssql_get_last_message() != 0) {
                    echo "<br/><b>PANIC! <br/> Error during the update of the database.</b><br/> Error: " . mssql_get_last_message();
                }
            }
        }

        phpCollab\Util::headerFunction("../administration/admin.php?msg=update");

    } catch (Exception $e) {

    }
}


include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($strings["edit_database"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->heading($strings["edit_database"]);

$block1->openContent();
$block1->contentTitle("Details");
$block1->form = "settings";
$block1->openForm("../administration/updatedatabase.php?action=generate");


if ($version == $versionNew) {
    if (empty($versionOld)) {
        $versionOld = $version;
    }
    echo "<input value=\"$versionOld\" name=\"versionOldNew\" type=\"hidden\">";
} else {
    echo "<input value=\"$version\" name=\"versionOldNew\" type=\"hidden\">";
}

echo "<tr class=\"odd\"><td class=\"leftvalue\">&nbsp;</td><td>Old version $versionOld<br/>";
$comptUpdateDatabase = count($updateDatabase);
for ($i = 0; $i < $comptUpdateDatabase; $i++) {
    if ($versionOld < $updateDatabase[$i]) {
        echo "<input type=\"checkbox\" value=\"1\" name=\"dumpVersion[$updateDatabase[$i]]\" checked>$updateDatabase[$i]";
        $submit = "true";
    }
}

echo "<br/>New version $version</td></tr>";

if ($submit == "true") {
    echo "<tr class=\"odd\"><td class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"" . $strings["save"] . "\"></td></tr>";
}

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
