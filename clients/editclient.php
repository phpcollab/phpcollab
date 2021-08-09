<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$id = $request->query->get('id');

// If no ID is passed in, then we can not proceed.  Redirect back to the client list
if (empty($id)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

//Get client organization
$organizations = $container->getOrganizationsManager();
$clientDetail = $organizations->checkIfClientExistsById($id);

if (empty($clientDetail)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            //case update client organization
            try {
                // Perform validation
                $emailValid = filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL);
                if ($request->request->get('email') && !filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL)) {
                    $error = $strings["invalid_email"];
                }

                if (!$error) {
                    // Check to see if we need to delete the file, which we should
                    if ($request->request->get('logoDel') == "on") {

                        $result = $organizations->setLogoExtensionByOrgId($id, '');

                        if ($result == 0) {
                            unlink(APP_ROOT . "/logos_clients/" . $id . "." . $request->request->get('extensionOld'));
                        }
                    }

                    // Check to see if a file was uploaded
                    if ($request->files->get('upload')) {
                        $fileUpload = $container->getFileUploadLoader($request->files->get('upload'));

                        $fileUpload->checkFileUpload();

                        $fileUpload->move(APP_ROOT . '/logos_clients/', $id);
                        $organizations->setLogoExtensionByOrgId($id, $fileUpload->getFileExtension());
                    }

                    //replace quotes by html code in name and address
                    $name = phpCollab\Util::convertData($request->request->get('name'));
                    $address = phpCollab\Util::convertData($request->request->get('address'));
                    $comments = phpCollab\Util::convertData($request->request->get('comments'));
                    $phone = (empty($request->request->get('phone'))) ? null : $request->request->get('phone');
                    $url = (empty($request->request->get('url'))) ? null : filter_var($request->request->get('url'), FILTER_SANITIZE_SPECIAL_CHARS);
                    $email = (empty($request->request->get('email'))) ? null : $request->request->get('email');
                    $hourlyRate = (empty($request->request->get('hourly_rate'))) ? 0.0 : $request->request->get('hourly_rate');
                    $owner = (empty($request->request->get('owner'))) ? null : $request->request->get('owner');


                    $organizations->updateClient($id, $name, $address, $phone, $url, $email, $comments, $owner,
                        $hourlyRate);

                    phpCollab\Util::headerFunction("../clients/viewclient.php?id=$id&msg=update");
                }

            } catch (Exception $exception) {
                $logger->critical('Edit Client Error ' . $exception->getMessage(), []);
                $msg = 'clientEditError';
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Clients: edit client' => $request->request->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $error = $strings["client_error_edit"];
    }
}

//set value in form
$name = $clientDetail['org_name'];
$address = $clientDetail['org_address1'];
$phone = $clientDetail['org_phone'];
$url = $clientDetail['org_url'];
$email = $clientDetail['org_email'];
$comments = $clientDetail['org_comments'];
$hourly_rate = $clientDetail['org_hourly_rate'];

$setTitle .= " : Edit Client ($name)";

$bodyCommand = 'onLoad="document.editForm.name.focus();"';
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], "in"));

$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $clientDetail['org_id'],
    $clientDetail['org_name'], "in"));
$blockPage->itemBreadcrumbs($strings["edit_organization"]);

$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

echo <<<FORM
	<form method="POST" action="../clients/editclient.php?id=$id" name="editForm" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
FORM;

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["edit_organization"] . " : " . $clientDetail['org_name']);

$block1->openContent();
$block1->contentTitle($strings["details"]);

if ($clientsFilter == "true") {

    $clientOwner = $members->getNonManagementMembers('mem.name');
    $selectOwnerOptions = '';
    foreach ($clientOwner as $owner) {
        if ($clientDetail['org_owner'] == $owner["mem_id"] || $session->get("id") == $owner["mem_id"]) {
            $selectOwnerOptions .= '<option value="' . $owner["mem_id"] . '" selected>' . $owner["mem_login"] . ' / ' . $owner["mem_name"] . '</option>';
        } else {
            $selectOwnerOptions .= '<option value="' . $owner["mem_id"] . '">' . $owner["mem_login"] . ' / ' . $owner["mem_name"] . '</option>';
        }
    }
    $selectOwner = <<<SELECT
    <select name="owner">
        $selectOwnerOptions
    </select>
SELECT;

    $block1->contentRow($strings["owner"], $selectOwner);
} else {
    echo '<input type="hidden" name="owner" value="' . $session->get("id") . '">';
}

$block1->contentRow("* " . $strings["name"],
    '<input size="44" value="' . $name . '" style="width: 400px" name="name" maxlength="100" type="text" />');
$block1->contentRow($strings["address"],
    '<textarea rows="3" style="width: 400px; height: 50px;" name="address" cols="43">' . $address . '</textarea>');
$block1->contentRow($strings["phone"],
    '<input size="32" value="' . $phone . '" style="width: 250px" name="phone" maxlength="32" type="phone" />');
$block1->contentRow($strings["url"],
    '<input size="44" value="' . $url . '" style="width: 400px" name="url" maxlength="2000" type="url" />');
$block1->contentRow($strings["email"],
    '<input size="44" value="' . $email . '" style="width: 400px" name="email" maxlength="2000" type="email" />');
$block1->contentRow($strings["comments"],
    '<textarea rows="3" style="width: 400px; height: 50px;" name="comments" cols="43">' . $comments . '</textarea>');

if ($enableInvoicing == "true") {
    $block1->contentRow($strings["hourly_rate"],
        '<input size="25" value="' . $hourly_rate . '" style="width: 200px" name="hourly_rate" maxlength="50" type="number" />');
}

$block1->contentRow($strings["logo"], '<input size="44" style="width: 400px" name="upload" type="file">');

if (file_exists("../logos_clients/" . $id . "." . $clientDetail['org_extension_logo'])) {
    $block1->contentRow("",
        '<div class="logoContainer"><img alt="" src="../logos_clients/' . $id . '.' . $clientDetail['org_extension_logo'] . '" /></div> <input name="extensionOld" type="hidden" value="' . $clientDetail['org_extension_logo'] . '" /><input name="logoDel" type="checkbox" value="on" /> ' . $strings["delete"]);
}

$block1->contentRow("",
    '<button type="submit" name="action" value="update">' . $strings["save"] . '</button> <a href="./viewclient.php?id=' . $id . '" style="margin-left: 1rem;">Cancel</a>');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
