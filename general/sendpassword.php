<?php

use phpCollab\Exceptions\TokenGenerationFailedException;

$checkSession = "false";
require_once '../includes/library.php';

$strings = $GLOBALS["strings"];

//test send query
if ($request->isMethod('post')) {

    if (empty($request->request->get('username'))) {
        $error = $strings["empty_field"];
    } else {
        $msg = 'email_pwd';

        try {
            $resetPassword = $container->getResetPasswordService();
            $resetPassword->forgotPassword($request->request->get('username'));
        } catch (TokenGenerationFailedException $e) {
            $error = $strings["genericError"];
        }
    }
}

$notLogged = "true";
include APP_ROOT . '/views/layout/header.php';

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
$block1->openForm("../general/sendpassword.php", null, $csrfHandler);

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($setTitle . " : " . $strings["password"]);

$block1->openContent();
$block1->contentTitle($strings["enter_login"]);

$block1->contentRow("* " . $strings["user_name"],
    '<input style="width: 125px" maxlength="16" size="16" value="" type="text" name="username" autocomplete="off" required autofocus />');
$block1->contentRow("", '<input type="submit" name="send" value="' . $strings['send'] . '" />');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
