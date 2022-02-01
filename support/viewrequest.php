<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
require_once '../includes/library.php';

try {
    $teams = $container->getTeams();
    $support = $container->getSupportLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$requestId = $request->query->get('id');

$strings = $GLOBALS["strings"];
$status = $GLOBALS["status"];
$requestStatus = $GLOBALS["requestStatus"];

if ($enableHelpSupport != "true") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($supportType == "admin") {
    if ($session->get("profile") != "0") {
        phpCollab\Util::headerFunction('../general/permissiondenied.php');
    }
}

$requestDetail = $support->getSupportRequestById($requestId);

$listPosts = $support->getSupportPostsByRequestId($requestId);

$teamMember = "false";

$teamMember = $teams->isTeamMember($requestDetail["sr_project"], $session->get("id"));

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $requestDetail["sr_project"],
        $requestDetail["sr_pro_name"], "in"));
} elseif ($supportType == "admin") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"],
        "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?", $strings["support_management"],
        "in"));
}
$blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $requestDetail["sr_project"],
    $strings["support_requests"], "in"));
$blockPage->itemBreadcrumbs($escaper->escapeHtml($requestDetail["sr_subject"]));
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "sdt";
$block1->openForm("./viewrequest.php", null, $csrfHandler);

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["support_request"] . " : " . $escaper->escapeHtml($requestDetail["sr_subject"]));
if ($teamMember == "true" || $session->get("profile") == "0") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "edit", $strings["edit_status"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);
    $block1->closePaletteIcon();
}
$block1->openContent();
$block1->contentTitle($strings["info"]);

$priority = $GLOBALS["priority"];

$block1->contentRow($strings["project"],
    $blockPage->buildLink(
        "../projects/viewproject.php?id=" . $requestDetail["sr_project"],
        $requestDetail["sr_pro_name"],
        "in"
    ));
$block1->contentRow($strings["subject"], $escaper->escapeHtml($requestDetail["sr_subject"]));
$block1->contentRow($strings["priority"], $priority[$requestDetail["sr_priority"]]);
$block1->contentRow($strings["status"], $requestStatus[$requestDetail["sr_status"]]);
$block1->contentRow($strings["date"], $requestDetail["sr_date_open"]);
$block1->contentRow($strings["user"],
    $blockPage->buildLink(
        $requestDetail["sr_mem_email_work"],
        $requestDetail["sr_mem_name"],
        "mail"
    )
);
$block1->contentRow($strings["message"], nl2br($escaper->escapeHtml($requestDetail["sr_message"])));

$block1->contentTitle($strings["responses"]);

if ($teamMember == "true" || $session->get("profile") != "0") {
    $block1->contentRow("",
        $blockPage->buildLink("../support/addpost.php?id=" . $requestDetail["sr_id"], $strings["add_support_response"],
            "in", 'formLabel'));
}

foreach ($listPosts as $post) {
    if (!empty($post["sp_mem_email_work"])) {
        $block1->contentRow($strings["posted_by"],
            $blockPage->buildLink(
                $escaper->escapeHtml($post["sp_mem_email_work"]),
                $escaper->escapeHtml($post["sp_mem_name"]),
                "mail"
            ));
    } else {
        $block1->contentRow($strings["posted_by"], $escaper->escapeHtml($post["sp_mem_name"]));
    }

    $block1->contentRow($strings["date"], phpCollab\Util::createDate($post["sp_date"], $session->get('timezone')));

    if ($teamMember == "true" || $session->get("profile") == "0") {
        $block1->contentRow(
            $blockPage->buildLink(
                "../support/deleterequests.php?action=deletePost&id=" . $post["sp_id"],
                $strings["delete_message"],
                "in"
            ),
            nl2br($escaper->escapeHtml($post["sp_message"]))
        );
    } else {
        $block1->contentRow("", nl2br($escaper->escapeHtml($post["sp_message"])));
    }
    $block1->contentRow("", "", "true");
}

if ($status == $requestStatus[0]) {
    $status = "new";
} elseif ($status == $requestStatus[1]) {
    $status = "open";
} elseif ($status == $requestStatus[2]) {
    $status = "complete";
}

$block1->closeContent();
$block1->openPaletteScript();
$block1->paletteScript(0, "edit", "../support/addpost.php?action=status&id=" . $requestDetail["sr_id"],
    "true,true,true", $strings["edit_status"]);
$block1->paletteScript(1, "remove",
    "../support/deleterequests.php?action=deleteRequest&sendto=$status&id=" . $requestDetail["sr_id"], "true,true,true",
    $strings["delete"]);
$block1->closePaletteScript(count($requestDetail), array_column($requestDetail, 'sr_id'));
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
