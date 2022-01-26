<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../users/emailusers.php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $organizations = $container->getOrganizationsManager();

    /*
    //anyone can send a message
    if ($session->get("profile") != "0") {
        header("Location:../general/permissiondenied.php?".session_name()."=".session_id());
        exit;
    }
    */

    $id = str_replace("**",",", $request->query->get("id"));

    // If no ID passed, then return with error
    if (empty($id)) {
        header("Location:../users/listusers.php?msg=blankUser");
    }

    // Let's get the user(s) and see if they have email addresses, if they don't then we want to display and error and/or redirect back.
    $listMembers = $members->getMembersByIdIn($id, 'mem.name');

    // If no member found, then return with error
    if (empty($listMembers)) {
        header("Location:../users/listusers.php?msg=blankUser");
    }

    $excludedList = [];

    // Check to see if any of the members DO NOT have an email address, if they do then remove from the list.
    foreach ($listMembers as $memberKey => $listMember) {
        if (empty($listMember["mem_email_work"])) {
            // Add member to the "excluded" list
            $excludedList[] = $listMember;

            // Remove $memberKey from parent array
            unset($listMembers[$memberKey]);
        }
    }

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
                '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
                '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
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


    if ($session->getFlashBag()->has('message')) {
        $blockPage->messageBox( $session->getFlashBag()->get('message')[0] );
    } else if ($msg != "") {
        include '../includes/messages.php';
        $blockPage->messageBox($msgLabel);
    }

    $pageBlock = new phpCollab\Block();

    $pageBlock->form = "user_email";
    $pageBlock->openForm("../users/emailusers.php?action=email", null, $csrfHandler);

    if ($session->getFlashBag()->has('errors')) {
        $pageBlock->headingError($strings["errors"]);
        foreach ($session->getFlashBag()->get('errors', []) as $error) {
            $pageBlock->contentError($error);
        }
    } else if (!empty($error)) {
        $pageBlock->headingError($strings["errors"]);
        $pageBlock->contentError($error);
    }

    $pageBlock->heading($strings["email_users"]);

    $pageBlock->openContent();


    if (empty($listMembers) && empty($excludedList)) {
            die('No user found');
    }

    if (!empty($excludedList)) {
        $pageBlock->contentTitle($strings["will_not_email"]);
        foreach ($excludedList as $excluded) {
            $pageBlock->contentRow("",
                $excluded["mem_name"] . " (" . $excluded["mem_name"] . ") <span style='margin-left: 1rem'>Reason: " . $strings["no_email"] . "</span>");
        }
    }

    $pageBlock->contentTitle($strings["email_following"]);

    foreach ($listMembers as $listMember) {
        $pageBlock->contentRow("", $listMember["mem_login"] . "&nbsp;(" . $listMember["mem_name"] . ")");
    }

    $pageBlock->contentTitle($strings["email"]);
    $pageBlock->contentRow($strings["subject"],
        '<input size="44" style="width: 400px" name="subject" maxlength="100" type="text" required="required">');
    $pageBlock->contentRow($strings["message"],
        '<textarea rows="10" style="width: 400px; height: 160px;" name="message" cols="47" required="required"></textarea>');
    $pageBlock->contentRow("",
        "<input type='submit' name='submit' value='" . $strings["send"] . "'> <input type='button' name='cancel' value='" . $strings["cancel"] . "' onClick='history.back();'><input type='hidden' value='$id' name='id'>");


    $pageBlock->closeContent();
    $pageBlock->closeForm();

    include APP_ROOT . '/views/layout/footer.php';
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}
