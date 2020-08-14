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

if ($session->get('profilSession') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$setTitle .= " : DB Administration";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], 'in'));
$blockPage->itemBreadcrumbs($strings["database"] . " " . MYDATABASE);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();
$block1->heading($strings["database"] . " " . MYDATABASE);

$block1->openContent();
$block1->contentTitle("Backup database");


echo <<<HTML
<tr class="odd"><td class="leftvalue">&nbsp;</td><td>
       <form method="post" action="backupMySQL.php" name="db_dump">
        <table>
        <tr>
    
            <td>
                <select name="tables[]" size="5" multiple="multiple">
HTML;


sort($tableCollab);

foreach ($tableCollab as $item) {
    echo "<option selected>$item</option>";
}

echo <<<HTML
                </select>
            </td>
        
            <td>
                <input type="radio" name="what" value="structureonly" />
                Structure only<br />
                <input type="radio" name="what" value="all" checked="checked" />
                Structure and data<br />
                <input type="radio" name="what" value="dataonly" />
                Data only
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="checkbox" name="drop" value="1" checked="checked" />
                Add "drop table"
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="checkbox" name="complete_insert" value="yes" />
                Complete inserts
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="checkbox" name="extended_insert" value="yes" />
                Extended inserts
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="checkbox" name="asfile" value="sendit" checked="checked" />
                Save as file ( <input type="checkbox" name="zip" value="zip" />"zipped" )
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Go" />
            </td>
        </tr>
        </table>
        <input type="hidden" name="server" value="1" />
        <input type="hidden" name="lang" value="en" />
        </form>
    </td>
</tr>
HTML;

$block1->contentTitle("Restore database from sql file");

echo <<<HTML
<tr class="odd"><td class="leftvalue">&nbsp;</td><td>Disabled</td></tr>
HTML;


$block1->closeContent();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
