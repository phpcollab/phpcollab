<?php
/*
** Application name: phpCollab
** Last Edit page: 15/01/2005
** Path by root: ../general/login.php
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
** FILE: login.php
**
** DESC: Screen: login page
**
** -----------------------------------------------------------------------------
** TO-DO:
** move to a better login system and authentication (try to db session)
**
** =============================================================================
*/


$checkSession = "false";
include '../includes/library.php';


if ($logout == "true") {
    $tmpquery1 = "UPDATE " . $tableCollab["logs"] . " SET connected='' WHERE login = '$loginSession'";
    Util::connectSql("$tmpquery1");

    // delete the authentication cookies
    //setcookie('loginCookie', '', time()-86400);
    //setcookie('passwordCookie', '', time()-86400);

    session_unset();
    $_SESSION = array();
    session_destroy();

    Util::headerFunction("../general/login.php?msg=logout");
}

$auth = Util::returnGlobal('auth', 'GET');
$loginForm = Util::returnGlobal('loginForm', 'POST');
$passwordForm = Util::returnGlobal('passwordForm', 'POST');

$match = false;
$ssl = false;

if (!empty($SSL_CLIENT_CERT) && !$logout && $auth != "test") {
    $auth = "on";
    $ssl = true;

    if (function_exists("openssl_x509_read")) {
        $x509 = openssl_x509_read($SSL_CLIENT_CERT);
        $cert_array = openssl_x509_parse($x509, true);
        $subject_array = $cert_array["subject"];
        $ssl_email = $subject_array["Email"];
        openssl_x509_free($x509);
    } else {
        $ssl_email = `echo "$SSL_CLIENT_CERT" | $pathToOpenssl x509 -noout -email`;
    }
} else {
    //test blank fields in form
    if ($auth == "test") {
        if ($loginForm == "" && $passwordForm == "") {
            $error = $strings["login_username"] . "<br/>" . $strings["login_password"];
        } else {
            if ($loginForm == "") {
                $error = $strings["login_username"];
            } else {
                if ($passwordForm == "") {
                    $error = $strings["login_password"];
                } else {
                    $auth = "on";

                    if ($rememberForm == "on") {
                        $oneyear = 22896000;
                        $storePwd = Util::getPassword($passwordForm);
                        setcookie("loginCookie", $loginForm, time() + $oneyear);
                        setcookie("passwordCookie", $storePwd, time() + $oneyear);
                    } else {
                        setcookie("loginCookie");
                        setcookie("passwordCookie");
                    }
                }
            }
        }
    }

    if ($forcedLogin == "false") {
        if ($auth == "on" && !$loginForm && !$passwordForm) {
            $auth = "off";
            $error = "Detecting variables poisoning ;-)";
        }
    }
}

//
// $loginCookie = Util::returnGlobal('loginCookie','COOKIE');
// $passwordCookie = Util::returnGlobal('passwordCookie','COOKIE');
// if ($loginCookie != "" && $passwordCookie != "") 
// {
//		$auth = "on";
// } 

if ($auth == "on") {
    $loginForm = strip_tags($loginForm);
    $passwordForm = strip_tags($passwordForm);

    if ($loginCookie != "" && $passwordCookie != "") {
        $loginForm = $loginCookie;
    }

    //query in members table (demo user not listed if demo mode false, to prohibit the access)
    if ($demoMode != true) {
        if ($ssl) {
            $tmpquery = "WHERE mem.email_work = '$ssl_email' AND mem.login != 'demo' AND mem.profil != '4'";
        } else {
            $tmpquery = "WHERE mem.login = '$loginForm' AND mem.login != 'demo' AND mem.profil != '4'";
        }
    } else {
        $tmpquery = "WHERE mem.login = '$loginForm' AND mem.profil != '4'";
    }

    $loginUser = new Request();
    $loginUser->openMembers($tmpquery);
    $comptLoginUser = count($loginUser->mem_id);

    //test if user exits
    if ($comptLoginUser == "0") {
        $error = $strings["invalid_login"];
        setcookie("loginCookie");
        setcookie("passwordCookie");
    } else {

        //test password
        if ($loginCookie != "" && $passwordCookie != "") {
            if (!$ssl && $passwordCookie != $loginUser->mem_password[0]) {
                $error = $strings["invalid_login"];
            } else {
                $match = true;
            }
        } else {
            if (!$ssl && !Util::doesPasswordMatch($loginForm, $passwordForm, $loginUser->mem_password[0])) {
                $error = $strings["invalid_login"];
            } else {
                $match = true;
            }
        }

        if ($match == true) {

            //crypt password in session
            $r = substr($passwordForm, 0, 2);
            $passwordForm = crypt($passwordForm, $r);

            //set session variables
            $browserSession = $_SERVER["HTTP_USER_AGENT"];
            $idSession = $loginUser->mem_id[0];
            $timezoneSession = $loginUser->mem_timezone[0];
            $languageSession = $languageForm;
            $loginSession = $loginForm;
            $passwordSession = $passwordForm;
            $nameSession = $loginUser->mem_name[0];
            $profilSession = $loginUser->mem_profil[0];
            $ipSession = $REMOTE_ADDR;
            $dateunixSession = date("U");
            $dateSession = date("d-m-Y H:i:s");
            $logouttimeSession = $loginUser->mem_logout_time[0];


            $_SESSION["browserSession"] = $browserSession;
            $_SESSION["idSession"] = $idSession;
            $_SESSION["timezoneSession"] = $timezoneSession;
            $_SESSION["languageSession"] = $languageSession;
            $_SESSION["loginSession"] = $loginSession;
            $_SESSION["passwordSession"] = $passwordSession;
            $_SESSION["nameSession"] = $nameSession;
            $_SESSION["profilSession"] = $profilSession;
            $_SESSION["ipSession"] = $ipSession;
            $_SESSION["dateunixSession"] = $dateunixSession;
            $_SESSION["dateSession"] = $dateSession;
            $_SESSION["logouttimeSession"] = $logouttimeSession;

            //register demosession = true in session if user = demo
            if ($loginForm == "demo") {
                $demoSession = "true";

                $_SESSION['demoSession'] = $demoSession;
            }

            //insert into or update log
            $ip = $REMOTE_ADDR;
            $tmpquery = "WHERE log.login = '$loginForm'";
            $registerLog = new Request();
            $registerLog->openLogs($tmpquery);
            $comptRegisterLog = count($registerLog->log_id);
            $session = session_id();

            if ($comptRegisterLog == "0") {
                $tmpquery1 = "INSERT INTO " . $tableCollab["logs"] . "(login,password,ip,session,compt,last_visite) VALUES('$loginForm','$passwordForm','$ip','$session','1','$dateheure')";
                Util::connectSql("$tmpquery1");
            } else {
                $lastvisiteSession = $registerLog->log_last_visite[0];

                $_SESSION['lastvisiteSession'] = $lastvisiteSession;

                $increm = $registerLog->log_compt[0] + 1;
                $tmpquery1 = "UPDATE " . $tableCollab["logs"] . " SET ip='$ip',session='$session',compt='$increm',last_visite='$dateheure' WHERE login = '$loginForm'";
                Util::connectSql("$tmpquery1");
            }

            // we must avoid to redirect to some special pages
            // otherwise, the user can't access to phpCollab
            $loginUser->mem_last_page[0] = str_replace('accessfile.php?mode=view&', 'viewfile.php?',
                $loginUser->mem_last_page[0]);
            $loginUser->mem_last_page[0] = str_replace('accessfile.php?mode=download&', 'viewfile.php?',
                $loginUser->mem_last_page[0]);

            //redirect for external link to internal page
            if ($url != "") {

                if ($loginUser->mem_profil[0] == "3") {
                    Util::headerFunction("../$url&updateProject=true&" . session_name() . "=" . session_id());
                } else {
                    Util::headerFunction("../$url&" . session_name() . "=" . session_id());
                }
            } //redirect to last page required (with auto log out feature)
            else {
                if ($loginUser->mem_last_page[0] != "" && $loginUser->mem_profil[0] != "3" && $lastvisitedpage) {
                    $tmpquery = "UPDATE " . $tableCollab["members"] . " SET last_page='' WHERE login = '$loginForm'";
                    Util::connectSql("$tmpquery");
                    Util::headerFunction("../" . $loginUser->mem_last_page[0] . "&" . session_name() . "=" . session_id());

                } else {
                    if ($loginUser->mem_last_page[0] != "" && ($loginCookie != "" && $passwordCookie != "") && $loginUser->mem_profil[0] != "3" && $lastvisitedpage) {
                        $tmpquery = "UPDATE " . $tableCollab["members"] . " SET last_page='' WHERE login = '$loginForm'";
                        Util::connectSql("$tmpquery");
                        Util::headerFunction("../" . $loginUser->mem_last_page[0] . "&" . session_name() . "=" . session_id());
                    } //redirect to home or admin page (if user is administrator)
                    else {
                        if ($loginUser->mem_profil[0] == "3") {
                            Util::headerFunction("../projects_site/home.php?" . session_name() . "=" . session_id());
                        } else {
                            if ($loginUser->mem_profil[0] == "0") {
                                if ($adminathome == '1') {
                                    Util::headerFunction("../general/home.php?" . session_name() . "=" . session_id());
                                } else {
                                    Util::headerFunction("../administration/admin.php?" . session_name() . "=" . session_id());
                                }

                            } else {
                                Util::headerFunction("../general/home.php?" . session_name() . "=" . session_id());
                            }
                        }
                    }
                }
            }
        }
    }
}

if ($session == "false" && $url == "") {
    $error = $strings["session_false"];
    session_regenerate_id();
}

if ($logout == "true") {
    $msg = "logout";
}

if ($demoMode == "true") {
    $loginForm = "demo";
    $passwordForm = "demo";
}


$notLogged = "true";
$bodyCommand = "onLoad='document.loginForm.loginForm.focus();'";
include('../themes/' . THEME . '/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include_once('../includes/messages.php');
    $blockPage->messagebox($msgLabel);
}


$block1 = new Block();

$block1->form = "login";
$block1->openForm("../general/login.php?auth=test&" . session_name() . "=" . session_id());

if ($url != "") {
    echo "<input value='$url' type='hidden' name='url'>";
}

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($setTitle . " : " . $strings["login"]);

$block1->openContent();
$block1->contentTitle($strings["please_login"]);

$selectLanguage = "<select name='languageForm'>";
$selectLanguage .= "<option value='$langDefault'>Default (" . $langValue["$langDefault"] . ")</option>";
$selectLanguage .= "
	<option value='ar'>Arabic</option>
	<option value='az'>Azerbaijani</option>
	<option value='pt-br'>Brazilian Portuguese</option>
	<option value='bg'>Bulgarian</option>
	<option value='ca'>Catalan</option>
	<option value='zh'>Chinese simplified</option>
	<option value='zh-tw'>Chinese traditional</option>
	<option value='cs-iso'>Czech (iso)</option>
	<option value='cs-win1250'>Czech (win1250)</option>
	<option value='da'>Danish</option>
	<option value='nl'>Dutch</option>
	<option value='en'>English</option>
	<option value='et'>Estonian</option>
	<option value='fr'>French</option>
	<option value='de'>German</option>
	<option value='hu'>Hungarian</option>
	<option value='is'>Icelandic</option>
	<option value='in'>Indonesian</option>
	<option value='it'>Italian</option>
	<option value='ko'>Korean</option>
	<option value='lv'>Latvian</option>
	<option value='ja'>Japanese</option>
	<option value='no'>Norwegian</option>
	<option value='pl'>Polish</option>
	<option value='pt'>Portuguese</option>
	<option value='ro'>Romanian</option>
	<option value='ru'>Russian</option>
	<option value='sk-win1250'>Slovak (win1250)</option>
	<option value='es'>Spanish</option>
	<option value='tr'>Turkish</option>
	<option value='uk'>Ukrainian</option>
";

$selectLanguage .= "</select>";

$block1->contentRow($strings["language"], $selectLanguage);

$block1->contentRow("* " . $strings["user_name"], "<input value='$loginForm' type='text' name='loginForm'>");
$block1->contentRow("* " . $strings["password"], "<input value='$passwordForm' type='password' name='passwordForm'>");

//$block1->contentRow("* ".$strings["remember_password"],"<input type=\"checkbox\" name=\"rememberForm\" value=\"on\">");

$block1->contentRow("",
    "<input type='submit' name='save' value='" . $strings["login"] . "'><br/><br/><br/>" . $blockPage->buildLink("../general/sendpassword.php?",
        $strings["forgot_pwd"], in));

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
