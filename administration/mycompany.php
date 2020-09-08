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


use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

if ($session->get('profile') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$org = $container->getOrganizationsManager();

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "update") {

                $extension = $request->request->get("extension");
                $extensionOld = $request->request->get("extensionOld");
                $orgName = $request->request->get("org_name");
                $orgAddress = $request->request->get("org_address");
                $orgPhone = $request->request->get("org_phone");
                $url = $request->request->get("website");
                $email = $request->request->get("email");
                $comments = $request->request->get("comments");
                $logoDel = $request->request->get("logoDel");

                if ($logoDel == "on") {
                    $org->setLogoExtensionByOrgId(1, '');

                    try {
                        unlink(APP_ROOT . "/logos_clients/1.$extensionOld");
                    } catch (Exception $e) {
                        $logger->error('Admin (company)', ['Exception message', $e->getMessage()]);
                        $error = $strings["action_not_allowed"];
                    }
                }

                $extension = strtolower(substr(strrchr($_FILES['logo']['name'], "."), 1));

                try {
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], "../logos_clients/1.$extension")) {
                        $org->setLogoExtensionByOrgId(1, $extension);
                    }
                } catch (Exception $e) {
                    $logger->error('Admin (company)', ['Exception message', $e->getMessage()]);
                    $error = $strings["action_not_allowed"];
                }

                $dbParams = [];
                $dbParams['name'] = phpCollab\Util::convertData($orgName);
                $dbParams['address1'] = phpCollab\Util::convertData($orgAddress);
                $dbParams['phone'] = $orgPhone;
                $dbParams['url'] = $url;
                $dbParams['email'] = $email;
                $dbParams['comments'] = phpCollab\Util::convertData($comments);

                $org->updateOrganizationInformation($dbParams);

                phpCollab\Util::headerFunction("../administration/mycompany.php?msg=update");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->critical('CSRF Token Error', [
            'Admin: Edit My Company',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}
$company = $org->getOrganizationById(1);

$setTitle .= " : Company Details";

$bodyCommand = "onLoad='document.adminDForm.cn.focus();'";
include APP_ROOT . '/views/layout/header.php';


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

echo <<<HTML
	<form id="{$block1->form}Anchor" method='post' action='../administration/mycompany.php?' name='adminDForm' enctype='multipart/form-data'>
        <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
	    <input type='hidden' name='MAX_FILE_SIZE' value='100000000'>
HTML;


if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["company_details"]);

$block1->openContent();

$block1->contentTitle($strings["company_info"]);
$block1->contentRow($strings["name"],
    '<input size="44" value="' . $company['org_name'] . '" style="width: 400px" name="org_name" maxlength="100" type="TEXT">');
$block1->contentRow($strings["address"],
    "<textarea rows='3' style='width: 400px; height: 50px;' name='org_address' cols='43'>{$company['org_address1']}</textarea>");
$block1->contentRow($strings["phone"],
    "<input size='32' value='{$company['org_phone']}' style='width: 250px' name='org_phone' maxlength='32' type='TEXT'>");
$block1->contentRow($strings["url"],
    "<input size='44' value='{$company['org_url']}' style='width: 400px' name='website' maxlength='2000' type='TEXT'>");
$block1->contentRow($strings["email"],
    "<input size='44' value='{$company['org_email']}' style='width: 400px' name='email' maxlength='2000' type='TEXT'>");
$block1->contentRow($strings["comments"],
    "<textarea rows='3' style='width: 400px; height: 50px;' name='comments' cols='43'>{$company['org_comments']}</textarea>");
$block1->contentRow($strings["logo"] . $blockPage->printHelp("mycompany_logo"),
    '<input size="44" style="width: 400px" name="logo" type="file">');

if (file_exists(APP_ROOT . "/logos_clients/1." . $company['org_extension_logo'])) {
    $logo = "../logos_clients/1." . $company['org_extension_logo'];
    $block1->contentRow(
        "",
        '<div class="logoContainer"><img src="' . $logo . '" alt="' . $company['org_name'] . '"></div>' .
        '<input name="extensionOld" type="hidden" value="' . $company['org_extension_logo'] . '">' .
        '<input name="logoDel" type="checkbox" value="on"> ' . $strings["delete"]
    );
}

$block1->contentRow("", '<button type="submit" name="action" value="update">' . $strings["save"] . '</button>');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
