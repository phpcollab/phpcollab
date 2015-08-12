<?php
/*
** Application name: phpCollab
** Last Edit page: 06/09/2004
** Path by root: ../administration/phpmyadmin.php
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
** FILE: phpmyadmin.php
**
** DESC: Screen: dump restore main db page (for mysql)
**
** HISTORY:
** 	2003-10-23	-	added new document info
**  2004-09-06  -   xhtml
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

$setTitle .= " : DB Administration";
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

echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td>
<script src='../includes/phpmyadmin/functions.js' type='text/javascript' language='javascript'></script>
       <form method='post' action='../includes/phpmyadmin/tbl_dump.php' name='db_dump'>
        <table>
        <tr>
    
            <td>
                <select name='table_select[]' size='5' multiple='multiple'>";

sort($tableCollab);
 while(list ($key, $val) = each ($tableCollab)) {
	echo "<option value='$val' selected>$val</option>";
  }

			echo "</select>
            </td>
        
            <td valign='middle'>
                <input type='radio' name='what' value='structure' />
                Structure only<br />
                <input type='radio' name='what' value='data' checked='checked' />
                Structure and data<br />
                <input type='radio' name='what' value='dataonly' />
                Data only
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='checkbox' name='drop' value='1' checked='checked' />
                Add 'drop table'
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='checkbox' name='showcolumns' value='yes' />
                Complete inserts
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='checkbox' name='extended_ins' value='yes' />
                Extended inserts
            </td>

        </tr>
            <tr>
            <td colspan='2'>
                <input type='checkbox' name='use_backquotes' value='1' />
                Use backquotes with tables and fields' names
            </td>
        </tr>
        
        <tr>
            <td colspan='2'>
                <input type='checkbox' name='asfile' value='sendit' onclick=\"return checkTransmitDump(this.form, 'transmit')\" checked='checked' />
                Save as file
    
                (
                <input type='checkbox' name='zip' value='zip' onclick=\"return checkTransmitDump(this.form, 'zip')\" />'zipped'&nbsp;
                
                <input type='checkbox' name='gzip' value='gzip' onclick=\"return checkTransmitDump(this.form, 'gzip')\" />'gzipped'
                
                )
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='submit' value='Go' />
            </td>
        </tr>
        </table>
        <input type='hidden' name='server' value='1' />
        <input type='hidden' name='lang' value='en' />
        <input type='hidden' name='db' value='".MYDATABASE."' />
        </form>
".$blockPage->buildLink("http://phpwizard.net/projects/phpMyAdmin","phpMyAdmin",powered)."</a></td></tr>";

$block1->contentTitle("Restore database from sql file");

echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td>
        <form method='post' action='../includes/phpmyadmin/read_dump.php' enctype='multipart/form-data'>
            <input type='hidden' name='is_js_confirmed' value='0' />
            <input type='hidden' name='lang' value='en' />
            <input type='hidden' name='server' value='1' />
            <input type='hidden' name='db' value='".MYDATABASE."' />
            <input type='hidden' name='pos' value='0' />
            <input type='hidden' name='goto' value='db_details.php' />
            <input type='hidden' name='zero_rows' value='Your SQL-query has been executed successfully' />
            <input type='hidden' name='prev_sql_query' value='' /><br />
            Location of sql file&nbsp;:<br />
            <div style='margin-bottom: 5px'>
            <input type='file' name='sql_file' /><br />
            </div>
    
            <input type='submit' name='SQL' value='Go' />
        </form>
".$blockPage->buildLink("http://phpwizard.net/projects/phpMyAdmin","phpMyAdmin",powered)."</a>
</td></tr>";

$block1->closeContent();

include '../themes/'.THEME.'/footer.php';
?>