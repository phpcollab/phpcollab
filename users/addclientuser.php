<?php

use phpCollab\Notifications\Notifications;
use phpCollab\Organizations\Organizations;

$checkSession = "true";
include_once '../includes/library.php';

$notifications = new Notifications();

$orgId = $request->query->get('organization');

if (!$orgId) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankUser");
}

$organizations = new Organizations();
$clientDetail = $organizations->checkIfClientExistsById($orgId);

if (empty($clientDetail)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

//case add client user
if ($request->query->get('action') == "add") {
    if ($request->isMethod('post')) {
        $user_login = "";
        $user_login_old = "";
        $user_full_name = "";
        $user_organization = "";
        $user_title = "";
        $user_email_work = "";
        $user_phone_work = "";
        $user_phone_home = "";
        $user_phone_mobile = "";
        $user_fax = "";
        $user_comments = "";
        $user_last_page = "";

        if (!empty($request->request->get('user_name'))) {
            $user_login = filter_var($request->request->get('user_name'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('full_name'))) {
            $user_full_name = filter_var($request->request->get('full_name'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('organization'))) {
            $user_organization = filter_var($request->request->get('organization'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('title'))) {
            $user_title = filter_var($request->request->get('title'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('email_work'))) {
            $user_email_work = filter_var($request->request->get('email_work'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('phone_work'))) {
            $user_phone_work = filter_var($request->request->get('phone_work'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('phone_home'))) {
            $user_phone_home = filter_var($request->request->get('phone_home'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('phone_mobile'))) {
            $user_phone_mobile = filter_var($request->request->get('phone_mobile'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('fax'))) {
            $user_fax = filter_var($request->request->get('fax'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('comments'))) {
            $user_comments = filter_var($request->request->get('comments'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('last_page'))) {
            $user_last_page = filter_var($request->request->get('last_page'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('password'))) {
            $user_password = $request->request->get('password');
        }

        if (!empty($request->request->get('password_confirm'))) {
            $user_password_confirm = $request->request->get('password_confirm');
        }

        if (!ctype_alnum($user_login)) {
            $error = $strings["alpha_only"];
        } else {
            if ($members->checkIfMemberExists($user_login)) {
                $error = $strings["user_already_exists"];
            } else {

                $newMemberId = null;
                //test if 2 passwords match
                if ($user_password != $user_password_confirm || $user_password == "") {
                    $error = $strings["new_password_error"];
                } else {
                    try {
                        $newMemberId = $members->addMember($user_login, $user_full_name, $user_email_work, $user_password, 3, $user_title, $user_organization, $user_phone_work, $user_phone_home, $user_phone_mobile, $user_fax, $user_comments, $dateheure);

                        if ($newMemberId) {
                            // Set the member password
                            $members->setPassword($newMemberId, $user_password);

                            $notifications->addMember($newMemberId);


                            // notify user hack by urbanfalcon
                            // 28/05/2003 patch by fullo
                            if ($user_email_work != "") {
                                $partSubject = $strings["noti_memberactivation1"];
                                $partFirst = $strings["noti_memberactivation2"];
                                $partSecond = $strings["noti_memberactivation3"];
                                $partThird = $strings["noti_memberactivation4"];
                                $partFourth = $strings["noti_memberactivation5"];
                                $partFooter = "--\n" . $strings["noti_foot1"];

                                $subject = $partSubject;
                                $message = $partFirst . "\n\n";
                                $message .= $partSecond . " ";
                                $message .= $user_login . "\n";
                                $message .= $partThird . " ";
                                $message .= $user_password;
                                $message .= "\n\n" . $partFourth;
                                $message .= "\n\n" . $partFooter;

                                // THE BELOW FROM LINE IS HARDCODED SINCE THE NOTIFICATION CLASS IS NOT BEING USED AND GLOBALS CAN'T REACH
                                $headers = "Content-type:text/plain;charset=\"UTF-8\"\nFrom: \"Support\" <" . $supportEmail . ">\nX-Priority: 3\nX-Mailer: PhpCollab $version";
                                mail("$user_email_work", "$partSubject", "$message", "$headers");

                                // SEND A NOTIFICATION EMAIL TO ADMIN - HARD CODED
                                mail($supportEmail, "Activation Success", "This message was generated by phpCollab:
        ----------------------------------------------------
        Account Activated For: $user_full_name
        Account Username: $user_login
        Account Password: $user_password", "$headers");
                            }
                            // END send notification text message

                            //if mantis bug tracker enabled
                            if ($enableMantis == "true") {
                                // Call mantis function for user changes..!!!
                                $f_access_level = $client_user_level; // reporter
                                include '../mantis/user_update.php';
                            }
                            phpCollab\Util::headerFunction("../clients/viewclient.php?id={$user_organization}&msg=add");

                        } else {
                            $error = $strings["errors"];
                        }
                    } catch (Exception $e) {
                        echo $error = $e->getMessage();
                    }
                }
            }
        }
    }
}

$bodyCommand = 'onLoad="document.client_user_addForm.user_name.focus();"';
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], 'in'));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $clientDetail['org_id'], $clientDetail['org_name'], 'in'));
$blockPage->itemBreadcrumbs($strings["add_client_user"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "client_user_add";
$block1->openForm("../users/addclientuser.php?organization={$orgId}&action=add");

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["add_client_user"]);

$block1->openContent();
$block1->contentTitle($strings["enter_user_details"]);

$block1->contentRow($strings["user_name"], '<input size="24" style="width: 250px;" maxlength="16" type="text" name="user_name" value="'.$request->request->get('user_name').'" required>');
$block1->contentRow($strings["full_name"], '<input size="24" style="width: 250px;" maxlength="64" type="text" name="full_name" value="'.$request->request->get('full_name').'" required>');
$block1->contentRow($strings["title"], '<input size="24" style="width: 250px;" maxlength="64" type="text" name="title" value="'.$request->request->get('title').'">');

$selectOrganization = '<select name="organization">';

$organizationsList = $organizations->getListOfOrganizations();


foreach ($organizationsList as $org) {
    if ($orgId == $org['org_id']) {
        $selectOrganization .= '<option value="' . $org['org_id'] . '" selected>' . $org['org_name'] . '</option>';
    } else {
        $selectOrganization .= '<option value="' . $org['org_id'] . '">' . $org['org_name'] . '</option>';
    }
}

$selectOrganization .= "</select>";
$block1->contentRow($strings["organization"], $selectOrganization);

$block1->contentRow($strings["email"], '<input size="24" style="width: 250px;" maxlength="128" type="email" name="email_work" value="' . $request->request->get('email_work') . '" required>');
$block1->contentRow($strings["work_phone"], '<input size="14" style="width: 150px;" maxlength="32" type="tel" name="phone_work" value="' . $request->request->get('phone_work') . '">');
$block1->contentRow($strings["home_phone"], '<input size="14" style="width: 150px;" maxlength="32" type="tel" name="phone_home" value="' . $request->request->get('phone_home') . '">');
$block1->contentRow($strings["mobile_phone"], '<input size="14" style="width: 150px;" maxlength="32" type="tel" name="phone_mobile" value="' . $request->request->get('phone_mobile') . '">');
$block1->contentRow($strings["fax"], '<input size="14" style="width: 150px;" maxlength="32" type="tel" name="fax" value="' . $request->request->get('fax') . '">');
$block1->contentRow($strings["comments"], '<textarea style="width: 400px; height: 50px;" name="comments" cols="35" rows="2">' . $request->request->get('comments') . '</textarea>');

$block1->contentTitle($strings["enter_password"]);
$block1->contentRow($strings["password"], '<input size="24" style="width: 250px;" maxlength="16" type="password" name="password" value="" required>');
$block1->contentRow($strings["confirm_password"], '<input size="24" style="width: 250px;" maxlength="16" type="password" name="password_confirm" value="" required>');
$block1->contentRow("", '<input type="submit" name="Save" value="' . $strings["save"] . '">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
