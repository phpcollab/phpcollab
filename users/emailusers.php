<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../users/emailusers.php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$organizations = $container->getOrganizationsManager();

/*
//anyone can send a message
if ($session->get("profile") != "0") {
    header("Location:../general/permissiondenied.php?".session_name()."=".session_id());
    exit;
}
*/

$id = $request->query->get("id");

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->query->get('action') == "email") {

                // get name and email of user sending the email
                $userPrefs = $members->getMemberById($session->get("id"));

                // get company name
                $clientDetail = $organizations->getOrganizationById(1);

                // get users to email
                $listMembers = $members->getMembersByIdIn($request->request->get("id"), 'mem.name');

                // format body and message
                $subject = stripslashes($request->request->get('subject'));
                $message = stripslashes($request->request->get('message'));
                $message = str_replace("\r\n", "\n", $message);

                foreach ($listMembers as $listMember) {

                    $signature = $userPrefs->mem_name[0] . "\n";
                    if (!empty($userPrefs["mem_title"])) {
                        $signature .= $userPrefs["mem_title"] . ", " . $clientDetail["org_name"] . "\n";
                    } else {
                        $signature .= $clientDetail["org_name"] . "\n";
                    }
                    if (!empty($userPrefs["mem_phone_work"])) {
                        $signature .= "Phone: " . $userPrefs["mem_phone_work"] . "\n";
                    }
                    if (!empty($userPrefs["mem_mobile"])) {
                        $signature .= "Mobile: " . $userPrefs["mem_mobile"] . "\n";
                    }
                    if (!empty($userPrefs["mem_fax"])) {
                        $signature .= "Fax: " . $userPrefs["mem_fax"] . "\n";
                    }

                    try {
                        $members->sendEmail($listMember["mem_email_work"], $listMember["mem_name"], $subject, $message,
                            null, null, $signature);
                    } catch (Exception $e) {
                        $logger->error($e->getMessage());
                        $msg = "genericError";
                    }


                }

                if ($session->get("profile") == "0") {
                    header("Location:../users/listusers.php?id={$clientDetail["org_id"]}&msg=email");
                } else {
                    header("Location:../general/home.php?msg=email");
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Users: Email user(s)',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

// start main page
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/listusers.php?", $strings["user_management"], "in"));
$blockPage->itemBreadcrumbs($strings["email_users"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include_once('../includes/messages.php');
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "user_email";
$block1->openForm("../users/emailusers.php?action=email", null, $csrfHandler);

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["email_users"]);

$block1->openContent();
$block1->contentTitle($strings["email_following"]);

$listMembers = $members->getMembersByIdIn($id, 'mem.name');

foreach ($listMembers as $listMember) {
    if (!empty($listMembers["mem_email_work"])) {
        $block1->contentRow("", $listMember["mem_login"] . "&nbsp;(" . $listMember["mem_name"] . ")");
    } else {
        $block1->contentRow("",
            $listMember["mem_login"] . "&nbsp;(" . $listMember["mem_name"] . ") " . $strings["no_email"]);
    }
}


$block1->contentTitle($strings["email"]);
$block1->contentRow($strings["subject"],
    '<input size="44" style="width: 400px" name="subject" maxlength="100" type="text">');
$block1->contentRow($strings["message"],
    '<textarea rows="10" style="width: 400px; height: 160px;" name="message" cols="47"></textarea>');
$block1->contentRow("",
    "<input type='submit' name='delete' value='" . $strings["email"] . "'> <input type='button' name='cancel' value='" . $strings["cancel"] . "' onClick='history.back();'><input type='hidden' value='$id' name='id'>");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
