<?php
/*
** Application name: phpCollab
** Last Edit page: 06/09/2004
** Path by root: ../administration/mycompany.php
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


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

if ($session->get('profile') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$org = $container->getOrganizationsManager();

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "update") {
                if ($request->request->get("delete_logo") == "true") {
                    $filesystem = new Filesystem();

                    try {
                        // Remove the old file
                        $fileExists = $filesystem->exists(APP_ROOT . '/logos_clients/1.' . $request->request->get("extensionOld"));
                        if ($fileExists) {
                            $filesystem->remove([APP_ROOT . '/logos_clients/1.' . $request->request->get("extensionOld")]);

                            // Check to see if the file was actually removed, if so then update the DB
                            if ($filesystem->exists(APP_ROOT . '/logos_clients/1.' . $request->request->get("extensionOld"))) {
                                $session->getFlashBag()->add(
                                    'errors',
                                    $strings["file_remove_error"]
                                );
                            } else {
                                // Set the company logo to an empty string
                                $org->setLogoExtensionByOrgId(1, '');
                            }
                        }
                    } catch (Exception $e) {
                        $logger->error('Admin (company)', ['Exception message', $e->getMessage()]);
                        $session->getFlashBag()->add(
                            'errors',
                            $strings["action_not_allowed"]
                        );
                    }
                }

                // Handle uploaded file
                if ($request->files->get('logo')) {
                    $fileUpload = $container->getFileUploadLoader($request->files->get('logo'));

                    $isValid = $fileUpload->checkFileUpload();

                    $extension = $fileUpload->getFileExtension();

                    $fileUpload->move(APP_ROOT . '/logos_clients/', 1);
                }

                $org->updateOrganizationInformation(
                    $request->request->get('name'),
                    $request->request->get('address'),
                    $request->request->get('phone'),
                    $request->request->get('url'),
                    $request->request->get('email'),
                    $request->request->get('comments')
                );

                $session->getFlashBag()->add(
                    'message',
                    sprintf($strings["success_message"], $strings["modification_succeeded"])
                );

                phpCollab\Util::headerFunction("../administration/mycompany.php");
            }
        }
    } catch (TypeError $e) {
        die("TypeError: " . $e->getMessage());
    } catch (InvalidArgumentException $invalidArgumentException) {
        $session->getFlashBag()->add(
            'errors',
            sprintf($strings["error_message"], $invalidArgumentException->getMessage())
        );
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Admin: Edit My Company',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $session->getFlashBag()->add(
            'errors',
            sprintf($strings["error_message"], $strings["file_image_invalid_type"])
        );
    }
}

/**
 * Set form field values
 */
$company = $org->getOrganizationById(1);
$companyName = !empty($request->request->get('name')) ? $request->request->get('name') : $company["org_name"];
$companyAddress = !empty($request->request->get('address')) ? $request->request->get('address') : $company["org_address1"];
$companyPhone = !empty($request->request->get('phone')) ? $request->request->get('phone') : $company["org_phone"];
$companyUrl = !empty($request->request->get('url')) ? $request->request->get('url') : $company["org_url"];
$companyEmail = !empty($request->request->get('email')) ? $request->request->get('email') : $company["org_email"];
$companyComments = !empty($request->request->get('comments')) ? $request->request->get('comments') : $company["org_comments"];

$setTitle .= " : Company Details";

$bodyCommand = "onLoad='document.adminMyCompanyForm.org_name.focus();'";
include APP_ROOT . '/views/layout/header.php';


$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], 'in'));
$blockPage->itemBreadcrumbs($strings["company_details"]);
$blockPage->closeBreadcrumbs();

if ($session->getFlashBag()->has('message')) {
    $blockPage->messageBox( $session->getFlashBag()->get('message')[0] );
} else if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

echo <<<HTML
	<form id="{$block1->form}Anchor" method='post' action='/administration/mycompany.php' name="adminMyCompanyForm" enctype='multipart/form-data'>
        <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
	    <input type='hidden' name='MAX_FILE_SIZE' value='100000000'>
HTML;



if ($session->getFlashBag()->has('errors')) {
    $block1->headingError($strings["errors"]);
    foreach ($session->getFlashBag()->get('errors', []) as $error) {
        $block1->contentError($error);
    }
} else if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["company_details"]);

$block1->openContent();

$block1->contentTitle($strings["company_info"]);
$block1->contentRow($strings["name"],
    '<input size="44" value="' . $companyName . '" style="width: 400px" name="name" maxlength="100" type="text" required="required">');
$block1->contentRow($strings["address"],
    "<textarea rows='3' style='width: 400px; height: 50px;' name='address' cols='43'>$companyAddress</textarea>");
$block1->contentRow($strings["phone"],
    '<input size="32" value="' . $companyPhone .'" style="width: 250px" name="phone" maxlength="32" type="phone">');
$block1->contentRow($strings["url"],
    '<input size="44" value="' . $companyUrl . '" style="width: 400px" name="url" maxlength="2000" type="url">');
$block1->contentRow($strings["email"],
    '<input size="44" value="' . $companyEmail .'" style="width: 400px" name="email" maxlength="2000" type="email">');
$block1->contentRow($strings["comments"],
    "<textarea rows='3' style='width: 400px; height: 50px;' name='comments' cols='43'>$companyComments</textarea>");
$block1->contentRow($strings["logo"] . $blockPage->printHelp("mycompany_logo"),
    '<input size="44" style="width: 400px" name="logo" type="file">');

if (file_exists(APP_ROOT . "/logos_clients/1." . $company['org_extension_logo'])) {
    $logo = "../logos_clients/1." . $company['org_extension_logo'];
    $block1->contentRow(
        "",
        '<div class="logoContainer"><img src="' . $logo . '" alt="' . $company['org_name'] . '"></div>' .
        '<input name="extensionOld" type="hidden" value="' . $company['org_extension_logo'] . '">' .
        '<input name="delete_logo" type="checkbox" value="true"> ' . $strings["delete"]
    );
}

$block1->contentRow("", '<button type="submit" name="action" value="update">' . $strings["save"] . '</button>');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
