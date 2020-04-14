<?php

use phpCollab\Members\Members;
use phpCollab\Organizations\Organizations;

$checkSession = "true";
include_once '../includes/library.php';

$members = new Members();

$id = $request->query->get('id');

//case update client organization
if (!empty($id)) {
    $organizations = new Organizations();
    $clientDetail = $organizations->checkIfClientExistsById($id);

    if (empty($clientDetail)) {
        phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
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

    //case update client organization
    if ($request->query->get('action') == "update") {

        if ($_POST["logoDel"] == "on") {

            $result = $organizations->setLogoExtensionByOrgId($id, '');

            if ($result == 0) {
                @unlink("../logos_clients/" . $id . "." . $_POST["extensionOld"]);
            }
        }

        // Check to see if file was actually uploaded or not
        if (!empty($_FILES["upload"]["tmp_name"]) && !empty($_FILES["upload"]["size"])) {
            // Check to see if the attached file is an image
            // Poor way of doing this, but its a band-aid for now
            if (getimagesize($_FILES['upload']['tmp_name'])) {
                $extension = strtolower(substr(strrchr($_FILES['upload']['name'], "."), 1));

                $target_file = "../logos_clients/" . $id . '.' . $extension;

                if (@move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
                    chmod($target_file, 0666);

                    $organizations->setLogoExtensionByOrgId($id, $extension);
                }

            }

        }

        //replace quotes by html code in name and address
        $name = phpCollab\Util::convertData($_POST["name"]);
        $address = phpCollab\Util::convertData($_POST["address"]);
        $comments = phpCollab\Util::convertData($_POST["comments"]);
        $phone = (empty($_POST["phone"])) ? null : $_POST["phone"];
        $url = (empty($_POST["url"])) ? null : $_POST["url"];
        $email = (empty($_POST["email"])) ? null : $_POST["email"];
        $hourlyRate = (empty($_POST["hourly_rate"])) ? null : $_POST["hourly_rate"];
        $owner = (empty($_POST["owner"])) ? null : $_POST["owner"];


        $organizations->updateClient($id, $name, $address, $phone, $url, $email, $comments, $owner, $hourlyRate);

        phpCollab\Util::headerFunction("../clients/viewclient.php?id=$id&msg=update");
    }

}

//case add client organization
if (empty($id)) {
    $setTitle .= " : Add Client";

    if ($request->query->get('action') == "add") {
        if (empty($_POST["name"])) {
            $error = $strings["blank_organization_field"];
        } else {
            $organizations = new Organizations();
            if ($organizations->checkIfClientExistsByName($_POST["name"])) {
                $error = $strings["organization_already_exists"];
            } else {
                $clientName = phpCollab\Util::convertData($_POST["name"]);
                $address = phpCollab\Util::convertData($_POST["address"]);
                $comments = phpCollab\Util::convertData($_POST["comments"]);
                $phone = (empty($_POST["phone"])) ? null : $_POST["phone"];
                $url = (empty($_POST["url"])) ? null : $_POST["url"];
                $email = (empty($_POST["email"])) ? null : $_POST["email"];
                $hourlyRate = (empty($_POST["hourly_rate"])) ? null : $_POST["hourly_rate"];
                $owner = (empty($_POST["owner"])) ? null : $_POST["owner"];

                if (empty($hourly_rate)) {
                    $hourly_rate = 0.00;
                }

                $newClientId = $organizations->addClient($clientName, $address, $phone, $url, $email, $comments, $owner, $hourly_rate);

                if (
                    $newClientId
                    && $_FILES['upload']['error'] == 0
                    && is_uploaded_file($_FILES['upload']['tmp_name'])
                ) {

                    $extension = strtolower(substr(strrchr($_FILES['upload']['name'], "."), 1));

                    $target_file = "../logos_clients/" . $newClientId . '.' . $extension;

                    if (@move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
                        chmod($target_file, 0666);
                        $organizations->setLogoExtensionByOrgId($newClientId, $extension);
                    }
                }

                phpCollab\Util::headerFunction("../clients/viewclient.php?id={$newClientId}&msg=add");
            }
        }
    }
}

$bodyCommand = 'onLoad="document.ecDForm.cn.focus();"';
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], "in"));

if ($id == "") {
    $blockPage->itemBreadcrumbs($strings["add_organization"]);
}

if ($id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $clientDetail['org_id'], $clientDetail['org_name'], "in"));
    $blockPage->itemBreadcrumbs($strings["edit_organization"]);
}

$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if (empty($id)) {
    echo <<<FORM
        <a id="{$block1->form}Anchor"></a>
	<form accept-charset="UNKNOWN" method="POST" action="../clients/editclient.php?action=add&" name="ecDForm" enctype="multipart/form-data">
	    <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
FORM;
}

if (!empty($id)) {
    echo <<<FORM
    <a id="{$block1->form}Anchor"></a>
	<form accept-charset="UNKNOWN" method="POST" action="../clients/editclient.php?id={$id}&action=update&" name="ecDForm" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="100000000">
FORM;
}

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if ($id == "") {
    $block1->heading($strings["add_organization"]);
}

if ($id != "") {
    $block1->heading($strings["edit_organization"] . " : " . $clientDetail['org_name']);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

if ($clientsFilter == "true") {
    $selectOwner = "<select name='owner'>";

    $clientOwner = $members->getNonManagementMembers('mem.name');

    foreach ($clientOwner as $owner) {
        if ($clientDetail['org_owner'] == $owner["mem_id"] || $idSession == $owner["mem_id"]) {
            $selectOwner .= '<option value="' . $owner["mem_id"] . '" selected>' . $owner["mem_login"] . ' / ' . $owner["mem_name"] . '</option>';
        } else {
            $selectOwner .= '<option value="' . $owner["mem_id"] . '">' . $owner["mem_login"] . ' / ' . $owner["mem_name"] . '</option>';
        }
    }

    $selectOwner .= "</select>";

    $block1->contentRow($strings["owner"], $selectOwner);
} else {
    echo '<input type="hidden" name="owner" value="'. $_SESSION["idSession"].'">';
}

$block1->contentRow("* " . $strings["name"], '<input size="44" value="' . $name . '" style="width: 400px" name="name" maxlength="100" type="TEXT" />');
$block1->contentRow($strings["address"], '<textarea rows="3" style="width: 400px; height: 50px;" name="address" cols="43">' . $address . '</textarea>');
$block1->contentRow($strings["phone"], '<input size="32" value="' . $phone . '" style="width: 250px" name="phone" maxlength="32" type="TEXT" />');
$block1->contentRow($strings["url"], '<input size="44" value="' . $url . '" style="width: 400px" name="url" maxlength="2000" type="TEXT" />');
$block1->contentRow($strings["email"], '<input size="44" value="' . $email . '" style="width: 400px" name="email" maxlength="2000" type="TEXT" />');
$block1->contentRow($strings["comments"], '<textarea rows="3" style="width: 400px; height: 50px;" name="comments" cols="43">' . $comments . '</textarea>');

if ($enableInvoicing == "true") {
    $block1->contentRow($strings["hourly_rate"], '<input size="25" value="' . $hourly_rate . '" style="width: 200px" name="hourly_rate" maxlength="50" type="TEXT" />');
}

$block1->contentRow($strings["logo"], '<input size="44" style="width: 400px" name="upload" type="file">');

if ($id != "") {
    if (file_exists("../logos_clients/" . $id . "." . $clientDetail['org_extension_logo'])) {
        $block1->contentRow("", '<img alt="" src="../logos_clients/' . $id . '.' . $clientDetail['org_extension_logo'] . '" /> <input name="extensionOld" type="hidden" value="' . $clientDetail['org_extension_logo'] . '" /><input name="logoDel" type="checkbox" value="on" /> ' . $strings["delete"]);
    }
}

$block1->contentRow("", "<input type='SUBMIT' value='" . $strings["save"] . "' />");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
