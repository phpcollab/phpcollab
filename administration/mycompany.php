<?php
/*
** Application name: phpCollab
** Last Edit page: 06/09/2004
** Path by root: ../administration/mycompany.php
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
** FILE: mycompany.php
**
** DESC: Screen: add/modify information on the main company
**
** HISTORY:
** 	2003-10-23	-	added new document info
**  2004-09-06  -   xhtml correction
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

if ($profilSession != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$org = new \phpCollab\Organizations\Organizations();

$action = phpCollab\Util::returnGlobal('action', 'GET');

if ($action == "update") {
    $extension = phpCollab\Util::returnGlobal('extension', 'POST');
    $extensionOld = phpCollab\Util::returnGlobal('extensionOld', 'POST');
    $cn = phpCollab\Util::returnGlobal('cn', 'POST');
    $add = phpCollab\Util::returnGlobal('add', 'POST');
    $wp = phpCollab\Util::returnGlobal('wp', 'POST');
    $url = phpCollab\Util::returnGlobal('url', 'POST');
    $email = phpCollab\Util::returnGlobal('email', 'POST');
    $c = phpCollab\Util::returnGlobal('c', 'POST');
    $logoDel = phpCollab\Util::returnGlobal('logoDel', 'POST');

    if ($logoDel == "on") {
        $org->setLogoExtensionByOrgId(1, '');

        try {
            unlink(APP_ROOT . "/logos_clients/1.$extensionOld");
        }
        catch(Exception $e) {
            echo 'Error deleting file. Message: ' .$e->getMessage();
        }
    }

    $extension = strtolower(substr(strrchr($_FILES['upload']['name'], "."), 1));

    try {
        if (move_uploaded_file($_FILES['upload']['tmp_name'], "../logos_clients/1.$extension")) {
            $org->setLogoExtensionByOrgId(1, $extension);
        }
    }
    catch(Exception $e) {
        echo 'Error moving file. Message: ' .$e->getMessage();
    }

    $dbParams = [];
    $dbParams['name'] = phpCollab\Util::convertData($cn);
    $dbParams['address1'] = phpCollab\Util::convertData($add);
    $dbParams['phone'] = $wp;
    $dbParams['url'] = $url;
    $dbParams['email'] = $email;
    $dbParams['comments'] = phpCollab\Util::convertData($c);

    $org->updateOrganizationInformation($dbParams);

    phpCollab\Util::headerFunction("../administration/mycompany.php");
}

$company = $org->getOrganizationById(1);

$setTitle .= " : Company Details";

$bodyCommand = "onLoad='document.adminDForm.cn.focus();'";
include APP_ROOT . '/themes/' . THEME . '/header.php';


$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], 'in'));
$blockPage->itemBreadcrumbs($strings["company_details"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

echo "<a name='" . $block1->form . "Anchor'></a>\n
	<form accept-charset='UNKNOWN' method='POST' action='../administration/mycompany.php?action=update&' name='adminDForm' enctype='multipart/form-data'>
	<input type='hidden' name='MAX_FILE_SIZE' value='100000000'>\n";

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["company_details"]);

$block1->openContent();

$block1->contentTitle($strings["company_info"]);
$block1->contentRow($strings["name"], '<input size="44" value="'. $company['org_name'] .'" style="width: 400px" name="cn" maxlength="100" type="TEXT">');
$block1->contentRow($strings["address"], "<textarea rows='3' style='width: 400px; height: 50px;' name='add' cols='43'>{$company['org_address1']}</textarea>");
$block1->contentRow($strings["phone"], "<input size='32' value='{$company['org_phone']}' style='width: 250px' name='wp' maxlength='32' type='TEXT'>");
$block1->contentRow($strings["url"], "<input size='44' value='{$company['org_url']}' style='width: 400px' name='url' maxlength='2000' type='TEXT'>");
$block1->contentRow($strings["email"], "<input size='44' value='{$company['org_email']}' style='width: 400px' name='email' maxlength='2000' type='TEXT'>");
$block1->contentRow($strings["comments"], "<textarea rows='3' style='width: 400px; height: 50px;' name='c' cols='43'>{$company['org_comments']}</textarea>");
$block1->contentRow($strings["logo"] . $blockPage->printHelp("mycompany_logo"), '<input size="44" style="width: 400px" name="upload" type="file">');

if (file_exists(APP_ROOT . "/logos_clients/1." . $company['org_extension_logo'])) {
    $logo = "../logos_clients/1." . $company['org_extension_logo'];
    $block1->contentRow(
        "",
        '<img src="' . $logo . '" border="0" alt="' . $company['org_name'] . '">' .
         '<input name="extensionOld" type="hidden" value="' . $company['org_extension_logo'] . '">' .
         '<input name="logoDel" type="checkbox" value="on"> ' . $strings["delete"]
    );
}

$block1->contentRow("", "<input type='SUBMIT' value='" . $strings["save"] . "'>");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
