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

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

$teams = $container->getTeams();
$notifications = $container->getNotificationsManager();

$userDetail = $members->getMemberById($session->get("id"));

if (empty($userDetail)) {
    phpCollab\Util::headerFunction("../users/listusers.php?msg=blankUser");
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "update") {

                $checkboxes = $request->request->get('alerts');

                try {
                    $notifications->setAlerts($session->get("id"), $checkboxes["taskAssignment"],
                        $checkboxes["removeProjectTeam"],
                        $checkboxes["addProjectTeam"], $checkboxes["newTopic"], $checkboxes["newPost"],
                        $checkboxes["statusTaskChange"], $checkboxes["priorityTaskChange"],
                        $checkboxes["duedateTaskChange"],
                        $checkboxes["clientAddTask"], $checkboxes["uploadFile"], $checkboxes["dailyAlert"],
                        $checkboxes["weeklyAlert"], $checkboxes["pastDueAlert"]);

                    phpCollab\Util::headerFunction("../preferences/updatenotifications.php?msg=update");
                } catch (Exception $e) {
                    $logger->error('Preferences (notifications)', ['Exception message', $e->getMessage()]);
                    $error = $strings["action_not_allowed"];
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Preferences: Update notifications',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

$userNotifications = $notifications->getMemberNotifications($session->get("id"));

if ($userNotifications["taskAssignment"] == "0") {
    $taskAssignment = "checked";
}

if ($userNotifications["statusTaskChange"] == "0") {
    $statusTaskChange = "checked";
}

if ($userNotifications["priorityTaskChange"] == "0") {
    $priorityTaskChange = "checked";
}

if ($userNotifications["duedateTaskChange"] == "0") {
    $dueDateTaskChange = "checked";
}

if ($userNotifications["addProjectTeam"] == "0") {
    $addProjectTeam = "checked";
}

if ($userNotifications["removeProjectTeam"] == "0") {
    $removeProjectTeam = "checked";
}

if ($userNotifications["newPost"] == "0") {
    $newPost = "checked";
}

if ($userNotifications["newTopic"] == "0") {
    $newTopic = "checked";
}

if ($userNotifications["clientAddTask"] == "0") {
    $clientAddTask = "checked";
}

if ($userNotifications["uploadFile"] == "0") {
    $uploadFile = "checked";
}

if ($userNotifications["dailyAlert"] == "0") {
    $dailyAlert = "checked";
}

if ($userNotifications["weeklyAlert"] == "0") {
    $weeklyAlert = "checked";
}

if ($userNotifications["pastDueAlert"] == "0") {
    $pastdueAlert = "checked";
}

$headBonus = /** @lang javascript */
    <<<HEAD_BONUS
<script type="text/JavaScript">
function checkboxes(){
	for (var i = 0; i < document.user_avertForm.elements.length; i++) {
		var e = document.user_avertForm.elements[i];
			if (e.type=='checkbox') {
				if (document.user_avertForm.chkbox_slt.value == "true") {
					e.checked = true;

				} else {
					e.checked = false;
				}
			}
	}
	if (document.user_avertForm.chkbox_slt.value == "true" ) {
		document.user_avertForm.chkbox_slt.value = "false";
	} else {
		document.user_avertForm.chkbox_slt.value = "true";
	}

}
</script>
HEAD_BONUS;

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($strings["preferences"]);
$blockPage->itemBreadcrumbs($blockPage->buildLink(
        "../preferences/updateuser.php?", $strings["user_profile"], "in") .
    " | " .
    $blockPage->buildLink(
        "../preferences/updatepassword.php?",
        $strings["change_password"], "in") . " | " . $strings["notifications"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "user_avert";
$block1->openForm("../preferences/updatenotifications.php", null, $csrfHandler);

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["edit_notifications"] . " : " . $userPrefs->mem_login[0]);
$block1->openContent();
$block1->contentTitle($strings["edit_notifications_info"]);

echo <<<HTML
<input type="hidden" name="chkbox_slt" value="true" />
<input type="hidden" name="action" value="update" />
<tr class="odd">
	<td style="vertical-align: top"  class="leftvalue">{$strings["select_deselect"]} :</td>
	<td>
	    <input type="checkbox" onclick="checkboxes();" />
	</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[taskAssignment]" value="0" {$taskAssignment}></td>
	<td>{$strings["edit_noti_taskassignment"]}</td>
</tr>	
<tr class="odd">
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[statusTaskChange]" value="0" {$statusTaskChange}></td>
	<td>{$strings["edit_noti_statustaskchange"]}</td>
</tr>
<tr class="odd">
    <td style="vertical-align: top" class="leftvalue">
        <input type="checkbox" name="alerts[priorityTaskChange]" value="0" {$priorityTaskChange}></td>
	<td>{$strings["edit_noti_prioritytaskchange"]}</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[duedateTaskChange]" value="0" {$dueDateTaskChange}></td>
	<td>{$strings["edit_noti_duedatetaskchange"]}</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[addProjectTeam]" value="0" {$addProjectTeam}></td>
	<td>{$strings["edit_noti_addprojectteam"]}</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[removeProjectTeam]" value="0" {$removeProjectTeam}></td>
	<td>{$strings["edit_noti_removeprojectteam"]}</td>
</tr>
<tr class="odd">	
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[newPost]" value="0" {$newPost}></td>
	<td>{$strings["edit_noti_newpost"]}</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[newTopic]" value="0" {$newTopic}></td>
	<td>{$strings["edit_noti_newtopic"]}</td>
</tr>

<tr class="odd">
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[clientAddTask]" value="0" {$clientAddTask}></td>
	<td>{$strings["edit_noti_clientaddtask"]}</td>
</tr>
<tr class="odd">
	<td style="vertical-align: top" class="leftvalue">
	    <input type="checkbox" name="alerts[uploadFile]" value="0" {$uploadFile}></td>
	<td>{$strings["edit_noti_uploadfile"]}</td>
</tr>
HTML;

// Check if email alerts set to "true"
if ($emailAlerts !== "true") {
    echo <<<HTML
    <tr class="odd">
        <td style="vertical-align: top;" class="leftvalue">
            <input type="checkbox" name="alerts[dailyAlert]" value="0" {$dailyAlert}></td>
        <td>{$strings["edit_noti_daily_alert"]}</td>
    </tr>

    <tr class="odd">
        <td style="vertical-align: top;" class="leftvalue">
            <input type="checkbox" name="alerts[weeklyAlert]" value="0" {$weeklyAlert}></td>
        <td>{$strings["edit_noti_weekly_alert"]}</td>
    </tr>

    <tr class="odd">
        <td style="vertical-align: top;" class="leftvalue">
            <input type="checkbox" name="alerts['pastDueAlert']" value="0" {$pastdueAlert}></td>
        <td>{$strings["edit_noti_pastdue_alert"]}</td>
    </tr>
HTML;
}

echo <<<HTML
<tr class="odd">
	<td style="vertical-align: top;" class="leftvalue">&nbsp;</td>
	<td><input type="submit" name="Save" value="{$strings["save"]}"></td>
</tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
