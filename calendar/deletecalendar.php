<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../calendar/deletecalendar.php
** Authors: Ceam / Fullo
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: deletecalendar.php
**
** DESC: screen: delete calendar item from db
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**	check for better template usage
**
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';

$strings = $GLOBALS['strings'];

$calendars = new \phpCollab\Calendars\Calendars();

if ($_GET['action'] == "delete") {
    $id = str_replace("**", ",", $_GET['id']);

    try {
        $delete = $calendars->deleteCalendar($id);
    } catch (\Exception $e) {
        echo "Error: $e";
    }

    phpCollab\Util::headerFunction("../calendar/viewcalendar.php?msg=delete");
}

$setTitle .= " : Delete Calendar";
if (strpos($_GET['id'], "**") !== false) {
    $setTitle .= " Entries";
} else {
    $setTitle .= " Entry";
}
    
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?", $strings["calendar"], 'in'));
$blockPage->itemBreadcrumbs($strings["delete"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}


$block1 = new phpCollab\Block();
$block1->form = "saP";
$block1->openForm("../calendar/deletecalendar.php?project=$project&action=delete&id=$id");

$block1->heading($strings["delete"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);

$listCalendar = $calendars->openCalendarById($id);

echo "<h3>Calendar:</h3>";

foreach ($listCalendar as $item) {
    echo <<<ROW
<tr class="odd">
<td valign="top" class="leftvalue">#{$item['cal_id']}</td>
<td>{$item['cal_shortname']}</td>
</tr>
ROW;
}

echo <<<ROW
<tr class="odd">
  <td valign="top" class="leftvalue">&nbsp;</td>
  <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
</tr>
ROW;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/'.THEME.'/footer.php';
