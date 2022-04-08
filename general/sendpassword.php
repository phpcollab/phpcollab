<?php

use phpCollab\Exceptions\TokenAlreadySentException;
use phpCollab\Exceptions\TooManyPasswordResetAttempts;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "false";
require_once '../includes/library.php';

$strings = $GLOBALS["strings"];

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if (empty($request->request->get('username'))) {
                $error = $strings["empty_field"];
            } else {
                $resetPassword = $container->getResetPasswordService();

                // Check to see if the `timeBetweenAttempts` setting exists, if it does not then fall back to 15 minutes
                $timeBetweenAttempts = !empty($resetPasswordTimes['timeBetweenAttempts']) ? $resetPasswordTimes['timeBetweenAttempts'] : 15;


                /*
                 * Check to see if there is an existing SESSION value
                 */
                if (
                    $session->has('passwordSentTimestamp')
                    && !$resetPassword->checkTimestamp(
                        $session->get('passwordSentTimestamp'),
                        $timeBetweenAttempts
                    )
                ) {
                    throw new TooManyPasswordResetAttempts();
                }

                // Call forgotPassword method
                try {
                    $resetTimes = (
                        isset($resetPasswordTimes)
                        && is_array($resetPasswordTimes)
                    ) ? $resetPasswordTimes : [
                        'tokenLifespan' => 60,
                        'timeBetweenAttempts' => 15,
                        'attemptLimit' => 3
                    ];

                    $resetPassword->forgotPassword($request->request->get('username'), $resetPasswordTimes);

                    $session->set('passwordSentTimestamp', new DateTime('now'));
                    $session->getFlashBag()->add(
                        'message',
                        $strings["send_password_phrase"]
                    );

                    // Email should have been sent, so redirect to the login page
                    phpCollab\Util::headerFunction("../general/login.php");

                } catch (TooManyPasswordResetAttempts $tooManyPasswordResetAttempts) {
                    throw $tooManyPasswordResetAttempts;
                } catch (TokenAlreadySentException $tokenAlreadySentException) {
                    throw $tokenAlreadySentException;
                } catch (Exception $exception) {
                    throw new Exception($exception->getMessage());
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Forgot Password',
            '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
            '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
        ]);
        $session->getFlashBag()->add(
            'errors',
            $strings["genericError"]
        );
    } catch (TokenAlreadySentException $tokenAlreadySentException) {
        $logger->warning('Exception - Token already sent', ['Error' => $tokenAlreadySentException->getMessage()]);
        $error = $strings["error_email_already_sent"];
        $session->getFlashBag()->add(
            'errors',
            $strings["error_email_already_sent"]
        );
        $session->set('passwordSentTimestamp', new DateTime('now'));
    } catch (TooManyPasswordResetAttempts $tooManyPasswordResetAttempts) {
        $logger->critical('Exception', ['Error' => $tooManyPasswordResetAttempts->getMessage()]);
        $session->getFlashBag()->add(
            'errors',
            $strings["error_too_many_attempts"]
        );

        $session->set('passwordSentTimestamp', new DateTime('now'));
    } catch (Exception $exception) {
        $logger->critical('Exception', ['Error' => $exception->getMessage()]);
        $session->getFlashBag()->add(
            'message',
            $strings["send_password_phrase"]
        );
        $session->set('passwordSentTimestamp', new DateTime('now'));
    }

}

$notLogged = "true";
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();


if ($session->getFlashBag()->has('message')) {
    $blockPage->messageBox($session->getFlashBag()->get('message')[0]);
} else {
    if (!empty($msg)) {
        include '../includes/messages.php';
        $blockPage->messageBox($msgLabel);
    }
}

$block1 = new phpCollab\Block();

$block1->form = "send";
$block1->openForm("../general/sendpassword.php", null, $csrfHandler);

if ($session->getFlashBag()->has('errors')) {
    $block1->headingError($strings["errors"]);
    foreach ($session->getFlashBag()->get('errors', []) as $error) {
        $block1->contentError($error);
    }
} else {
    if (!empty($errors)) {
        include '../includes/messages.php';
        $block1->headingError($strings["errors"]);
        $block1->contentError($error);
    }
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

$session->getFlashBag()->clear();
