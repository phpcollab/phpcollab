<?php
/*
** Application name: phpCollab
** Last Edit page: 02/08/2007
** Path by root: ../includes/calendar.php
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


use phpCollab\Members\Members;
use phpCollab\Notifications\Notifications;
use phpCollab\Sorting\Sorting;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';

$members = new Members();
$teams = new Teams();

if ($profilSession != "0") {
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

//case update user
if (!empty($_GET["id"])) {
    if ($id == "1" && $idSession == "1") {
        phpCollab\Util::headerFunction("../preferences/updateuser.php");
    }

    //case update user
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if ($_POST["action"] == "update") {

            $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
            $oldUsername = filter_input(INPUT_POST, "username_old", FILTER_SANITIZE_STRING);
            $password = $_POST["password"];
            $passwordConfirm = $_POST["password_confirm"];
            $fullName = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_STRING);
            $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $phoneWork = filter_input(INPUT_POST, "phone_work", FILTER_SANITIZE_STRING);
            $phoneMobile = filter_input(INPUT_POST, "phone_mobile", FILTER_SANITIZE_STRING);
            $phoneHome = filter_input(INPUT_POST, "phone_home", FILTER_SANITIZE_STRING);
            $fax = filter_input(INPUT_POST, "fax", FILTER_SANITIZE_STRING);
            $lastPage = filter_input(INPUT_POST, "last_page", FILTER_SANITIZE_STRING);
            $profile = filter_input(INPUT_POST, "profile", FILTER_SANITIZE_NUMBER_INT);
            $comments = htmlspecialchars($_POST["comments"], ENT_QUOTES, 'UTF-8');


            if ($htaccessAuth == "true") {
                $Htpasswd = new Htpasswd;
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

                    $listTeams = $teams->getTeamByMemberId($id);

                    try {
                        $members->updateMember($id, $username, $fullName, $email, $title, null, $phoneWork, $phoneHome, $phoneMobile, $fax, $lastPage, $comments, $profile);

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
                            if ($password != $passwordConfirm || $passwordConfirm == "") {
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

                                $members->setPassword($id, $password);

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
                        echo "<h3>{$e->getMessage()}</h3>";
                        $error = $strings["action_not_allowed"];
                    }

                }
            }
        }
    }

    $userDetail = $members->getMemberById($_GET["id"]);

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
if (empty($_GET["id"])) {
    $checked2 = "checked";

    //case add user
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($action == "add") {

            $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
            $oldUsername = filter_input(INPUT_POST, "username_old", FILTER_SANITIZE_STRING);
            $password = $_POST["password"];
            $passwordConfirm = $_POST["password_confirm"];
            $fullName = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_STRING);
            $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $phoneWork = filter_input(INPUT_POST, "phone_work", FILTER_SANITIZE_STRING);
            $phoneMobile = filter_input(INPUT_POST, "phone_mobile", FILTER_SANITIZE_STRING);
            $phoneHome = filter_input(INPUT_POST, "phone_home", FILTER_SANITIZE_STRING);
            $fax = filter_input(INPUT_POST, "fax", FILTER_SANITIZE_STRING);
            $lastPage = filter_input(INPUT_POST, "last_page", FILTER_SANITIZE_STRING);
            $profile = filter_input(INPUT_POST, "profile", FILTER_SANITIZE_NUMBER_INT);
            $comments = htmlspecialchars($_POST["comments"], ENT_QUOTES, 'UTF-8');

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
                            $sorting = new Sorting();
                            $notifications = new Notifications();

                            //replace quotes by html code in name and address
                            $full_name = phpCollab\Util::convertData($full_name);
                            $title = phpCollab\Util::convertData($title);
                            $comments = phpCollab\Util::convertData($comments);
                            $password = phpCollab\Util::getPassword($password);

                            $newUserId = $members->addMember($username, $fullName, $email, $password, $profile, $title, 1, $phoneWork, $phoneHome, $phoneMobile, $fax, $comments, $dateheure, 0);

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
            $username = $_POST["username"];
            $full_name = $_POST["full_name"];
            $title = $_POST["title"];

            $email = $_POST["email"];
            $phone_work = $_POST["phone_work"];
            $phone_home = $_POST["phone_home"];
            $phone_mobile = $_POST["phone_mobile"];
            $fax = $_POST["fax"];
            $last_page = $_POST["last_page"];
            $comments = $_POST["comments"];
            $profile = $_POST["profile"];

        }
    }
}
    $bodyCommand = 'onLoad="document.user_editForm.un.focus();"';
    include APP_ROOT . '/themes/' . THEME . '/header.php';

    $blockPage = new phpCollab\Block();
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../users/listusers.php?", $strings["user_management"], "in"));

    if ($id == "") {
        $blockPage->itemBreadcrumbs($strings["add_user"]);
    }
    if ($id != "") {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../users/viewuser.php?id=$id", $userDetail["mem_login"], "in"));
        $blockPage->itemBreadcrumbs($strings["edit_user"]);
    }
    $blockPage->closeBreadcrumbs();

    if ($msg != "") {
        include '../includes/messages.php';
        $blockPage->messageBox($msgLabel);
    }

    $block1 = new phpCollab\Block();

    if (empty($_GET["id"])) {
        $block1->form = "user_edit";
        $block1->openForm("../users/edituser.php?id=$id#" . $block1->form . "Anchor");
    }

    if (!empty($_GET["id"])) {
        $block1->form = "user_edit";
        $block1->openForm("../users/edituser.php?id=$id#" . $block1->form . "Anchor");
    }

    if (!empty($error)) {
        $block1->headingError($strings["errors"]);
        $block1->contentError($error);
    }

    if (empty($_GET["id"])) {
        $block1->heading($strings["add_user"]);
    }
    if (!empty($_GET["id"])) {
        $block1->heading($strings["edit_user"] . " : " . $userDetail["mem_login"]);
    }

    $block1->openContent();

    if (empty($_GET["id"])) {
        $block1->contentTitle($strings["enter_user_details"]);
    }
    if (!empty($_GET["id"])) {
        $block1->contentTitle($strings["edit_user_details"]);
    }

    $block1->contentRow($strings["user_name"], '<input size="24" style="width: 250px;" maxlength="16" type="text" name="username" required value="' . $username . '" autocomplete="off"><input type="hidden" name="username_old" value="' . $username . '">');
    $block1->contentRow($strings["full_name"], '<input size="24" style="width: 250px;" maxlength="64" type="text" name="full_name" required value="' . $full_name . '">');
    $block1->contentRow($strings["title"], '<input size="24" style="width: 250px;" maxlength="128" type="text" name="title" value="' . $title . '">');
    $block1->contentRow($strings["email"], '<input size="24" style="width: 250px;" maxlength="128" type="text" name="email" required value="' . $email . '">');
    $block1->contentRow($strings["work_phone"], '<input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_work" value="' . $phone_work . '">');
    $block1->contentRow($strings["home_phone"], '<input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_home" value="' . $phone_home . '">');
    $block1->contentRow($strings["mobile_phone"], '<input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_mobile" value="' . $phone_mobile . '">');
    $block1->contentRow($strings["fax"], '<input size="14" style="width: 150px;" maxlength="32" type="text" name="fax" value="' . $fax . '">');
    if ($lastvisitedpage === true) {
        $block1->contentRow($strings["last_page"], '<input size="24" style="width: 250px;" maxlength="255" type="text" name="last_page" value="' . $last_page . '">');
    }
    $block1->contentRow($strings["comments"], '<textarea style="width: 350px; height: 60px;" name="comments" cols="45" rows="5">' . $comments . '</textarea>');

    if ($id == "") {
        $block1->contentTitle($strings["enter_password"]);
    }
    if ($id != "") {
        $block1->contentTitle($strings["change_password_user"]);
    }

    $block1->contentRow($strings["password"], '<input size="24" style="width: 250px;" maxlength="16" type="password" name="password" value="" autocomplete="off">');
    $block1->contentRow($strings["confirm_password"], '<input size="24" style="width: 250px;" maxlength="16" type="password" name="password_confirm" value="">');

// if the user isn't a client user then i give the opportunity to change the permission
    if ($profile != '3') {
        $block1->contentTitle($strings["select_permissions"]);
        $block1->contentRow('<input type="radio" name="profile" value="1" ' . $checked1 . ' />', '<b>' . $strings["project_manager_permissions"] . '</b>');
        $block1->contentRow('<input type="radio" name="profile" value="2" ' . $checked2 . ' />', '<b>' . $strings["user_permissions"] . '</b>');
        $block1->contentRow('<input type="radio" name="profile" value="4" ' . $checked4 . ' />', '<b>' . $strings["disabled_permissions"] . '</b>');
        $block1->contentRow('<input type="radio" name="profile" value="5" ' . $checked5 . ' />', '<b>' . $strings["project_manager_administrator_permissions"] . '</b>');
    } else {
        $block1->contentRow('', '<input type="hidden" name="perm" value="3" />');
    }

    $block1->contentRow("", '<input type="submit" name="Save" value="' . $strings["save"] . '">');

    $block1->closeContent();

    if (empty($_GET["id"])) {
        echo '<input type="hidden" name="action" value="add" />';
    }

    if (!empty($_GET["id"])) {
        echo '<input type="hidden" name="action" value="update" />';
    }


    $block1->closeForm();

    include APP_ROOT . '/themes/' . THEME . '/footer.php';
