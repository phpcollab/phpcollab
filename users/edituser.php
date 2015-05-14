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


$checkSession = "true";
include_once('../includes/library.php');

if ($profilSession != "0") {
    Util::headerFunction('../general/permissiondenied.php?' . session_name() . '=' . session_id());
    exit;
}

//case update user
if ($id != "") {

    if ($id == "1" && $idSession == "1") {
        Util::headerFunction("../preferences/updateuser.php?" . session_name() . "=" . session_id());
        exit;
    }

//case update user
    if ($action == "update") {
        if ($htaccessAuth == "true") {
            $Htpasswd = new Htpasswd;
        }
        if (!preg_match("/^[A-Za-z0-9]+$/", $un)) {
            $error = $strings["alpha_only"];
        } else {

//test if login already exists
            $tmpquery = "WHERE mem.login = '$un' AND mem.login != '$unOld'";
            $existsUser = new request();
            $existsUser->openMembers($tmpquery);
            $comptExistsUser = count($existsUser->mem_id);
            if ($comptExistsUser != "0") {
                $error = $strings["user_already_exists"];
            } else {

//replace quotes by html code in name and address
                $fn = Util::convertData($fn);
                $tit = Util::convertData($tit);
                $c = Util::convertData($c);
                $em = Util::convertData($em);
                $wp = Util::convertData($wp);
                $hp = Util::convertData($hp);
                $mp = Util::convertData($mp);
                $fax = Util::convertData($fax);
                $last_page = Util::convertData($last_page);

                $tmpquery = "UPDATE " . $tableCollab["members"] . " SET login='$un',name='$fn',title='$tit',email_work='$em',phone_work='$wp',phone_home='$hp',mobile='$mp',fax='$fax',comments='$c',profil='$perm',last_page='$last_page' WHERE id = '$id'";
                Util::connectSql("$tmpquery");

                if ($htaccessAuth == "true") {
                    if ($un != $unOld) {
                        $tmpquery = "WHERE tea.member = '$id'";
                        $listProjects = new request();
                        $listProjects->openTeams($tmpquery);
                        $comptListProjects = count($listProjects->tea_id);

                        if ($comptListProjects != "0") {
                            for ($i = 0; $i < $comptListProjects; $i++) {
                                $Htpasswd->initialize("../files/" . $listProjects->tea_pro_id[$i] . "/.htpasswd");
                                $Htpasswd->renameUser($unOld, $un);
                            }
                        }
                    }
                }

//test if new password set
                if ($pw != "") {

//test if 2 passwords match
                    if ($pw != $pwa || $pwa == "") {
                        $error = $strings["new_password_error"];
                    } else {
                        $pw = Util::getPassword($pw);

                        if ($htaccessAuth == "true") {
                            if ($un == $unOld) {
                                $tmpquery = "WHERE tea.member = '$id'";
                                $listProjects = new request();
                                $listProjects->openTeams($tmpquery);
                                $comptListProjects = count($listProjects->tea_id);
                            }

                            if ($comptListProjects != "0") {
                                for ($i = 0; $i < $comptListProjects; $i++) {
                                    $Htpasswd->initialize("../files/" . $listProjects->tea_pro_id[$i] . "/.htpasswd");
                                    $Htpasswd->changePass($un, $pw);
                                }
                            }
                        }
                        $tmpquery = "UPDATE " . $tableCollab["members"] . " SET password='$pw' WHERE id = '$id'";
                        Util::connectSql("$tmpquery");
//if mantis bug tracker enabled
                        if ($enableMantis == "true") {
// Call mantis function for user changes..!!!
                            $f_access_level = $team_user_level; // Developer
                            include("../mantis/user_update.php");
                        }

                        Util::headerFunction("../users/listusers.php?msg=update&" . session_name() . "=" . session_id());
                        exit;
                    }
                } else {
//if mantis bug tracker enabled
                    if ($enableMantis == "true") {
// Call mantis function for user changes..!!!
                        $f_access_level = $team_user_level; // Developer
                        include("../mantis/user_update.php");
                    }
                    Util::headerFunction("../users/listusers.php?msg=update&" . session_name() . "=" . session_id());
                    exit;
                }
            }
        }
    }
    $tmpquery = "WHERE mem.id = '$id'";
    $detailUser = new request();
    $detailUser->openMembers($tmpquery);
    $comptDetailUser = count($detailUser->mem_id);

//test exists selected user, redirect to list if not
    if ($comptDetailUser == "0") {
        Util::headerFunction("../users/listusers.php?msg=blankUser&" . session_name() . "=" . session_id());
        exit;
    }

//set values in form
    $un = $detailUser->mem_login[0];
    $fn = $detailUser->mem_name[0];
    $tit = $detailUser->mem_title[0];

    $em = $detailUser->mem_email_work[0];
    $wp = $detailUser->mem_phone_work[0];
    $hp = $detailUser->mem_phone_home[0];
    $mp = $detailUser->mem_mobile[0];
    $fax = $detailUser->mem_fax[0];
    $last_page = $detailUser->mem_last_page[0];
    $c = $detailUser->mem_comments[0];
    $perm = $detailUser->mem_profil[0];

    $setTitle .= " : Edit User ($un)";

//set radio button with permissions value
    if ($perm == "1") {
        $checked1 = "checked";
    }
    if ($perm == "2") {
        $checked2 = "checked";
    }
    if ($perm == "3") {
        $checked3 = "checked";
    }
    if ($perm == "4") {
        $checked4 = "checked";
    }
    if ($perm == "5") {
        $checked5 = "checked";
    }
}

//case add user
if ($id == "") {
    $checked2 = "checked";

//case add user
    if ($action == "add") {
        if (!preg_match("/^[A-Za-z0-9]+$/", $un)) {
            $error = $strings["alpha_only"];
        } else {

//test if login already exists
            $tmpquery = "WHERE mem.login = '$un'";
            $existsUser = new request();
            $existsUser->openMembers($tmpquery);
            $comptExistsUser = count($existsUser->mem_id);
            if ($comptExistsUser != "0") {
                $error = $strings["user_already_exists"];
            } else {

//test if 2 passwords match
                if ($pw != $pwa || $pw == "") {
                    $error = $strings["new_password_error"];
                } else {

//replace quotes by html code in name and address
                    $fn = Util::convertData($fn);
                    $tit = Util::convertData($tit);
                    $c = Util::convertData($c);
                    $pw = Util::getPassword($pw);
                    $tmpquery1 = "INSERT INTO " . $tableCollab["members"] . "(login,name,title,email_work,phone_work,phone_home,mobile,fax,comments,password,profil,created,organization,timezone) VALUES('$un','$fn','$tit','$em','$wp','$hp','$mp','$fax','$c','$pw','$perm','$dateheure','1','0')";
                    Util::connectSql("$tmpquery1");
                    $tmpquery = $tableCollab["members"];
                    Util::getLastId($tmpquery);
                    $num = $lastId[0];
                    unset($lastId);
                    $tmpquery2 = "INSERT INTO " . $tableCollab["sorting"] . "(member) VALUES('$num')";
                    Util::connectSql("$tmpquery2");
                    $tmpquery3 = "INSERT INTO " . $tableCollab["notifications"] . "(member,taskAssignment,removeProjectTeam,addProjectTeam,newTopic,newPost,statusTaskChange,priorityTaskChange,duedateTaskChange,clientAddTask) VALUES ('$num','0','0','0','0','0','0','0','0','0')";
                    Util::connectSql("$tmpquery3");
//if mantis bug tracker enabled
                    if ($enableMantis == "true") {
// Call mantis function for user changes..!!!
                        $f_access_level = $team_user_level; // Developer
                        include("../mantis/create_new_user.php");
                    }
                    Util::headerFunction("../users/listusers.php?msg=add&" . session_name() . "=" . session_id());
                    exit;
                }
            }
        }
    }
}

$bodyCommand = "onLoad=\"document.user_editForm.un.focus();\"";
include('../themes/' . THEME . '/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/listusers.php?", $strings["user_management"], in));

if ($id == "") {
    $blockPage->itemBreadcrumbs($strings["add_user"]);
}
if ($id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../users/viewuser.php?id=$id", $detailUser->mem_login[0], in));
    $blockPage->itemBreadcrumbs($strings["edit_user"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include('../includes/messages.php');
    $blockPage->messagebox($msgLabel);
}

$block1 = new block();

if ($id == "") {
    $block1->form = "user_edit";
    $block1->openForm("../users/edituser.php?id=$id&action=add&" . session_name() . "=" . session_id() . "#" . $block1->form . "Anchor");
}
if ($id != "") {
    $block1->form = "user_edit";
    $block1->openForm("../users/edituser.php?id=$id&action=update&" . session_name() . "=" . session_id() . "#" . $block1->form . "Anchor");
}

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if ($id == "") {
    $block1->heading($strings["add_user"]);
}
if ($id != "") {
    $block1->heading($strings["edit_user"] . " : " . $detailUser->mem_login[0]);
}

$block1->openContent();

if ($id == "") {
    $block1->contentTitle($strings["enter_user_details"]);
}
if ($id != "") {
    $block1->contentTitle($strings["edit_user_details"]);
}

$block1->contentRow($strings["user_name"], "<input size=\"24\" style=\"width: 250px;\" maxlength=\"16\" type=\"text\" name=\"un\" value=\"$un\"><input type=\"hidden\" name=\"unOld\" value=\"$un\">");
$block1->contentRow($strings["full_name"], "<input size=\"24\" style=\"width: 250px;\" maxlength=\"64\" type=\"text\" name=\"fn\" value=\"$fn\">");
$block1->contentRow($strings["title"], "<input size=\"24\" style=\"width: 250px;\" maxlength=\"128\" type=\"text\" name=\"tit\" value=\"$tit\">");
$block1->contentRow($strings["email"], "<input size=\"24\" style=\"width: 250px;\" maxlength=\"128\" type=\"text\" name=\"em\" value=\"$em\">");
$block1->contentRow($strings["work_phone"], "<input size=\"14\" style=\"width: 150px;\" maxlength=\"32\" type=\"text\" name=\"wp\" value=\"$wp\">");
$block1->contentRow($strings["home_phone"], "<input size=\"14\" style=\"width: 150px;\" maxlength=\"32\" type=\"text\" name=\"hp\" value=\"$hp\">");
$block1->contentRow($strings["mobile_phone"], "<input size=\"14\" style=\"width: 150px;\" maxlength=\"32\" type=\"text\" name=\"mp\" value=\"$mp\">");
$block1->contentRow($strings["fax"], "<input size=\"14\" style=\"width: 150px;\" maxlength=\"32\" type=\"text\" name=\"fax\" value=\"$fax\">");
if ($lastvisitedpage == true) {
    $block1->contentRow($strings["last_page"], "<input size=\"24\" style=\"width: 250px;\" maxlength=\"255\" type=\"text\" name=\"last_page\" value=\"$last_page\">");
}
$block1->contentRow($strings["comments"], "<textarea style=\"width: 350px; height: 60px;\" name=\"c\" cols=\"45\" rows=\"5\">$c</textarea>");

if ($id == "") {
    $block1->contentTitle($strings["enter_password"]);
}
if ($id != "") {
    $block1->contentTitle($strings["change_password_user"]);
}

$block1->contentRow($strings["password"], "<input size=\"24\" style=\"width: 250px;\" maxlength=\"16\" type=\"password\" name=\"pw\" value=\"\">");
$block1->contentRow($strings["confirm_password"], "<input size=\"24\" style=\"width: 250px;\" maxlength=\"16\" type=\"password\" name=\"pwa\" value=\"\">");

// if the user isn't a client user then i give the opportunity to change the permission
if ($perm != '3') {
    $block1->contentTitle($strings["select_permissions"]);
    $block1->contentRow("<input type='radio' name='perm' value='1' $checked1 />", "<b>" . $strings["project_manager_permissions"] . "</b>");
    $block1->contentRow("<input type='radio' name='perm' value='2' $checked2 />", "<b>" . $strings["user_permissions"] . "</b>");
    $block1->contentRow("<input type='radio' name='perm' value='4' $checked4 />", "<b>" . $strings["disabled_permissions"] . "</b>");
    $block1->contentRow("<input type='radio' name='perm' value='5' $checked5 />", "<b>" . $strings["project_manager_administrator_permissions"] . "</b>");
} else {
    $block1->contentRow('', "<input type='hidden' name='perm' value='3' />");
}

$block1->contentRow("", "<input type='submit' name='Save' value='" . $strings["save"] . "'>");

$block1->closeContent();
$block1->closeForm();

include('../themes/' . THEME . '/footer.php');
?>