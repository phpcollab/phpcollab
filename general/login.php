<?php
/*
** Application name: phpCollab
** Path by root: ../general/login.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: login.php
**
** DESC: Screen: login page
**
**
** =============================================================================
*/

$checkSession = "false";
require_once '../includes/library.php';

$strings = $GLOBALS["strings"];
$loginMethod = $GLOBALS["loginMethod"];

$auth = $request->query->get("auth");
$usernameForm = $request->request->get("usernameForm");
$passwordForm = $request->request->get("passwordForm");

$match = false;
$ssl = false;

if (!empty($SSL_CLIENT_CERT) && !$request->query->get('logout') && $request->query->get('auth') != "test") {
    $auth = "on";
    $ssl = true;

    if (function_exists("openssl_x509_read")) {
        $x509 = openssl_x509_read($SSL_CLIENT_CERT);
        $cert_array = openssl_x509_parse($x509);
        $subject_array = $cert_array["subject"];
        $ssl_email = $subject_array["Email"];
        openssl_x509_free($x509);
    } else {
        $ssl_email = `echo "$SSL_CLIENT_CERT" | $pathToOpenssl x509 -noout -email`;
    }
} else {
    //test blank fields in form
    if ($auth == "test") {
        if ($usernameForm == "" && $passwordForm == "") {
            $error = $strings["login_username"] . "<br/>" . $strings["login_password"];
        } else {
            if ($usernameForm == "") {
                $error = $strings["login_username"];
            } else {
                if ($passwordForm == "") {
                    $error = $strings["login_password"];
                } else {
                    $auth = "on";
                }
            }
        }
    }

    if ($forcedLogin == "false") {
        if ($auth == "on" && !$usernameForm && !$passwordForm) {
            $auth = "off";
            $error = "Detecting variables poisoning ;-)";
        }
    }
}

if ($auth == "on") {
    $usernameForm = strip_tags($usernameForm);
    $passwordForm = strip_tags($passwordForm);

    if (!empty($loginCookie) && !empty($passwordCookie)) {
        $usernameForm = $loginCookie;
    }

    $loginData = [];
    $loginData['login'] = $usernameForm;
    $loginData['demo'] = $demoMode;
    $loginData['ssl'] = $ssl;
    $loginData['ssl_email'] = $ssl_email;

    $member = $members->getMemberByLogin($loginData);


    //test if user exits
    if (!$member) {
        $logger->notice('Member not found', ['username' => $usernameForm]);
        $error = $strings["invalid_login"];
    } else {

        //test password
        if (!empty($loginCookie) && !empty($passwordCookie)) {
            if (!$ssl && $passwordCookie != $member['mem_password']) {
                $logger->notice('Invalid password', ['username' => $usernameForm]);
                $error = $strings["invalid_login"];
            } else {
                $match = true;
            }
        } else {
            if (!$ssl && !phpCollab\Util::doesPasswordMatch($usernameForm, $passwordForm, $member['mem_password'],
                    $loginMethod)) {
                $logger->notice('Invalid password', ['username' => $usernameForm]);
                $error = $strings["invalid_login"];
            } else {
                $match = true;
            }
        }

        if ($match === true) {

            //crypt password in session
            $r = substr($passwordForm, 0, 2);
            $passwordForm = crypt($passwordForm, $r);

            //set session variables
            $session->set('auth', true);
            $session->set('id', $member['mem_id']);
            $session->set('timezone', $member['mem_timezone']);
            $session->set('language', $request->request->get("languageForm"));
            $session->set('login', $usernameForm);
            $session->set('name', $member['mem_name']);
            if (!empty($member['mem_email_work'])) {
                $session->set('email', $member['mem_email_work']);
            }
            $session->set('profile', $member['mem_profil']);
            $session->set('orgId', $member['mem_organization']);
            $session->set('ip', $request->server->get("REMOTE_ADDR"));
            $session->set('dateunix', date("U"));
            $session->set('date', date("d-m-Y H:i:s"));
            $session->set('logoutTime', $member['mem_logout_time']);
            $session->set('theme', THEME);

            //register demo = true in session if user = demo
            if ($usernameForm == "demo") {
                $session->set('demo', "true");
            }

            //insert into or update log
            $logEntry = $loginLogs->getLogByLogin($usernameForm);

            $logger->info('Set session to', ['sessionId' => $session->getId()]);
            /**
             * Validate form data
             */

            $filteredData = [];
            $filteredData['login'] = filter_var((string)$request->request->get('usernameForm'), FILTER_SANITIZE_STRING);
            $filteredData['ip'] = filter_var($request->server->get("REMOTE_ADDR"), FILTER_SANITIZE_STRING);
            $filteredData['session'] = $session->getId();
            $filteredData['last_visite'] = $dateheure;

            if (!$logEntry) {
                $filteredData['compt'] = 1;
                $loginLogs->insertLogEntry($filteredData);
            } else {
                $session->set('lastVisited', $logEntry['last_visite']);

                $filteredData['compt'] = $logEntry['compt'] + 1;

                $loginLogs->updateLogEntry($filteredData);
            }

            // we must avoid to redirect to some special pages
            // otherwise, the user can't access to phpCollab
            $member['mem_last_page'] = str_replace(
                'accessfile.php?mode=view&',
                'viewfile.php?',
                $member['mem_last_page']
            );
            $member['mem_last_page'] = str_replace(
                'accessfile.php?mode=download&',
                'viewfile.php?',
                $member['mem_last_page']
            );

            $logger->info('User logged in', ['username' => $usernameForm]);

            //redirect for external link to internal page
            if ($request->request->get('url') != "") {
                if ($member['mem_profil'] == "3") {
                    phpCollab\Util::headerFunction("../{$request->request->get('url')}&updateProject=true");
                } else {
                    phpCollab\Util::headerFunction("../{$request->request->get('url')}");
                }
            } //redirect to last page required (with auto log out feature)
            else {
                if ($member['mem_last_page'] != "" && $member['mem_profil'] != "3" && $lastvisitedpage) {
                    $members->setLastPageVisitedByLogin($usernameForm, '');
                    phpCollab\Util::headerFunction("../" . $member['mem_last_page']);
                } else {
                    if ($member['mem_last_page'] != "" && (!empty($loginCookie) && !empty($passwordCookie)) && $member['mem_profil'] != "3" && $lastvisitedpage) {
                        $members->setLastPageVisitedByLogin($usernameForm, '');
                        phpCollab\Util::headerFunction("../" . $member['mem_last_page']);
                    } //redirect to home or admin page (if user is administrator)
                    else {
                        if ($member['mem_profil'] == "3") {
                            phpCollab\Util::headerFunction("../projects_site/home.php");
                        } else {
                            if ($member['mem_profil'] == "0") {
                                if ($adminathome == '1') {
                                    phpCollab\Util::headerFunction("../general/home.php");
                                } else {
                                    phpCollab\Util::headerFunction("../administration/admin.php");
                                }
                            } else {
                                phpCollab\Util::headerFunction("../general/home.php");
                            }
                        }
                    }
                }
            }
        }
    }
}

if ($session == "false" && empty($url)) {
    $logger->notice('Invalid session for user', ['username' => $usernameForm]);
    $error = $strings["session_false"];
}

if ($request->query->get('logout') == "true") {
    $msg = "logout";
}

if ($demoMode == "true") {
    $usernameForm = "demo";
    $passwordForm = "demo";
}


$notLogged = "true";

$bodyCommand = "onLoad='document.loginForm.usernameForm.focus();'";

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();



if ($session->getFlashBag()->has('message')) {
    $blockPage->messageBox( $session->getFlashBag()->get('message')[0] );
}

$block1 = new phpCollab\Block();

$block1->form = "login";
$block1->openForm("../general/login.php?auth=test", null, $csrfHandler);

if (!empty($request->query->get('url'))) {
    echo "<input value='{$request->query->get('url')}' type='hidden' name='url'>";
}

if ($session->getFlashBag()->get("error")) {
    $block1->headingError($strings["errors"]);
    foreach ($session->getFlashBag()->get('error', []) as $message) {
        $block1->contentError($message);
    }

}

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($setTitle . " : " . $strings["login"]);

$block1->openContent();
$block1->contentTitle($strings["please_login"]);

if (!empty($session->get('language'))) {
    $langSelected[$session->get('language')] = "selected";
} else {
    $langSelected[ $GLOBALS["langDefault"] = "selected"];
}

$selectLanguage = <<<SELECT_LANG
<select name='languageForm'>";
    <option value="$langDefault">{$languagesArray["$langDefault"]} (Default)</option>
    <option value="ar" {$langSelected["ar"]}>Arabic</option>
    <option value="az" {$langSelected["az"]}>Azerbaijani</option>
    <option value="pt-br"" {$langSelected["pt-br"]}>Brazilian Portuguese</option>
    <option value="bg" {$langSelected["bg"]}>Bulgarian</option>
    <option value="ca" {$langSelected["ca"]}>Catalan</option>
    <option value="zh" {$langSelected["zh"]}>Chinese simplified</option>
    <option value="zh-tw" {$langSelected["zh-tw"]}>Chinese traditional</option>
    <option value="cs-iso" {$langSelected["cs-iso"]}>Czech (iso)</option>
    <option value="cs-win1250" {$langSelected["cs-win1250"]}>Czech (win1250)</option>
    <option value="da" {$langSelected["da"]}>Danish</option>
    <option value="nl" {$langSelected["nl"]}>Dutch</option>
    <option value="en" {$langSelected["en"]}>English</option>
    <option value="et" {$langSelected["et"]}>Estonian</option>
    <option value="fr" {$langSelected["fr"]}>French</option>
    <option value="de" {$langSelected["de"]}>German</option>
    <option value="hu" {$langSelected["hu"]}>Hungarian</option>
    <option value="is" {$langSelected["is"]}>Icelandic</option>
    <option value="in" {$langSelected["in"]}>Indonesian</option>
    <option value="it" {$langSelected["it"]}>Italian</option>
    <option value="ko" {$langSelected["ko"]}>Korean</option>
    <option value="lv" {$langSelected["lv"]}>Latvian</option>
    <option value="no" {$langSelected["no"]}>Norwegian</option>
    <option value="pl" {$langSelected["pl"]}>Polish</option>
    <option value="pt" {$langSelected["pt"]}>Portuguese</option>
    <option value="ro" {$langSelected["ro"]}>Romanian</option>
    <option value="ru" {$langSelected["ru"]}>Russian</option>
    <option value="sk-win1250" {$langSelected["sk-win1250"]}>Slovak (win1250)</option>
    <option value="es" {$langSelected["es"]}>Spanish</option>
    <option value="tr" {$langSelected["tr"]}>Turkish</option>
    <option value="uk" {$langSelected["uk"]}>Ukrainian</option>
</select>
SELECT_LANG;

$block1->contentRow($strings["language"], $selectLanguage);

$block1->contentRow("* " . $strings["user_name"],
    "<input value='$usernameForm' type='text' name='usernameForm' required>");
$block1->contentRow("* " . $strings["password"],
    "<input value='$passwordForm' type='password' name='passwordForm' autocomplete='off' required>");

$block1->contentRow(
    "",
    "<input type='submit' name='save' value='" . $strings["login"] . "'><br/><br/><br/>" . $blockPage->buildLink(
        "../general/sendpassword.php",
        $strings["forgot_pwd"],
        'in'
    )
);

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
