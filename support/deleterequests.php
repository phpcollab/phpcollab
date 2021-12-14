<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $support = $container->getSupportLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$id = $request->query->get('id');
$action = $request->query->get('action');

$sendto = $request->query->get('sendto');
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

                phpCollab\Util::headerFunction("../support/support.php?msg=delete&action=$sendto&project=$project");
            }

            if ($action == "deletePost") {
                $id = str_replace("**", ",", $id);
                $pieces = explode(",", $id);
                $num = count($pieces);
                $support->deleteSupportPostsById($id);

                phpCollab\Util::headerFunction("../support/viewrequest.php?msg=delete&id=$sendto");
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
    $id = str_replace("**", ",", $id);
    $listRequest = $support->getSupportRequestByIdIn($id);
} elseif ($action == "deletePost") {
    if (strpos($id, "**") !== false) {
        $id = str_replace("**", ",", $id);
        $listPosts = $support->getSupportPostsByRequestIdIn($id);
        $listRequest = $support->getSupportRequestById($listPosts[0]["sp_request_id"]);
    } else {
        $listPost = $support->getSupportPostById($id);
        $listRequest = $support->getSupportRequestById($listPost["sp_request_id"]);
    }
}

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    if (isset($listRequest) && $listRequest != '') {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $listRequest["sr_project"],
            $listRequest["sr_pro_name"], "in"));
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $listRequest["sr_project"],
            $strings["support_requests"], "in"));
    }
    if ($action == "deleteRequest") {
        $blockPage->itemBreadcrumbs($strings["delete_request"]);
    } elseif ($action == "deletePost") {
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
        $block1->openForm("deleterequests.php?action=deleteRequest&id=$id&sendto=$sendto&project=" . $listRequest["sr_project"],
            null, $csrfHandler);
    } elseif ($action == "deletePost") {
        $block1->openForm("deleterequests.php?action=deletePost&id=$id&sendto=" . $listRequest["sr_id"], null,
            $csrfHandler);
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
    if (isset($listRequest) && $listRequest != '') {
        foreach ($listRequest as $request) {
            echo '<tr class="odd"><td class="leftvalue">&nbsp;</td><td>' . $request["sr_id"] . ' - ' . $request["sr_subject"] . '</td></tr>';
        }
    }
    echo <<< TR
    <tr class="odd">
      <td class="leftvalue">&nbsp;</td>
      <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
    </tr>
TR;
} elseif ($action == "deletePost") {
    if (isset($listPosts) && $listPosts != '') {
        foreach ($listPost as $post) {
            echo '<tr class="odd"><td class="leftvalue">&nbsp;</td><td>' . $post["sp_id"] . ' - ' . $post["sp_message"] . '</td></tr>';
        }
    } elseif (isset($listPost) && $listPost != '') {
        echo '<tr class="odd"><td class="leftvalue">&nbsp;</td><td>' . $listPost["sp_id"] . ' - ' . $listPost["sp_message"] . '</td></tr>';
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
