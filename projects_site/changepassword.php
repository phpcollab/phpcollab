<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root: ../project_site/changepassword.php
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

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : " . $strings["change_password"];

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "update") {

                $teams = $container->getTeams();

                $r = substr($request->request->get('old_password'), 0, 2);

                if (
                    empty($request->request->get('new_password'))
                    || empty($request->request->get('confirm_password'))
                    || $request->request->get('new_password') != $request->request->get('confirm_password')
                ) {
                    $error = $strings["new_password_error"];
                } else {
                    if ($htaccessAuth == "true") {
                        $Htpasswd = $container->getHtpasswdService();
                        $listTeams = $teams->getTeamByMemberId($session->get("id"));

                        if ($listTeams) {
                            foreach ($listTeams as $team) {
                                try {
                                    $Htpasswd->initialize("files/" . $team["tea_pro_id"] . "/.htpasswd");
                                    $Htpasswd->changePass($session->get("login"), phpCollab\Util::getPassword($request->request->get('new_password')));
                                } catch (Exception $e) {
                                    $logger->error('Project Site (password reset)', ['Exception message', $e->getMessage()]);
                                    $error = $strings["rest_password_error"];
                                }
                            }
                        }
                    }

                    try {
                        $members->setPassword($session->get("id"), $request->request->get('new_password'));

                        phpCollab\Util::headerFunction("changepassword.php?msg=update");
                    } catch (Exception $exception) {
                        $logger->error('Project Site (password reset)', ['Exception message', $e->getMessage()]);
                        $error = $strings["rest_password_error"];
                    }
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Project Site: Change password',
            '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
            '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

$userDetail = $members->getMemberById($session->get("id"));

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
        <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
            <table class="nonStriped">
                <tr>
                    <th colspan="2">{$strings["change_password"]}</th>
                </tr>
FORM;

if (!empty($error)) {
    echo '<tr><td colspan="2"><div class="alert error">' . $error . '</div></td></tr>';
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
