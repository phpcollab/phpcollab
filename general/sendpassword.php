<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../general/sendpassword.php
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
** FILE: sendpassword.php
**
** DESC: Screen: send password if lost
**
** HISTORY:
**      2006-07-28      -       xhtml fixes
** 	2003-10-23	-	added new document info
**  09/04/2006	-	fixed secunia bug n. SA19449
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/


$checkSession = "false";
include_once '../includes/library.php';

//security fix
$loginForm = htmlspecialchars(strip_tags($loginForm),ENT_QUOTES);
$pw = htmlspecialchars(strip_tags($pw),ENT_QUOTES);

//test send query
if ($action == "send") 
{
	$tmpquery = "WHERE mem.login = '$loginForm'";
	$userDetail = new phpCollab\Request();
	$userDetail->openMembers($tmpquery);
	$comptUserDetail = count ($userDetail->mem_id);

//test if user exists
		if ($comptUserDetail == "0") {
			$error = $strings["no_login"];

//test if email of user exists
		} else if ($userDetail->mem_email_work[0] != "") {
			phpCollab\Util::passwordGenerator();
			$pw = phpCollab\Util::getPassword($pass_g);
			$tmpquery = "UPDATE ".$tableCollab["members"]." SET password='$pw' WHERE login = '$loginForm'";
			phpCollab\Util::connectSql("$tmpquery");

			$body = $strings["user_name"]." : ".$userDetail->mem_login[0]."\n\n".$strings["password"]." : $pass_g";

			$mail = new phpCollab\Notification();

			$mail->getUserinfo("1","from");

			$subject = $setTitle . " ".$strings["password"];

			$mail->Subject = $subject;
			$mail->Priority = "1";
			$mail->Body = $body;
			$mail->AddAddress($userDetail->mem_email_work[0], $userDetail->mem_name[0]);
			$mail->Send();
			$mail->ClearAddresses();

			$msg = 'email_pwd';

		} else {
			$error = $strings["no_email"];
		}
	$send = "on";
}

$notLogged = "true";
$bodyCommand = "onLoad=\"document.sendForm.loginForm.focus();\"";
include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "send";
$block1->openForm("../general/sendpassword.php?action=send");

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($setTitle . " : ".$strings["password"]);

$block1->openContent();
$block1->contentTitle($strings["enter_login"]);

$block1->contentRow("* ".$strings["user_name"],"<input style='width: 125px' maxlength='16' size='16' value='$loginForm' type='text' name='loginForm' />");
$block1->contentRow("","<input type='submit' name='send' value='".$strings['send']."' />");

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>
