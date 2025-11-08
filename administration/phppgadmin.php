<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../administration/phppgadmin.php
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
require_once '../includes/library.php';

if ($session->get('profile') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$setTitle .= " : DB Administraton";

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], in));
$blockPage->itemBreadcrumbs($strings["database"] . " " . MYDATABASE);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();
$block1->heading($strings["database"] . " " . MYDATABASE);

$block1->openContent();
$block1->contentTitle("Backup database");

echo <<<HTML
<tr class="odd"><td class="leftvalue">&nbsp;</td><td>
    <form method="post" action="backupPostgreSQL.php" name="pg_dump">
        <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
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
    </form>
</td></tr>
HTML;

$block1->closeContent();

include APP_ROOT . '/views/layout/footer.php';
