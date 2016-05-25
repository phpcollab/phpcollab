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
** FILE: updateclientuser.php
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
include_once '../includes/library.php';

$tmpquery = "WHERE org.id = '$organization'";
$detailClient = new phpCollab\Request();
$detailClient->openOrganizations($tmpquery);
$comptDetailClient = count($detailClient->org_id);

$tmpquery = "WHERE mem.id = '$id'";
$userDetail = new phpCollab\Request();
$userDetail->openMembers($tmpquery);
$comptUserDetail = count($userDetail->mem_id);

//case update client user
if ($action == "update") {
    if (!preg_match("/^[A-Za-z0-9]+$/", $un)) {
        $error = $strings["alpha_only"];
    } else {

//test if login already exists
        $tmpquery = "WHERE mem.login = '$un' AND mem.login != '$unOld'";
        $existsUser = new phpCollab\Request();
        $existsUser->openMembers($tmpquery);
        $comptExistsUser = count($existsUser->mem_id);
        if ($comptExistsUser != "0") {
            $error = $strings["user_already_exists"];
        } else {

//replace quotes by html code in name
            $fn = phpCollab\Util::convertData($fn);
            $tit = phpCollab\Util::convertData($tit);
            $c = phpCollab\Util::convertData($c);
            $em = phpCollab\Util::convertData($em);
            $wp = phpCollab\Util::convertData($wp);
            $hp = phpCollab\Util::convertData($hp);
            $mp = phpCollab\Util::convertData($mp);
            $fax = phpCollab\Util::convertData($fax);
            $last_page = phpCollab\Util::convertData($last_page);
            $tmpquery = "UPDATE " . $tableCollab["members"] . " SET login='$un',name='$fn',title='$tit',organization='$clod',email_work='$em',phone_work='$wp',phone_home='$hp',mobile='$mp',fax='$fax',last_page='$last_page',comments='$c' WHERE id = '$id'";
            phpCollab\Util::connectSql("$tmpquery");

//test if new password set
            if ($pw != "") {

//test if 2 passwords match
                if ($pw != $pwa || $pwa == "") {
                    $error = $strings["new_password_error"];
                } else {
                    $pw = phpCollab\Util::getPassword($pw);
                    $tmpquery = "UPDATE " . $tableCollab["members"] . " SET password='$pw' WHERE id = '$id'";
                    phpCollab\Util::connectSql("$tmpquery");
                    phpCollab\Util::headerFunction("../clients/viewclient.php?msg=update&id=$clod");
                    exit;
                }
            } else {
//if mantis bug tracker enabled
                if ($enableMantis == "true") {
                    // Call mantis function for user changes..!!!
                    $f_access_level = $client_user_level; // reporter
                    include '../mantis/user_update.php';
                }
                phpCollab\Util::headerFunction("../clients/viewclient.php?msg=update&id=$clod");
                exit;
            }
        }
    }
}

//set values in form
$un = $userDetail->mem_login[0];
$fn = $userDetail->mem_name[0];
$clod = $userDetail->mem_organization[0];


$tit = $userDetail->mem_title[0];
$em = $userDetail->mem_email_work[0];
$wp = $userDetail->mem_phone_work[0];
$hp = $userDetail->mem_phone_home[0];
$mp = $userDetail->mem_mobile[0];
$fax = $userDetail->mem_fax[0];
$last_page = $userDetail->mem_last_page[0];
$c = $userDetail->mem_comments[0];

$bodyCommand = "onLoad=\"document.client_user_editForm.un.focus();\"";
include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["organizations"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=$organization", $detailClient->org_name[0], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/viewclientuser.php?organization=$organization&id=" . $userDetail->mem_id[0], $userDetail->mem_login[0], in));
$blockPage->itemBreadcrumbs($strings["edit_client_user"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "client_user_edit";
$block1->openForm("../users/updateclientuser.php?action=update&organization=$organization");

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["edit_client_user"] . " : $un");

$block1->openContent();
$block1->contentTitle($strings["edit_user_details"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["user_name"] . " :</td><td><input type='hidden' name='id' value='$id'><input size='24' style='width: 250px;' maxlength='16' type='text' name='un' value='$un'><input type='hidden' name='unOld' value='$un'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["full_name"] . " :</td><td><input size='24' style='width: 250px;' maxlength='64' type='text' name='fn' value='$fn'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["title"] . " :</td><td><input size='24' style='width: 250px;' maxlength='64' type='text' name='tit' value='$tit'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["organization"] . " :</td><td><select name='clod'>";

$tmpquery = "WHERE org.id != '1' ORDER BY org.name";
$selectClient = new phpCollab\Request();
$selectClient->openOrganizations($tmpquery);
$comptSelectClient = count($selectClient->org_id);
for ($i = 0; $i < $comptSelectClient; $i++) {
    if ($userDetail->mem_organization[0] == $selectClient->org_id[$i]) {
        echo "<option value='" . $selectClient->org_id[$i] . "' selected>" . $selectClient->org_name[$i] . "</option>";
    } else {
        echo "<option value='" . $selectClient->org_id[$i] . "'>" . $selectClient->org_name[$i] . "</option>";
    }
}

echo "</select></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["email"] . " :</td><td><input size='24' style='width: 250px;' maxlength='128' type='text' name='em' value='$em'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["work_phone"] . " :</td><td><input size='14' style='width: 150px;' maxlength='32' type='text' name='wp' value='$wp'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["home_phone"] . " :</td><td><input size='14' style='width: 150px;' maxlength='32' type='text' name='hp' value='$hp'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["mobile_phone"] . " :</td><td><input size='14' style='width: 150px;' maxlength='32' type='text' name='mp' value='$mp'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["fax"] . " :</td><td class='infoValueField' width='634'><input size='14' style='width: 150px;' maxlength='32' type='text' name='fax' value='$fax'></td></tr>";
if ($lastvisitedpage == true) {
    echo "<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["last_page"] . " :</td>
			<td class='infoValueField' width='634'><input size='14' style='width: 150px;' maxlength='32' type='text' name='last_page' value='$last_page'></td>
		  </tr>";
}
echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["comments"] . " :</td><td><textarea style='width: 400px; height: 50px;' name='c' cols='35' rows='2'>$c</textarea></td></tr>";

$block1->contentTitle($strings["change_password_user"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["password"] . " :</td><td><input size='24' style='width: 250px;' maxlength='16' type='password' name='pw' value=''></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["confirm_password"] . " :</td><td><input size='24' style='width: 250px;' maxlength='16' type='password' name='pwa' value=''></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><input type='submit' name='Save' value='" . $strings["save"] . "'></td></tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
?>