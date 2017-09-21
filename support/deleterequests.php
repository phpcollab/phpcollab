<?php
$checkSession = "true";
include_once '../includes/library.php';

$support = new \phpCollab\Support\Support();

$id = isset($_GET["id"]) ? $_GET["id"] : null;
$action = isset($_GET["action"]) ? $_GET["action"] : null;

$sendto = isset($_GET["sendTo"]) ? $_GET["sendTo"] : null;
$project = isset($_GET["project"]) ? $_GET["project"] : null;

$strings = $GLOBALS["strings"];

if ($enableHelpSupport != "true") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($supportType == "admin") {
    if ($profilSession != "0") {
        phpCollab\Util::headerFunction('../general/permissiondenied.php');
    }
}

if ($action == "deleteRequest") {
    $id = str_replace("**", ",", $id);
    $pieces = explode(",", $id);
    $num = count($pieces);

    $support->deleteSupportRequests($id);
    $support->deleteSupportPostsByRequestId($id);

    phpCollab\Util::headerFunction("../support/support.php?msg=delete&action={$sendto}&project={$project}");
}

if ($action == "deletePost") {
    $id = str_replace("**", ",", $id);
    $pieces = explode(",", $id);
    $num = count($pieces);
    $support->deleteSupportPostsById($id);

    phpCollab\Util::headerFunction("../support/viewrequest.php?msg=delete&id=$sendto");
}


if ($action == "deleteR") {
    $id = str_replace("**", ",", $id);
    $listRequest = $support->getSupportRequestByIdIn($id);
} elseif ($action == "deleteP") {
    $id = str_replace("**", ",", $id);
    $listPost = $support->getSupportPostsByRequestIdIn($id);

    $listRequest = $support->getSupportRequestById($listPost["sp_request_id"]);
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    if (isset($listRequest) && $listRequest != '') {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $listRequest["sr_project"], $listRequest["sr_pro_name"], "in"));
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $listRequest["sr_project"], $strings["support_requests"], "in"));
    }
    if ($action == "deleteR") {
        $blockPage->itemBreadcrumbs($strings["delete_request"]);
    } elseif ($action == "deleteP") {
        $blockPage->itemBreadcrumbs($strings["delete_support_post"]);
    }
} elseif ($supportType == "admin") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?", $strings["support_management"], "in"));
    if (isset($listRequest) && $listRequest != '') {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $listRequest["sr_project"], $strings["support_requests"], "in"));
    }
    if ($action == "deleteR") {
        $blockPage->itemBreadcrumbs($strings["delete_request"]);
    } elseif ($action == "deleteP") {
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
    if ($action == "deleteR") {
        $block1->openForm("../support/deleterequests.php?action=deleteRequest&id=$id&sendto=$sendto&project=" . $listRequest["sr_project"] . "");
    } elseif ($action == "deleteP") {
        $block1->openForm("../support/deleterequests.php?action=deletePost&id=$id&sendto=" . $listRequest["sr_id"] . "");
    }
}

if ($action == "deleteR") {
    $block1->heading($strings["delete_request"]);
} elseif ($action == "deleteP") {
    $block1->heading($strings["delete_support_post"]);
}

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

if ($action == "deleteR") {
    if (isset($listRequest) && $listRequest != '') {
        foreach ($listRequest as $request) {
            echo '<tr class="odd"><td valign="top" class="leftvalue">&nbsp;</td><td>' . $request["sr_subject"] . '</td></tr>';
        }
    }
    echo <<< TR
    <tr class="odd">
      <td valign="top" class="leftvalue">&nbsp;</td>
      <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
    </tr>
TR;
} elseif ($action == "deleteP") {
    if (isset($listPost) && $listPost != '') {
        foreach ($listPost as $post) {
            echo '<tr class="odd"><td valign="top" class="leftvalue">&nbsp;</td><td>' . $post["sp_id"] . '</td></tr>';
        }
    }
    echo <<< TR
    <tr class="odd">
      <td valign="top" class="leftvalue">&nbsp;</td>
      <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
    </tr>
TR;
}

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
