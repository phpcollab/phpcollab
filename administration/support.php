<?php

$checkSession = "true";
require_once '../includes/library.php';

$support = $container->getSupportLoader();

if ($session->get('profile') != "0" || $enableHelpSupport != "true") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], 'in'));
$blockPage->itemBreadcrumbs($strings["support_management"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

if ($enableHelpSupport == "true") {
    $newRequestCount = count($support->getSupportRequestByStatus(0));
    $openRequestCount = count($support->getSupportRequestByStatus(1));
    $completeRequestCount = count($support->getSupportRequestByStatus(2));

    $block1 = new phpCollab\Block();
    $block1->form = "help";

    if (isset($error) && $error != "") {
        $block1->headingError($strings["errors"]);
        $block1->contentError($error);
    }
    $block1->heading($strings["support_requests"]);

    $block1->openContent();
    $block1->contentTitle($strings["information"]);
    $block1->contentRow($strings["new_requests"],
        "$newRequestCount - " . $blockPage->buildLink("../support/support.php?action=new",
            $strings["manage_new_requests"], 'in') . "<br/><br/>");
    $block1->contentRow($strings["open_requests"],
        "$openRequestCount - " . $blockPage->buildLink("../support/support.php?action=open",
            $strings["manage_open_requests"], 'in') . "<br/><br/>");
    $block1->contentRow($strings["closed_requests"],
        "$completeRequestCount - " . $blockPage->buildLink("../support/support.php?action=complete",
            $strings["manage_closed_requests"], 'in') . "<br/><br/>");
    $block1->closeContent();
}

include APP_ROOT . '/views/layout/footer.php';
