<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root: ../project_site/changepassword.php
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
** FILE: changepassword.php
**
** DESC:
**
** HISTORY:
**  23/03/2004  -   added new document info
**  24/03/2004  -   fixed session problem
**  24/03/2004  -   xhtml code
**  05/11/2004  -   fixed bug 837027
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/


use phpCollab\Members\Members;
use phpCollab\Teams\Teams;

$checkSession = "true";
include '../includes/library.php';

$members = new Members();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST["action"] == "update") {

        $teams = new Teams();

        $r = substr($_POST["old_password"], 0, 2);
        $encryptedOldPassword = crypt($_POST["old_password"], $r);

        if ($encryptedOldPassword != $passwordSession) {
            $error = $strings["old_password_error"];
        } else {
            if (
                empty($_POST["new_password"])
                || empty($_POST["confirm_password"])
                || $_POST["new_password"] != $_POST["confirm_password"]
            ) {
                $error = $strings["new_password_error"];
            } else {
                $encryptedNewPassword = phpCollab\Util::getPassword($_POST["new_password"]);

                if ($htaccessAuth == "true") {
                    $Htpasswd = new Htpasswd;
                    $listTeams = $teams->getTeamByMemberId($idSession);

                    if ($listTeams) {
                        foreach ($listTeams as $team) {
                            try {
                                $Htpasswd->initialize("files/" . $team["tea_pro_id"] . "/.htpasswd");
                                $Htpasswd->changePass($loginSession, $encryptedNewPassword);
                            }
                            catch (Exception $e) {
                                echo $e->getMessage();
                            }
                        }
                    }
                }

                phpCollab\Util::newConnectSql(
                    "UPDATE {$tableCollab["members"]} SET password = :password WHERE id = :member_id",
                    ["password" => $encryptedNewPassword, "member_id" => $idSession]
                );

                $_SESSION['passwordSession'] = $encryptedNewPassword;

                phpCollab\Util::headerFunction("changepassword.php?msg=update");
            }
        }
    }
}

$userDetail = $members->getMemberById($idSession);

if (empty($userDetail)) {
    phpCollab\Util::headerFunction("userlist.php?msg=blankUser");
}

$titlePage = $strings["change_password"];
include 'include_header.php';

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage = new phpCollab\Block();
    $blockPage->messageBox($msgLabel);
}

echo <<<FORM
    <form method="POST" 
        action="../projects_site/changepassword.php" 
        name="changepassword"
        class="noBorder">
            <table class="nonStriped">
                <tr>
                    <th colspan="2">{$strings["change_password"]}</th>
                </tr>
FORM;

if (!empty($error)) {
    echo '<tr><td colspan="2"><div class="alert error">' . $error .'</div></td></tr>';
}
echo <<<FORM
                <tr>
                    <th>*&nbsp;{$strings["old_password"]} :</th>
                    <td><input style="width: 150px;" type="password" name="old_password" value="" required></td>
                </tr>
                <tr>
                    <th>*&nbsp;{$strings["new_password"]} :</th>
                    <td><input style="width: 150px;" type="password" name="new_password" value="" required></td>
                </tr>
                <tr>
                    <th>*&nbsp;{$strings["confirm_password"]} :</th>
                    <td><input style="width: 150px;" type="password" name="confirm_password" value="" required></td>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                    <td colspan="2"><input name="submit" type="submit" value="{$strings["save"]}"></td>
                </tr>
            </table>
            <input type="hidden" name="action" value="update" />
        </form>
FORM;

include("include_footer.php");
