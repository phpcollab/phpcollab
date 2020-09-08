<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Util;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

$id = $request->query->get('id');
$action = $request->query->get('action');

if ($enableHelpSupport != "true") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($supportType == "admin") {
    if ($session->get("profile") != "0") {
        phpCollab\Util::headerFunction('../general/permissiondenied.php');
    }
}

$support = $container->getSupportLoader();

$requestDetail = $support->getSupportRequestById($id);

if ($request->isMethod('post')) {

    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "edit") {

                try {
                    $status = $request->request->get('status');
                    $dateClose = ($request->request->get('status') == 2) ? $dateheure : null;

                    $support->updateSupportPostStatus($id, $status, $dateClose);

                    $postDetails = $support->getSupportPostById($id);
                    if ($notifications == "true") {
                        if ($requestDetail["sr_status"] != $request->request->get('status')) {
                            $num = $id;
                            $support->sendPostChangedNotification($postDetails);
                        }
                    }

                    phpCollab\Util::headerFunction("../support/viewrequest.php?id=$id");
                } catch (Exception $e) {
                    $logger->error('Support (edit post)', ['Exception message', $e->getMessage()]);
                    $error = $strings["action_not_allowed"];
                }
            }

            if ($request->request->get('action') == "add") {
                try {
                    $newPost = $support->addSupportPost($id, Util::convertData($request->request->get('message')),
                        $dateheure, $session->get("id"), $requestDetail["sr_project"]);

                    if (!empty($newPost) && $notifications == "true") {
                        $support->sendPostChangedNotification($newPost);
                    }
                } catch (Exception $e) {
                    $logger->error('Support (add post)', ['Exception message', $e->getMessage()]);
                    $error = $strings["action_not_allowed"];
                }

                phpCollab\Util::headerFunction("../support/viewrequest.php?id=$id");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->critical('CSRF Token Error', [
            'Support: Add post',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();

if ($supportType == "team") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $requestDetail["sr_project"],
        $requestDetail["sr_pro_name"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $requestDetail["sr_project"],
        $strings["support_requests"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/viewrequest.php?id=" . $requestDetail["sr_id"],
        $requestDetail["sr_subject"], "in"));
    if ($action == "status") {
        $blockPage->itemBreadcrumbs($strings["edit_status"]);
    } else {
        $blockPage->itemBreadcrumbs($strings["add_support_response"]);
    }
} elseif ($supportType == "admin") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"],
        "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?", $strings["support_management"],
        "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $requestDetail["sr_project"],
        $strings["support_requests"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/viewrequest.php?id=" . $requestDetail["sr_id"],
        $requestDetail["sr_subject"], "in"));
    if ($action == "status") {
        $blockPage->itemBreadcrumbs($strings["edit_status"]);
    } else {
        $blockPage->itemBreadcrumbs($strings["add_support_response"]);
    }
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}


$block2 = new phpCollab\Block();

$block2->form = "sr";
if ($action == "status") {
    $block2->openForm("../support/addpost.php?id=$id&#" . $block2->form . "Anchor", null, $csrfHandler);
    echo <<<FORM
    <input type="hidden" name="action" value="edit">
FORM;
} else {
    $block2->openForm("../support/addpost.php?id=$id&#" . $block2->form . "Anchor", null, $csrfHandler);
    echo <<<FORM
    <input type="hidden" name="action" value="add">
FORM;
}


if (!empty($error)) {
    $block2->headingError($strings["errors"]);
    $block2->contentError($error);
}

$block2->heading($strings["add_support_respose"]);

$block2->openContent();
$block2->contentTitle($strings["details"]);
if ($action == "status") {
    echo <<<TR
    <tr class="odd">
        <td class="leftvalue">{$strings["status"]} :</td>
        <td><select name="status">
TR;

    $comptSta = count($requestStatus);
    for ($i = 0; $i < $comptSta; $i++) {
        if ($requestDetail["sr_status"] == $i) {
            echo "<option value=\"$i\" selected>$requestStatus[$i]</option>";
        } else {
            echo "<option value=\"$i\">$requestStatus[$i]</option>";
        }
    }
    echo "</select></td></tr>";
} else {
    echo <<<HTML
        <tr class="odd">
            <td class="leftvalue">{$strings["message"]}</td>
            <td><textarea rows="3" style="width: 400px; height: 200px;" name="message" cols="43">{$request->request->get('message')}</textarea></td>
        </tr>
HTML;
}
echo <<<TR
        <tr class="odd">
            <td class="leftvalue">&nbsp;</td>
            <td><input type="submit" value="{$strings["submit"]}"></td>
        </tr>
TR;

$block2->closeContent();

echo <<<FORM
    <input type="hidden" name="user" value="{$session->get("id")}>
FORM;

$block2->closeForm();

include APP_ROOT . '/views/layout/footer.php';
