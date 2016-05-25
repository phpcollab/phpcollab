<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../preferences/updateuser.php
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
** FILE: updateuser.php
**
** DESC: Screen: 
**
** HISTORY:
** 	2003-10-23	-	added new document info
**	2003-10-27	-	session problem fixed
** -----------------------------------------------------------------------------
** TO-DO:
** move to a better login system and authentication (try to db session)
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

if ($action == "update") {
	if (($logout_time < "30" && $logout_time != "0") || !is_numeric($logout_time)) {
		$logout_time = "30";
	}
	$fn = phpCollab\Util::convertData($fn);
	$tit = phpCollab\Util::convertData($tit);
	$em = phpCollab\Util::convertData($em);
	$wp = phpCollab\Util::convertData($wp);
	$hp = phpCollab\Util::convertData($hp);
	$mp = phpCollab\Util::convertData($mp);
	$fax = phpCollab\Util::convertData($fax);
	$logout_time = phpCollab\Util::convertData($logout_time);
	$tmpquery = "UPDATE ".$tableCollab["members"]." SET name='$fn',title='$tit',email_work='$em',phone_work='$wp',phone_home='$hp',mobile='$mp',fax='$fax',logout_time='$logout_time',timezone='$tz' WHERE id = '$idSession'";
	phpCollab\Util::connectSql("$tmpquery");
	$timezoneSession = $tz;
	$logouttimeSession = $logout_time;
	$dateunixSession = date("U");
	$nameSession = $fn;

	$_SESSION['logouttimeSession'] = $logouttimeSession;
	$_SESSION['timezoneSession'] = $timezoneSession;
	$_SESSION['dateunixSession'] = $dateunixSession;
	$_SESSION['nameSession'] = $nameSession;

//if mantis bug tracker enabled
		if ($enableMantis == "true") {
// Call mantis function for user profile changes..!!!
			include ("../mantis/user_profile.php");				
		}
	phpCollab\Util::headerFunction("../preferences/updateuser.php?msg=update");
}

$tmpquery = "WHERE mem.id = '$idSession'";
$userPrefs = new phpCollab\Request();
$userPrefs->openMembers($tmpquery);
$comptUserPrefs = count($userPrefs->mem_id);

if ($comptUserPrefs == "0") {
	phpCollab\Util::headerFunction("../users/listusers.php?msg=blankUser");
	exit;
}

$bodyCommand = "onLoad=\"document.user_edit_profileForm.fn.focus();\"";
include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($strings["preferences"]);
if ($notifications == "true") {
$blockPage->itemBreadcrumbs($strings["user_profile"]." | ".$blockPage->buildLink("../preferences/updatepassword.php?",$strings["change_password"],in)." | ".$blockPage->buildLink("../preferences/updatenotifications.php?",$strings["notifications"],in));
} else {
$blockPage->itemBreadcrumbs($strings["user_profile"]." | ".$blockPage->buildLink("../preferences/updatepassword.php?",$strings["change_password"],in));
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "user_edit_profile";
$block1->openForm("../preferences/updateuser.php");
echo "<input type=\"hidden\" name=\"action\" value=\"update\">";

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["user_profile"]." : ".$userPrefs->mem_login[0]);

$block1->openPaletteIcon();
$block1->paletteIcon(0,"export",$strings["export"]);
$block1->closePaletteIcon();

$block1->openContent();
$block1->contentTitle($strings["edit_user_account"]);

$block1->contentRow($strings["full_name"],"<input size=\"24\" style=\"width: 250px;\" type=\"text\" name=\"fn\" value=\"".$userPrefs->mem_name[0]."\">");
$block1->contentRow($strings["title"],"<input size=\"24\" style=\"width: 250px;\" type=\"text\" name=\"tit\" value=\"".$userPrefs->mem_title[0]."\">");
$block1->contentRow($strings["email"],"<input size=\"24\" style=\"width: 250px;\" type=\"text\" name=\"em\" value=\"".$userPrefs->mem_email_work[0]."\">");
$block1->contentRow($strings["work_phone"],"<input size=\"14\" style=\"width: 150px;\" type=\"text\" name=\"wp\" value=\"".$userPrefs->mem_phone_work[0]."\">");
$block1->contentRow($strings["home_phone"],"<input size=\"14\" style=\"width: 150px;\" type=\"text\" name=\"hp\" value=\"".$userPrefs->mem_phone_home[0]."\">");
$block1->contentRow($strings["mobile_phone"],"<input size=\"14\" style=\"width: 150px;\" type=\"text\" name=\"mp\" value=\"".$userPrefs->mem_mobile[0]."\">");
$block1->contentRow($strings["fax"],"<input size=\"14\" style=\"width: 150px;\" type=\"text\" name=\"fax\" value=\"".$userPrefs->mem_fax[0]."\">");
$block1->contentRow($strings["logout_time"].$blockPage->printHelp("user_autologout"),"<input size=\"14\" style=\"width: 150px;\" type=\"text\" name=\"logout_time\" value=\"".$userPrefs->mem_logout_time[0]."\"> sec.");

if ($gmtTimezone == "true") {
$selectTimezone = "<select name=\"tz\">";
	for ($i=-12;$i<=+12;$i++) {
		if ($userPrefs->mem_timezone[0] == $i) {
			$selectTimezone .= "<option value=\"$i\" selected>$i</option>";
		} else {
			$selectTimezone .= "<option value=\"$i\">$i</option>";
		}
	}
$selectTimezone .= "</select>";
$block1->contentRow($strings["user_timezone"].$blockPage->printHelp("user_timezone"),$selectTimezone);
}

if ($userPrefs->mem_profil[0] == "0") {
	$block1->contentRow($strings["permissions"],$strings["administrator_permissions"]);
} else if ($userPrefs->mem_profil[0] == "1") {
	$block1->contentRow($strings["permissions"],$strings["project_manager_permissions"]);
} else if ($userPrefs->mem_profil[0] == "2") {
	$block1->contentRow($strings["permissions"],$strings["user_permissions"]);
} else if ($userPrefs->mem_profil[0] == "5") { 
	$block1->contentRow($strings["permissions"],$strings["project_manager_administrator_permissions"]); 
}

$block1->contentRow($strings["account_created"],phpCollab\Util::createDate($userPrefs->mem_created[0],$timezoneSession));
$block1->contentRow("","<input type=\"submit\" name=\"Save\" value=\"".$strings["save"]."\">");

$block1->closeContent();
$block1->closeForm();

$block1->openPaletteScript();
$block1->paletteScript(0,"export","../users/exportuser.php?id=$idSession","true,true,true",$strings["export"]);
$block1->closePaletteScript("","");

include '../themes/'.THEME.'/footer.php';
?>