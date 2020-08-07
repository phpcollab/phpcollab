<?php

use phpCollab\Members\ResetPassword;

$checkSession = "false";
include_once '../includes/library.php';

$strings = $GLOBALS["strings"];

//test send query
if ($request->isMethod('post')) {

    if (empty($request->request->get('username'))) {
        $error = $strings["empty_field"];
    } else {
        $msg = 'email_pwd';

        $resetPassword = new ResetPassword();
        $resetPassword->reset($request->request->get('username'));
    }
}

$notLogged = "true";
$bodyCommand = "onLoad=\"document.sendForm.loginForm.focus();\"";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();

if (!empty($msg)) {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "send";
$block1->openForm("../general/sendpassword.php");

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($setTitle . " : " . $strings["password"]);

$block1->openContent();
$block1->contentTitle($strings["enter_login"]);

$block1->contentRow("* " . $strings["user_name"], '<input style="width: 125px" maxlength="16" size="16" value="" type="text" name="username" autocomplete="off" required />');
$block1->contentRow("", '<input type="submit" name="send" value="' . $strings['send'] . '" />');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
