<?php

// Does not need to validate logged in
use phpCollab\Exceptions\TooManyPasswordResetAttempts;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "false";
require_once '../includes/library.php';

// See if there is a token in the URL
$token = $request->query->get("token") ;

// If no token, then it is an invalid link.  Redirect to the login page
if (!$token) {
    phpCollab\Util::headerFunction("../general/login.php");
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            // If it is a post, check that the token is submitted
            if (!empty($request->request->get("token")) &&
                !empty($request->request->get("password")) &&
                !empty($request->request->get("passwordConfirm"))) {

                if (empty($request->request->get("password"))) {
//                    $error = $strings["login_password"];
                    $session->getFlashBag()->add(
                        'errors',
                        $strings["login_password"]
                    );

                }

                if (empty($request->request->get("passwordConfirm"))) {
//                    $error = $strings["password_confirm_blank"];
                    $session->getFlashBag()->add(
                        'errors',
                        $strings["password_confirm_blank"]
                    );

                }

                if ($request->request->get("password") !== $request->request->get("passwordConfirm")) {
//                    $error = $strings["new_password_error"];
                    $session->getFlashBag()->add(
                        'errors',
                        $strings["new_password_error"]
                    );

                }




                if (
                    !$session->getFlashBag()->has('errors')
                ) {
                    $resetPassword = $container->getResetPasswordService();
                    $resetPassword->validate($request);
                }
                // Password was saved, so add success message and redirect to login page.
                $session->getFlashBag()->add(
                    'message',
                    $strings["password_successful_changed"]
                );
                phpCollab\Util::headerFunction("../general/login.php");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Forgot Password',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
        $session->getFlashBag()->add(
            'errors',
            $strings["genericError"]
        );

    } catch (TooManyPasswordResetAttempts $exception) {
        $session->getFlashBag()->add(
            'errors',
            $exception->getMessage()
        );
        phpCollab\Util::headerFunction("../general/login.php");

    } catch (Exception $exception) {
        $logger->error('Reset Password', ['Exception' => $exception->getMessage()]);
        $session->getFlashBag()->add(
            'errors',
            $string["permissiondenied"]
        );
        phpCollab\Util::headerFunction("../general/login.php");
    }
}

$notLogged = "true";
$setTitle .= " : Reset Password";

include APP_ROOT . '/views/layout/header.php';

$block1 = new phpCollab\Block();

$block1->form = "password";
$block1->openForm("../general/sendpassword.php", null, $csrfHandler);

echo '<input value="' . $token . '" type="hidden" name="token">';

if ($session->getFlashBag()->has('errors')) {
    $block1->headingError($strings["errors"]);
    foreach ($session->getFlashBag()->get('errors', []) as $error) {
        $block1->contentError($error);
    }
} else if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($setTitle);

$block1->openContent();

$block1->contentRow("* " . $strings["new_password"],
    '<input type="password" name="password" autocomplete="off" required autofocus>');
$block1->contentRow("* " . $strings["confirm_password"],
    '<input type="password" name="passwordConfirm" autocomplete="off" required>');

$block1->contentRow(
    "",
    '<input type="submit" name="save" value="' . $strings["save"] . '">'
);

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';

$session->getFlashBag()->clear();
