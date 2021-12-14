<?php
/*
** Application name: phpCollab
** Last Edit page: 02/08/2007
** Path by root: ../includes/calendar.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: edituser.php
**
** DESC: Screen:	displays the details of a client user
**
** HISTORY:
** 	02/08/2007	-	added Last Viewed Page code - Mindblender
**
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/


use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $teams = $container->getTeams();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

if ($session->get('profile') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

// Set default form values
$username = null;
$full_name = null;
$title = null;
$email = null;
$phone_work = null;
$phone_home = null;
$phone_mobile = null;
$fax = null;
$last_page = null;
$comments = null;
$profile = '';

// Redirect to Preferences if it is the "root/super" admin
if ($request->query->get('id') == "1" && $session->get("id") == "1") {
    phpCollab\Util::headerFunction("../preferences/updateuser.php");
}

//case update user
if (!empty($request->query->get('id'))) {
    //case update user
    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($request->request->get('action') == "update") {

                    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                    $oldUsername = filter_input(INPUT_POST, "username_old", FILTER_SANITIZE_STRING);
                    $password = $request->request->get('password');
                    $passwordConfirm = $request->request->get('password_confirm');
                    $fullName = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_STRING);
                    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
                    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                    $phoneWork = filter_input(INPUT_POST, "phone_work", FILTER_SANITIZE_STRING);
                    $phoneMobile = filter_input(INPUT_POST, "phone_mobile", FILTER_SANITIZE_STRING);
                    $phoneHome = filter_input(INPUT_POST, "phone_home", FILTER_SANITIZE_STRING);
                    $fax = filter_input(INPUT_POST, "fax", FILTER_SANITIZE_STRING);
                    $lastPage = filter_input(INPUT_POST, "last_page", FILTER_SANITIZE_STRING);
                    $profile = filter_input(INPUT_POST, "profile", FILTER_SANITIZE_NUMBER_INT);
                    $comments = htmlspecialchars($request->request->get('comments'), ENT_QUOTES);


                    if ($htaccessAuth == "true") {
                        $Htpasswd = $container->getHtpasswdService();
                    }
                    if (!preg_match("/^[A-Za-z0-9]+$/", $username)) {
                        $error = $strings["alpha_only"];
                    } else {
                        if ($members->checkIfMemberExists($username, $oldUsername)) {
                            $error = $strings["user_already_exists"];
                        } else {
                            $fullName = phpCollab\Util::convertData($fullName);
                            $title = phpCollab\Util::convertData($title);
                            $comments = phpCollab\Util::convertData($comments);
                            $email = phpCollab\Util::convertData($email);
                            $phone_work = phpCollab\Util::convertData($phone_work);
                            $phone_home = phpCollab\Util::convertData($phone_home);
                            $phone_mobile = phpCollab\Util::convertData($phone_mobile);
                            $fax = phpCollab\Util::convertData($fax);
                            $last_page = phpCollab\Util::convertData($last_page);

                            $listTeams = $teams->getTeamByMemberId($request->query->get("id"));

                            try {
                                $members->updateMember($request->query->get("id"), $username, $fullName, $email, $title,
                                    1, $phoneWork, $phoneHome, $phoneMobile, $fax, $lastPage, $comments, $profile);

                                if ($htaccessAuth == "true") {
                                    if ($username != $oldUsername) {
                                        if ($listTeams) {
                                            foreach ($listTeams as $team) {
                                                $Htpasswd->initialize("../files/" . $team["tea_pro_id"] . "/.htpasswd");
                                                $Htpasswd->renameUser($oldUsername, $username);
                                            }
                                        }
                                    }
                                }

                                //test if new password set
                                if ($password != "") {

                                    //test if 2 passwords match
                                    if ($password != $passwordConfirm) {
                                        $error = $strings["new_password_error"];
                                    } else {
                                        $password = phpCollab\Util::getPassword($password);

                                        if ($htaccessAuth == "true") {
                                            if ($username == $oldUsername && $listTeams) {
                                                foreach ($listTeams as $team) {
                                                    $Htpasswd->initialize("../files/" . $team["tea_pro_id"] . "/.htpasswd");
                                                    $Htpasswd->changePass($username, $password);
                                                }
                                            }
                                        }

                                        $members->setPassword($request->query->get("id"), $password);

                                        //if mantis bug tracker enabled
                                        if ($enableMantis == "true") {
                                            // Call mantis function for user changes..!!!
                                            $f_access_level = $team_user_level; // Developer
                                            include '../mantis/user_update.php';
                                        }
                                    }
                                } else {
                                    //if mantis bug tracker enabled
                                    if ($enableMantis == "true") {
                                        // Call mantis function for user changes..!!!
                                        $f_access_level = $team_user_level; // Developer
                                        include '../mantis/user_update.php';
                                    }
                                }
                                phpCollab\Util::headerFunction("../users/listusers.php?msg=update");
                            } catch (Exception $e) {
                                $logger->error($e->getMessage());
                                $error = $strings["action_not_allowed"];
                            }

                        }
                    }
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Users: Edit user',
                '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
                '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }
    }

    $userDetail = $members->getMemberById($request->query->get('id'));

    //test exists selected user, redirect to list if not
    if (empty($userDetail)) {
        phpCollab\Util::headerFunction("../users/listusers.php?msg=blankUser");
    }

    //set values in form
    $username = $userDetail["mem_login"];
    $full_name = $userDetail["mem_name"];
    $title = $userDetail["mem_title"];

    $email = $userDetail["mem_email_work"];
    $phone_work = $userDetail["mem_phone_work"];
    $phone_home = $userDetail["mem_phone_home"];
    $phone_mobile = $userDetail["mem_mobile"];
    $fax = $userDetail["mem_fax"];
    $last_page = $userDetail["mem_last_page"];
    $comments = $userDetail["mem_comments"];
    $profile = $userDetail["mem_profil"];

    $setTitle .= " : Edit User ($username)";

    //set radio button with permissions value
    if ($profile == "1") {
        $checked1 = "checked";
    }
    if ($profile == "2") {
        $checked2 = "checked";
    }
    if ($profile == "3") {
        $checked3 = "checked";
    }
    if ($profile == "4") {
        $checked4 = "checked";
    }
    if ($profile == "5") {
        $checked5 = "checked";
    }
}

//case add user
if (empty($request->query->get('id'))) {
    $setTitle .= " : Add User";
    $checked2 = "checked";

    //case add user
    if ($request->isMethod('post')) {
        if ($request->request->get("action") == "add") {

            $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
            $oldUsername = filter_input(INPUT_POST, "username_old", FILTER_SANITIZE_STRING);
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');
            $fullName = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_STRING);
            $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $phoneWork = filter_input(INPUT_POST, "phone_work", FILTER_SANITIZE_STRING);
            $phoneMobile = filter_input(INPUT_POST, "phone_mobile", FILTER_SANITIZE_STRING);
            $phoneHome = filter_input(INPUT_POST, "phone_home", FILTER_SANITIZE_STRING);
            $fax = filter_input(INPUT_POST, "fax", FILTER_SANITIZE_STRING);
            $lastPage = filter_input(INPUT_POST, "last_page", FILTER_SANITIZE_STRING);
            $profile = filter_input(INPUT_POST, "profile", FILTER_SANITIZE_NUMBER_INT);
            $comments = htmlspecialchars($request->request->get('comments'), ENT_QUOTES);

            if (!preg_match("/^[A-Za-z0-9]+$/", $username)) {
                $error = $strings["alpha_only"];
            } else {

                //test if login already exists
                if ($members->checkIfMemberExists($username, $oldUsername)) {
                    $error = $strings["user_already_exists"];
                } else {

                    //test if 2 passwords match
                    if ($password != $passwordConfirm || $password == "") {
                        $error = $strings["new_password_error"];
                    } else {
                        try {
                            $sorting = $container->getSortingLoader();
                            $notifications = $container->getNotificationsManager();

                            //replace quotes by html code in name and address
                            $full_name = phpCollab\Util::convertData($full_name);
                            $title = phpCollab\Util::convertData($title);
                            $comments = phpCollab\Util::convertData($comments);
                            $password = phpCollab\Util::getPassword($password);

                            $newUserId = $members->addMember($username, $fullName, $email, $password, $profile, $title,
                                1, $phoneWork, $phoneHome, $phoneMobile, $fax, $comments, $dateheure);

                            $sorting->addMember($newUserId);

                            $notifications->addMember($newUserId);

                            //if mantis bug tracker enabled
                            if ($enableMantis == "true") {
                                // Call mantis function for user changes..!!!
                                $f_access_level = $team_user_level; // Developer
                                include '../mantis/create_new_user.php';
                            }

                            phpCollab\Util::headerFunction("../users/listusers.php?msg=add");

                        } catch (Exception $e) {
                            $error = $strings["action_not_allowed"];
                        }

                    }
                }
            }

            // Populate form fields, incase there was an error
            $username = $request->request->get('username');
            $full_name = $request->request->get('full_name');
            $title = $request->request->get('title');

            $email = $request->request->get('email');
            $phone_work = $request->request->get('phone_work');
            $phone_home = $request->request->get('phone_home');
            $phone_mobile = $request->request->get('phone_mobile');
            $fax = $request->request->get('fax');
            $last_page = $request->request->get('last_page');
            $comments = $request->request->get('comments');
            $profile = $request->request->get('profile');

        }
    }
}
$bodyCommand = 'onLoad="document.user_editForm.username.focus();"';
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/listusers.php?", $strings["user_management"], "in"));

if ($request->query->get("id") == "") {
    $blockPage->itemBreadcrumbs($strings["add_user"]);
}
if ($request->query->get("id") != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../users/viewuser.php?id={$request->query->get("id")}",
        $userDetail["mem_login"], "in"));
    $blockPage->itemBreadcrumbs($strings["edit_user"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if (empty($request->query->get('id'))) {
    $block1->form = "user_edit";
    $block1->openForm("../users/edituser.php?id={$request->query->get("id")}#" . $block1->form . "Anchor", null,
        $csrfHandler);
}

if (!empty($request->query->get('id'))) {
    $block1->form = "user_edit";
    $block1->openForm("../users/edituser.php?id={$request->query->get("id")}#" . $block1->form . "Anchor", null,
        $csrfHandler);
}

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if (empty($request->query->get('id'))) {
    $block1->heading($strings["add_user"]);
}
if (!empty($request->query->get('id'))) {
    $block1->heading($strings["edit_user"] . " : " . $userDetail["mem_login"]);
}

$block1->openContent();

if (empty($request->query->get('id'))) {
    $block1->contentTitle($strings["enter_user_details"]);
}
if (!empty($request->query->get('id'))) {
    $block1->contentTitle($strings["edit_user_details"]);
}

$block1->contentRow($strings["user_name"],
    '<input size="24" style="width: 250px;" maxlength="16" type="text" name="username" required value="' . $username . '" autocomplete="off"><input type="hidden" name="username_old" value="' . $username . '">');
$block1->contentRow($strings["full_name"],
    '<input size="24" style="width: 250px;" maxlength="64" type="text" name="full_name" required value="' . $full_name . '">');
$block1->contentRow($strings["title"],
    '<input size="24" style="width: 250px;" maxlength="128" type="text" name="title" value="' . $title . '">');
$block1->contentRow($strings["email"],
    '<input size="24" style="width: 250px;" maxlength="128" type="text" name="email" required value="' . $email . '">');
$block1->contentRow($strings["work_phone"],
    '<input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_work" value="' . $phone_work . '">');
$block1->contentRow($strings["home_phone"],
    '<input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_home" value="' . $phone_home . '">');
$block1->contentRow($strings["mobile_phone"],
    '<input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_mobile" value="' . $phone_mobile . '">');
$block1->contentRow($strings["fax"],
    '<input size="14" style="width: 150px;" maxlength="32" type="text" name="fax" value="' . $fax . '">');
if ($lastvisitedpage === true) {
    $block1->contentRow($strings["last_page"],
        '<input size="24" style="width: 250px;" maxlength="255" type="text" name="last_page" value="' . $last_page . '">');
}
$block1->contentRow($strings["comments"],
    '<textarea style="width: 350px; height: 60px;" name="comments" cols="45" rows="5">' . $comments . '</textarea>');

if (empty($request->query->get("id"))) {
    $block1->contentTitle($strings["enter_password"]);
}
if (!empty($request->query->get("id"))) {
    $block1->contentTitle($strings["change_password_user"]);
}

$block1->contentRow($strings["password"],
    '<input size="24" style="width: 250px;" maxlength="16" type="password" name="password" value="" autocomplete="off">');
$block1->contentRow($strings["confirm_password"],
    '<input size="24" style="width: 250px;" maxlength="16" type="password" name="password_confirm" value="">');

// if the user isn't a client user then i give the opportunity to change the permission
if ($profile != '3') {
    $block1->contentTitle($strings["select_permissions"]);
    $block1->contentRow('<label><input type="radio" name="profile" value="1" ' . $checked1 . ' />',
        '<b>' . $strings["project_manager_permissions"] . '</b></label>');
    $block1->contentRow('<label><input type="radio" name="profile" value="2" ' . $checked2 . ' />',
        '<b>' . $strings["user_permissions"] . '</b></label>');
    $block1->contentRow('<label><input type="radio" name="profile" value="4" ' . $checked4 . ' />',
        '<b>' . $strings["disabled_permissions"] . '</b></label>');
    $block1->contentRow('<label><input type="radio" name="profile" value="5" ' . $checked5 . ' />',
        '<b>' . $strings["project_manager_administrator_permissions"] . '</b></label>');
} else {
    $block1->contentRow('', '<input type="hidden" name="perm" value="3" />');
}

$block1->contentRow("", '<input type="submit" name="Save" value="' . $strings["save"] . '">');

$block1->closeContent();

if (empty($request->query->get('id'))) {
    echo '<input type="hidden" name="action" value="add" />';
}

if (!empty($request->query->get('id'))) {
    echo '<input type="hidden" name="action" value="update" />';
}


$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
