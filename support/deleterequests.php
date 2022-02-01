<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $support = $container->getSupportLoader();

    $id = $request->query->get('id');
    $action = $request->query->get('action');

    $sendTo = $request->query->get('sendto');
    $project = $request->query->get('project');

    $strings = $GLOBALS["strings"];

    if ($enableHelpSupport != "true") {
        phpCollab\Util::headerFunction('../general/permissiondenied.php');
    }

    if ($supportType == "admin") {
        if ($session->get("profile") != "0") {
            phpCollab\Util::headerFunction('../general/permissiondenied.php');
        }
    }

    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($action == "deleteRequest") {
                    $id = str_replace("**", ",", $id);
                    $pieces = explode(",", $id);
                    $num = count($pieces);

                    $support->deleteSupportRequests($id);
                    $support->deleteSupportPostsByRequestId($id);

                    phpCollab\Util::headerFunction("../support/support.php?msg=delete&action=$sendTo&project=$project");
                }

                if ($action == "deletePost") {
                    $id = str_replace("**", ",", $id);
                    $pieces = explode(",", $id);
                    $num = count($pieces);
                    $support->deleteSupportPostsById($id);

                    phpCollab\Util::headerFunction("../support/viewrequest.php?msg=delete&id=$sendTo");
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Support: Delete request',
                '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
                '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }
    }


    if ($action == "deleteRequest") {
        $ids = explode(',', str_replace("**", ",", $id));
        // If there is more than one ID, then loop over and get the result for each one and stuff it into an array
        foreach ($ids as $item) {
            $listRequest[] = $support->getSupportRequestById($item);
        }
    }

    if ($action == "deletePost") {
        if (strpos($id, "**") !== false) {
            $id = str_replace("**", ",", $id);
            // Get all associated responses
            $listPosts = $support->getSupportPostsByRequestIdIn($id);
            // Get Request
            $listRequest = $support->getSupportRequestById($listPosts[0]["sp_request_id"]);
        } else {
            // Get all associated responses
            $listPost = $support->getSupportPostById($id);
            // Get Request
            $listRequest = $support->getSupportRequestById($listPost["sp_request_id"]);
        }
    }

    // Check to see if the request/post was loaded, if not, then go back to the list
    if (
        (empty($listPosts) || empty($listPost)) && empty($listRequest)
    ) {
        phpCollab\Util::headerFunction($_SERVER['HTTP_REFERER']);
    }

    include APP_ROOT . '/views/layout/header.php';

    $blockPage = new phpCollab\Block();
    $blockPage->openBreadcrumbs();
    if ($supportType == "team") {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));

        if (isset($listRequest) && !empty($listRequest)) {
            $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $listRequest[0]["sr_project"],
                $listRequest[0]["sr_pro_name"], "in"));
            $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $listRequest[0]["sr_project"],
                $strings["support_requests"], "in"));
        }

        if ($action == "deleteRequest") {
            $blockPage->itemBreadcrumbs($strings["delete_request"]);
        }

        if ($action == "deletePost") {
            $blockPage->itemBreadcrumbs($strings["delete_support_post"]);
        }
    } elseif ($supportType == "admin") {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"],
            "in"));
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?", $strings["support_management"],
            "in"));
        if (isset($listRequest) && $listRequest != '') {
            $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $listRequest["sr_project"],
                $strings["support_requests"], "in"));
        }
        if ($action == "deleteRequest") {
            $blockPage->itemBreadcrumbs($strings["delete_request"]);
        } elseif ($action == "deletePost") {
            $blockPage->itemBreadcrumbs($strings["delete_support_post"]);
        }
    }
    $blockPage->closeBreadcrumbs();


    if ($msg != "") {
        include '../includes/messages.php';
        $blockPage->messageBox($GLOBALS["msgLabel"]);
    }

    $block1 = new phpCollab\Block();

    $block1->form = "saP";

    if (isset($listRequest) && $listRequest != '') {

        if ($action == "deleteRequest") {
            $block1->openForm("deleterequests.php?action=deleteRequest&id=$id&sendto=$sendTo&project=" . $project,
                null, $csrfHandler);
        } elseif ($action == "deletePost") {
            $block1->openForm("deleterequests.php?project=$project&action=deletePost&id=$id&sendto=" . $listRequest["sr_id"],
                null, $csrfHandler);
        }
    }

    if ($action == "deleteRequest") {
        $block1->heading($strings["delete_request"]);
    } elseif ($action == "deletePost") {
        $block1->heading($strings["delete_support_post"]);
    }

    $block1->openContent();
    $block1->contentTitle($strings["delete_following"]);

    if ($action == "deleteRequest") {
        if (isset($listRequest) && !empty($listRequest)) {
            foreach ($listRequest as $index => $request) {
                $rowClass = ($index % 2) ? 'odd ': 'even' ;
                echo <<<HTML
                <tr class="$rowClass">
                    <td class="leftvalue formLabel">{$strings["support_request"]} : </td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="$rowClass">
                    <td class="leftvalue">{$strings["subject"]} : </td>
                    <td>{$escaper->escapeHtml($request["sr_subject"])} ( {$request["sr_id"]} )</td>
                </tr>
                <tr class="$rowClass">
                    <td class="leftvalue">{$strings["message"]} : </td>
                    <td>{$escaper->escapeHtml($request["sr_message"])}</td>
                </tr>
                <tr class="$rowClass">
                    <td colspan="2" style="padding: 0.75rem"></td>
                </tr>

HTML;

            }

            echo <<< TR
            <tr class="">
              <td class="leftvalue">&nbsp;</td>
              <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
            </tr>
TR;
        }
    }

    if ($action == "deletePost") {
        if (isset($listPosts) && $listPosts != '') {
            foreach ($listPost as $post) {
                echo '<tr class="odd"><td class="leftvalue">&nbsp;</td><td>' . $post["sp_id"] . ' - ' . $escaper->escapeHtml($post["sp_message"]) . '</td></tr>';
            }
        }

        if (isset($listPost) && $listPost != '') {
            echo '<tr class="odd"><td class="leftvalue">&nbsp;</td><td>' . $listPost["sp_id"] . ' - ' . $escaper->escapeHtml($listPost["sp_message"]) . '</td></tr>';
        }

        echo <<< TR
        <tr class="odd">
          <td class="leftvalue">&nbsp;</td>
          <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
        </tr>
TR;

    }

    $block1->closeContent();
    $block1->closeForm();

    include APP_ROOT . '/views/layout/footer.php';
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}
