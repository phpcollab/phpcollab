<?php
/*
** Application name: phpCollab
** Last Edit page: 2005-03-08 
** Path by root: ../preferences/updatenotifications.php
** Authors: Ceam / Fullo / dracono
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editproject.php
**
** DESC: Screen: Create or edit a project
**
** HISTORY:
**	03/06/2005	-	fix for http://www.php-collab.org/community/viewtopic.php?t=2018
**	03/06/2005	-	xhtml 
**	26/09/2006	-	add daily and weekly email notifications 
** -----------------------------------------------------------------------------
** TO-DO:
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

$tmpquery = "WHERE mem.id = '$idSession'";
$userPrefs = new Request();
$userPrefs->openMembers($tmpquery);
$comptUserPrefs = count($userPrefs->mem_id);

if ($comptUserPrefs == "0") {
    Util::headerFunction("../users/listusers.php?msg=blankUser");
    exit;
}

if ($action == "update") {
    for ($i = 0; $i < 15; $i++) {
        if ($tbl_check[$i] == "") {
            $tbl_check[$i] = "1";
        }
        //echo $tbl_check[$i]."<br/>";
        Util::headerFunction("../preferences/updatenotifications.php?msg=update");
    }

    $tmpquery = "UPDATE " . $tableCollab["notifications"] . " SET taskAssignment='$tbl_check[0]',statusTaskChange='$tbl_check[1]',priorityTaskChange='$tbl_check[2]',duedateTaskChange='$tbl_check[3]',addProjectTeam='$tbl_check[4]',removeProjectTeam='$tbl_check[5]',newPost='$tbl_check[6]',newTopic='$tbl_check[7]',clientAddTask='$tbl_check[8]',uploadFile='$tbl_check[9]',dailyAlert='$tbl_check[10]',weeklyAlert='$tbl_check[11]',pastdueAlert='$tbl_check[12]' WHERE member = '$idSession'";
    Util::connectSql($tmpquery);
}

$tmpquery = "WHERE noti.member = '$idSession'";
$userAvert = new Request();
$userAvert->openNotifications($tmpquery);

if ($userAvert->not_taskassignment[0] == "0") {
    $taskAssignment = "checked";
}

if ($userAvert->not_statustaskchange[0] == "0") {
    $statusTaskChange = "checked";
}

if ($userAvert->not_prioritytaskchange[0] == "0") {
    $priorityTaskChange = "checked";
}

if ($userAvert->not_duedatetaskchange[0] == "0") {
    $duedateTaskChange = "checked";
}

if ($userAvert->not_addprojectteam[0] == "0") {
    $addProjectTeam = "checked";
}

if ($userAvert->not_removeprojectteam[0] == "0") {
    $removeProjectTeam = "checked";
}

if ($userAvert->not_newpost[0] == "0") {
    $newPost = "checked";
}

if ($userAvert->not_newtopic[0] == "0") {
    $newTopic = "checked";
}

if ($userAvert->not_clientaddtask[0] == "0") {
    $clientAddTask = "checked";
}

if ($userAvert->not_uploadfile[0] == "0") {
    $uploadFile = "checked";
}

if ($userAvert->not_daily_alert[0] == "0") {
    $dailyAlert = "checked";
}

if ($userAvert->not_weekly_alert[0] == "0") {
    $weeklyAlert = "checked";
}

if ($userAvert->not_pastdue_alert[0] == "0") {
    $pastdueAlert = "checked";
}


$headBonus = "<script type=\"text/JavaScript\">
<!--
function checkboxes(){
	for (var i = 0; i < document.user_avertForm.elements.length; i++) {
		var e = document.user_avertForm.elements[i];
			if (e.type=='checkbox') {
				if (document.user_avertForm.chkbox_slt.value == \"true\") {
					e.checked = true;

				} else {
					e.checked = false;
				}
			}
	}
	if (document.user_avertForm.chkbox_slt.value == \"true\" ) {
		document.user_avertForm.chkbox_slt.value = \"false\";
	} else {
		document.user_avertForm.chkbox_slt.value = \"true\";
	}

}
//-->
</script>";
include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($strings["preferences"]);
$blockPage->itemBreadcrumbs($blockPage->buildLink("../preferences/updateuser.php?", $strings["user_profile"],
        in) . " | " . $blockPage->buildLink("../preferences/updatepassword.php?", $strings["change_password"],
        in) . " | " . $strings["notifications"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new Block();
$block1->form = "user_avert";
$block1->openForm("../preferences/updatenotifications.php?action=update");

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["edit_notifications"] . " : " . $userPrefs->mem_login[0]);
$block1->openContent();
$block1->contentTitle($strings["edit_notifications_info"]);

echo "
<input type='hidden' name='chkbox_slt' value='true' />
<tr class='odd'>
	<td valign='top' class='leftvalue'>" . $strings["select_deselect"] . " :</td>
	<td>
		<a href='javascript:checkboxes();' onmouseover='window.status = \"" . $strings["select_deselect"] . "\";return true;' onmouseout='window.status = \";return true;\"'><img name='all' src='../themes/" . $block1->getThemeImgPath() . "/checkbox_off_16.gif' border='0' alt=''></a>
	</td>
</tr>
<tr class='odd'>
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[0]' value='0' $taskAssignment></td>
	<td>" . $strings["edit_noti_taskassignment"] . "</td>
</tr>	
<tr class='odd'>
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[1]' value='0' $statusTaskChange></td>
	<td>" . $strings["edit_noti_statustaskchange"] . "</td>
</tr>
<tr class='odd'>
<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[2]' value='0' $priorityTaskChange></td>
	<td>" . $strings["edit_noti_prioritytaskchange"] . "</td>
</tr>
<tr class='odd'>
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[3]' value='0' $duedateTaskChange></td>
	<td>" . $strings["edit_noti_duedatetaskchange"] . "</td>
</tr>
<tr class='odd'>
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[4]' value='0' $addProjectTeam></td>
	<td>" . $strings["edit_noti_addprojectteam"] . "</td>
</tr>
<tr class='odd'>
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[5]' value='0' $removeProjectTeam></td>
	<td>" . $strings["edit_noti_removeprojectteam"] . "</td>
</tr>
<tr class='odd'>	
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[6]' value='0' $newPost></td>
	<td>" . $strings["edit_noti_newpost"] . "</td>
</tr>
<tr class='odd'>
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[7]' value='0' $newTopic></td>
	<td>" . $strings["edit_noti_newtopic"] . "</td>
</tr>

<tr class='odd'>
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[8]' value='0' $clientAddTask></td>
	<td>" . $strings["edit_noti_clientaddtask"] . "</td>
</tr>
<tr class='odd'>
	<td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[9]' value='0' $uploadFile></td>
	<td>" . $strings["edit_noti_uploadfile"] . "</td>
</tr>
";

// Check if email alerts set to "true"
if ($emailAlerts == "true") {
    echo "
    <tr class='odd'>
        <td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[10]' value='0' $dailyAlert></td>
        <td>" . $strings["edit_noti_daily_alert"] . "</td>
    </tr>

    <tr class='odd'>
        <td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[11]' value='0' $weeklyAlert></td>
        <td>" . $strings["edit_noti_weekly_alert"] . "</td>
    </tr>

    <tr class='odd'>
        <td valign='top' class='leftvalue'><input type='checkbox' name='tbl_check[12]' value='0' $pastdueAlert></td>
        <td>" . $strings["edit_noti_pastdue_alert"] . "</td>
    </tr>
    ";
}

echo "
<tr class='odd'>
	<td valign='top' class='leftvalue'>&nbsp;</td>
	<td><input type='submit' name='Save' value='" . $strings["save"] . "'></td>
</tr>
";

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
?>
