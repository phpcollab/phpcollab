<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../administration/phppgadmin.php
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
** FILE: phppgadmin.php
**
** DESC: Screen: dump restore main db page (for postgreSQL)
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
** 	move to the render engine  
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

if ($profilSession != "0") {
	Util::headerFunction('../general/permissiondenied.php?'.session_name().'='.session_id());
	exit;
}

$setTitle .= " : DB Administraton";

include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($strings["database"]." ".MYDATABASE);
$blockPage->closeBreadcrumbs();

$block1 = new Block();
$block1->heading($strings["database"]." ".MYDATABASE);

$block1->openContent();
$block1->contentTitle("Backup database");

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>
<form method=\"post\" action=\"../includes/phppgadmin/db_dump.php\">View dump (schema) of database<br/>
<table>
    <tr>
        <td>
            <input type=\"radio\" name=\"what\" value=\"structure\">Structure only        </td>
        <td>
            <input type=\"checkbox\" name=\"drop\" value=\"1\">Add 'drop table'        </td>
        <td colspan=\"2\">
            <input type=\"submit\" value=\"Go\">
        </td>
    </tr>
    <tr>
        <td>
            <input type=\"radio\" name=\"what\" value=\"data\" checked>Structure and data        </td>
        <td>
            <input type=\"checkbox\" name=\"asfile\" value=\"sendit\">send        </td>
    </tr>
</table>
<input type=\"hidden\" name=\"server\" value=\"1\">
<input type=\"hidden\" name=\"db\" value=\"".MYDATABASE."\">
<input type=\"hidden\" name=\"table\" value=\"y_updates\">
</form>
".$blockPage->buildLink("http://phppgadmin.sourceforge.net","phpPgAdmin",powered)."</a></td></tr>";

$block1->contentTitle("Restore database from sql file");

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>
        <form method=\"post\" action=\"../includes/phpmyadmin/read_dump.php\" enctype=\"multipart/form-data\">
            <input type=\"hidden\" name=\"is_js_confirmed\" value=\"0\" />
            <input type=\"hidden\" name=\"lang\" value=\"en\" />
            <input type=\"hidden\" name=\"server\" value=\"1\" />
            <input type=\"hidden\" name=\"db\" value=\"".MYDATABASE."\" />
            <input type=\"hidden\" name=\"pos\" value=\"0\" />
            <input type=\"hidden\" name=\"goto\" value=\"db_details.php\" />
            <input type=\"hidden\" name=\"zero_rows\" value=\"Your SQL-query has been executed successfully\" />
            <input type=\"hidden\" name=\"prev_sql_query\" value=\"\" /><br />
            Location of sql file&nbsp;:<br />
            <div style=\"margin-bottom: 5px\">
            <input type=\"file\" name=\"sql_file\" /><br />
            </div>
    
            <input type=\"submit\" name=\"SQL\" value=\"Go\" />
        </form>
".$blockPage->buildLink("http://phpwizard.net/projects/phpMyAdmin","phpMyAdmin",powered)."</a>
</td></tr>
</table>
</td></tr>";

$block1->closeContent();

include '../themes/'.THEME.'/footer.php';
?>