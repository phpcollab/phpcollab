<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

// Check to see if an id is passed, if so then they want to edit a client, not add one, so let's redirect them
if ($request->query->get("id")) {
    phpCollab\Util::headerFunction("../clients/editclient.php?id=" . $request->query->get("id"));
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            try {
                if (empty($request->request->get('name'))) {
                    $error = $strings["blank_organization_field"];
                } else {
                    $organizations = $container->getOrganizationsManager();
                    if ($organizations->checkIfClientExistsByName($request->request->get('name'))) {
                        $error = $strings["organization_already_exists"];
                    } else {
                        $clientName = phpCollab\Util::convertData($request->request->get('name'));
                        $address = phpCollab\Util::convertData($request->request->get('address'));
                        $comments = phpCollab\Util::convertData($request->request->get('comments'));
                        $phone = (empty($request->request->get('phone'))) ? null : $request->request->get('phone');
                        $url = (empty($request->request->get('url'))) ? null : $request->request->get('url');
                        $email = (empty($request->request->get('email'))) ? null : $request->request->get('email');
                        $hourlyRate = (empty($request->request->get('hourly_rate'))) ? 0.00 : $request->request->get('hourly_rate');
                        $owner = (empty($request->request->get('owner'))) ? null : $request->request->get('owner');

                        $newClientId = $organizations->addClient($clientName, $address, $phone, $url, $email,
                            $comments, $owner, $hourlyRate);

                        /**
                         * check if a file was sent, if so, handle it
                         */
                        if ($newClientId && $request->files->get('upload')) {

                            $fileUpload = $container->getFileUploadLoader($request->files->get('upload'));

                            $fileUpload->checkFileUpload();

                            $fileUpload->move(APP_ROOT . '/logos_clients/', $newClientId);

                            $organizations->setLogoExtensionByOrgId($newClientId, $fileUpload->getFileExtension());
                        }
                        phpCollab\Util::headerFunction("../clients/viewclient.php?id={$newClientId}&msg=add");
                    }
                }
            } catch (Exception $exception) {
                $logger->critical('Add Client Error ' . $e->getMessage(), []);
                $msg = 'clientAddError';
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->critical('CSRF Token Error', [
            'Add Client' => '',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $error = $strings["client_error_add"];
    }
}

$setTitle .= " : Add Client";

$bodyCommand = 'onLoad="document.addForm.cn.focus();"';
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], "in"));

$blockPage->itemBreadcrumbs($strings["add_organization"]);

$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

echo <<<FORM
	<form method="POST" action="../clients/addclient.php" name="addForm" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
FORM;

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["add_organization"]);

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
        {$selectOwnerOptions}
    </select>
SELECT;

    $block1->contentRow($strings["owner"], $selectOwner);
} else {
    echo '<input type="hidden" name="owner" value="' . $session->get("id") . '">';
}

$block1->contentRow("* " . $strings["name"],
    '<input size="44" value="' . $name . '" style="width: 400px" name="name" maxlength="100" type="TEXT" />');
$block1->contentRow($strings["address"],
    '<textarea rows="3" style="width: 400px; height: 50px;" name="address" cols="43">' . $address . '</textarea>');
$block1->contentRow($strings["phone"],
    '<input size="32" value="' . $phone . '" style="width: 250px" name="phone" maxlength="32" type="TEXT" />');
$block1->contentRow($strings["url"],
    '<input size="44" value="' . $url . '" style="width: 400px" name="url" maxlength="2000" type="TEXT" />');
$block1->contentRow($strings["email"],
    '<input size="44" value="' . $email . '" style="width: 400px" name="email" maxlength="2000" type="TEXT" />');
$block1->contentRow($strings["comments"],
    '<textarea rows="3" style="width: 400px; height: 50px;" name="comments" cols="43">' . $comments . '</textarea>');

if ($enableInvoicing == "true") {
    $block1->contentRow($strings["hourly_rate"],
        '<input size="25" value="' . $hourly_rate . '" style="width: 200px" name="hourly_rate" maxlength="50" type="text" />');
}

$block1->contentRow($strings["logo"], '<input name="upload" type="file">');
$block1->contentRow("", '<button type="submit" name="action" value="add">' . $strings["save"] . '</button>');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
