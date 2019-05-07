<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../users/listusers.php

use phpCollab\Members\Members;

$checkSession = "true";
include_once '../includes/library.php';

if ($profilSession != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$members = new Members();

$setTitle .= " : List Users";

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($strings["user_management"]);
$blockPage->closeBreadcrumbs();

if (!empty($msg)) {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "ulU";
$block1->openForm("../users/listusers.php#" . $block1->form . "Anchor");

$block1->heading($strings["user_management"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0, "add", $strings["add"]);
$block1->paletteIcon(1, "remove", $strings["delete"]);
$block1->paletteIcon(2, "info", $strings["view"]);
$block1->paletteIcon(3, "edit", $strings["edit"]);
$block1->paletteIcon(4, "export", $strings["export"]);
$block1->paletteIcon(5, "email", $strings["email"]);
$block1->closePaletteIcon();

$block1->sorting("users", $sortingUser["users"], "mem.name ASC", $sortingFields = array(
    0 => "mem.name",
    1 => "mem.login",
    2 => "mem.email_work",
    3 => "mem.profil",
    4 => "log.connected")
);

if ($demoMode == "true") {
    $listMembers = $members->getNonClientMembers($block1->sortingValue);
} else {
    $listMembers = $members->getAllMembers($block1->sortingValue);

    // Find the "demo" user in the user list
    $key = array_search(2, array_column($listMembers, 'mem_id'));

    // Remove the "demo" user from results
    unset($listMembers[$key]);
}

if ($listMembers) {
    $block1->openResults();

    $block1->labels($labels = array(
        0 => $strings["full_name"], 
        1 => $strings["user_name"], 
        2 => $strings["email"], 
        3 => $strings["profile"], 
        4 => $strings["connected"]), 
        "false"
    );

    foreach ($listMembers as $member) {
        $idProfil = $member["mem_profil"];
        $block1->openRow();
        $block1->checkboxRow($member["mem_id"]);
        $block1->cellRow($blockPage->buildLink("../users/viewuser.php?id=" . $member["mem_id"], $member["mem_name"], "in"));
        $block1->cellRow($member["mem_login"]);
        $block1->cellRow($blockPage->buildLink($member["mem_email_work"], $member["mem_email_work"], "mail"));
        $block1->cellRow($profil[$idProfil]);
        if ($member["mem_log_connected"] > $dateunix - 5 * 60) {
            $block1->cellRow($strings["yes"] . " " . $strings["clients_connected"] . " (" . $member["mem_last_page"] . ")");
        } else {
            $block1->cellRow($strings["no"] . " (" . $member["mem_last_page"] . ")");
        }
        $block1->closeRow();
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
$block1->paletteScript(0, "add", "../users/edituser.php?", "true,true,true", $strings["add"]);
$block1->paletteScript(1, "remove", "../users/deleteusers.php?", "false,true,true", $strings["delete"]);
$block1->paletteScript(2, "info", "../users/viewuser.php?", "false,true,false", $strings["view"]);
$block1->paletteScript(3, "edit", "../users/edituser.php?", "false,true,false", $strings["edit"]);
$block1->paletteScript(4, "export", "../users/exportusers.php?", "true,false,true", $strings["export"]);
$block1->paletteScript(5, "email", "../users/emailusers.php?", "false,true,true", $strings["email"]);
$block1->closePaletteScript(count($listMembers), $listMembers[0]["mem_id"]);

include APP_ROOT . '/themes/' . THEME . '/footer.php';
