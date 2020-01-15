<?php

$checkSession = "false";
include_once '../includes/library.php';

$members = new \phpCollab\Members\Members();

//security fix
$loginForm = htmlspecialchars(strip_tags($_POST["loginForm"]), ENT_QUOTES);
$pw = htmlspecialchars(strip_tags($_POST["pw"]), ENT_QUOTES);

$strings = $GLOBALS["strings"];

//test send query
if ($_GET["action"] == "send") {
    $userDetail = $members->getMemberByLogin($loginForm);

    //test if user exists
    if (!$userDetail) {
        $error = $strings["no_login"];

        //test if email of user exists
    } elseif ($userDetail["mem_email_work"] != "") {
        $pass_g = phpCollab\Util::passwordGenerator();
        $pw = phpCollab\Util::getPassword($pass_g);

        $body = $strings["user_name"] . " : " . $userDetail["mem_login"] . "\n\n" . $strings["password"] . " : $pass_g";

        $mail = new \phpCollab\Notification();

        $mail->getUserinfo("1", "from");

        $subject = $setTitle . " " . $strings["password"];

        $mail->Subject = $subject;
        $mail->Priority = "1";
        $mail->Body = $body;
        $mail->AddAddress($userDetail["mem_email_work"], $userDetail["mem_name"]);
        $mail->Send();
        $mail->ClearAddresses();

        $msg = 'email_pwd';

        $tmpquery = "UPDATE {$GLOBALS["tableCollab"]["members"]} SET password = :password WHERE login = :login";

        phpCollab\Util::newConnectSql($tmpquery, ["password" => $pw, "login" => $loginForm]);
    } else {
        $error = $strings["no_email"];
    }
    $send = "on";
}

$notLogged = "true";
$bodyCommand = "onLoad=\"document.sendForm.loginForm.focus();\"";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "send";
$block1->openForm("../general/sendpassword.php?action=send");

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($setTitle . " : " . $strings["password"]);

$block1->openContent();
$block1->contentTitle($strings["enter_login"]);

$block1->contentRow("* " . $strings["user_name"], "<input style='width: 125px' maxlength='16' size='16' value='$loginForm' type='text' name='loginForm' />");
$block1->contentRow("", "<input type='submit' name='send' value='" . $strings['send'] . "' />");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
