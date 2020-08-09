<?php

use phpCollab\Organizations\Organizations;

$checkSession = "true";
include_once '../includes/library.php';

$orgId = $request->query->get('orgid');
$userId = $request->query->get('userid');

if (empty($userId) || empty($orgId)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

$organizations = new Organizations();

$clientDetail = $organizations->getOrganizationById($orgId);
$comptDetailClient = count($detailClient);

$userDetail = $members->getMemberById($userId);
$comptUserDetail = count($userDetail);

//case update client user
if ($request->query->get('action') == "update") {
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

        if (!empty($request->request->get('user_name_old'))) {
            $user_login_old = filter_var($request->request->get('user_name_old'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('full_name'))) {
            $user_full_name = filter_var($request->request->get('full_name'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('organization'))) {
            $user_organization = filter_var($request->request->get('organization'), FILTER_SANITIZE_STRING);
        }

        if (!empty($request->request->get('title'))) {
            $user_ = filter_var($request->request->get('title'), FILTER_SANITIZE_STRING);
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
            if ($members->checkIfMemberExists($user_login, $user_login_old)) {
                $error = $strings["user_already_exists"];
            } else {
                try {
                    $updated = $members->updateMember($userId, $user_login, $user_full_name, $user_email_work, $user_title, $user_organization, $user_phone_work, $user_phone_home, $user_phone_mobile, $user_fax, $user_last_page, $user_comments);

                    if ($user_password != "") {

                        //test if 2 passwords match
                        if ($user_password != $user_password_confirm || $user_password_confirm == "") {
                            $error = $strings["new_password_error"];
                        } else {
                            try {
                                $members->setPassword($userId, $user_password);
                            } catch (Exception $e) {
                                echo 'Message: ' . $e->getMessage();
                            }
                            phpCollab\Util::headerFunction("../clients/viewclient.php?msg=update&id=$user_organization");
                        }
                    } else {
                        //if mantis bug tracker enabled
                        if ($enableMantis == "true") {
                            // Call mantis function for user changes..!!!
                            $f_access_level = $client_user_level; // reporter
                            include '../mantis/user_update.php';
                        }
                        phpCollab\Util::headerFunction("../clients/viewclient.php?msg=update&id=$user_organization");
                    }
                } catch (Exception $e) {
                    echo $error = $e->getMessage();
                }
            }
        }
    }
}

//set values in form
$user_name = $userDetail["mem_login"];
$full_name = $userDetail["mem_name"];
$organization = $userDetail["mem_organization"];
$title = $userDetail["mem_title"];
$email_work = $userDetail["mem_email_work"];
$phone_work = $userDetail["mem_phone_work"];
$phone_home = $userDetail["mem_phone_home"];
$phone_mobile = $userDetail["mem_mobile"];
$fax = $userDetail["mem_fax"];
$last_page = $userDetail["mem_last_page"];
$comments = $userDetail["mem_comments"];

$bodyCommand = "onLoad=\"document.client_user_editForm.un.focus();\"";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["organizations"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=$orgId", $clientDetail["org_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/viewclientuser.php?organization=$orgId&id=" . $userDetail["mem_id"], $userDetail["mem_login"], "in"));
$blockPage->itemBreadcrumbs($strings["edit_client_user"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "client_user_edit";
$block1->openForm("../users/updateclientuser.php?action=update&orgid=$orgId&userid=$userId");

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["edit_client_user"] . " : $user_name");

$block1->openContent();
$block1->contentTitle($strings["edit_user_details"]);

echo <<<HTML
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["user_name"]} :</td>
    <td>
        <input type="hidden" name="id" value="{$userId}">
        <input size="24" style="width: 250px;" maxlength="16" type="text" name="user_name" value="{$user_name}">
        <input type="hidden" name="user_name_old" value="{$user_name}">
    </td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["full_name"]} :</td>
    <td><input size="24" style="width: 250px;" maxlength="64" type="text" name="full_name" value="{$full_name}"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["title"]} :</td>
    <td><input size="24" style="width: 250px;" maxlength="64" type="text" name="title" value="{$title}"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["organization"]} :</td>
    <td>
        <select name="organization">";
HTML;

$selectClient = $organizations->getListOfOrganizations('org.name ASC');

if ($selectClient) {
    foreach ($selectClient as $client) {
        if ($userDetail["mem_organization"] == $client["org_id"]) {
            echo "<option value='" . $client["org_id"] . "' selected>" . $client["org_name"] . "</option>";
        } else {
            echo "<option value='" . $client["org_id"] . "'>" . $client["org_name"] . "</option>";
        }
    }
} else {
    echo "none";
}

echo <<<HTML
        </select>
    </td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["email"]} :</td>
    <td><input size="24" style="width: 250px;" maxlength="128" type="text" name="email_work" value="{$email_work}"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["work_phone"]} :</td>
    <td><input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_work" value="{$phone_work}"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["home_phone"]} :</td>
    <td><input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_home" value="{$phone_home}"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["mobile_phone"]} :</td>
    <td><input size="14" style="width: 150px;" maxlength="32" type="text" name="phone_mobile" value="{$phone_mobile}"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["fax"]} :</td>
    <td class="infoValueField" width="634"><input size="14" style="width: 150px;" maxlength="32" type="text" name="fax" value="{$fax}"></td>
</tr>
HTML;

if ($lastvisitedpage === true) {
    echo <<<HTML
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["last_page"]} :</td>
    <td class="infoValueField" width="634"><input size="14" style="width: 150px;" maxlength="32" type="text" name="last_page" value="{$last_page}"></td>
</tr>
HTML;
}
echo <<<HTML
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["comments"]} :</td>
    <td><textarea style="width: 400px; height: 50px;" name="comments" cols="35" rows="2">{$comments}</textarea></td>
</tr>
HTML;

$block1->contentTitle($strings["change_password_user"]);

echo <<<HTML
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["password"]} :</td>
    <td><input size="24" style="width: 250px;" maxlength="16" type="password" name="password" value=""></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["confirm_password"]} :</td>
    <td><input size="24" style="width: 250px;" maxlength="16" type="password" name="password_confirm" value=""></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td><input type="submit" name="Save" value="{$strings["save"]}"></td>
</tr>
HTML;
$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
