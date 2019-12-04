<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../administration/admin.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: admin.php
**
** DESC: Screen: ADMINISTRATION
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';
$setTitle .= " : Administration";
$admin = new \phpCollab\Administration\Administration();

if ($profilSession != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($strings["admin_intro"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include APP_ROOT . '/includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->heading($strings["administration"]);

$block1->openContent();
$block1->contentTitle($strings["admin_intro"]);

$block1->contentRow("", $blockPage->buildLink("../users/listusers.php", $strings["user_management"], "in"));

if ($enableInvoicing == "true") {
    $block1->contentRow("", $blockPage->buildLink("../services/listservices.php", $strings["service_management"], "in"));
}

if ($supportType == "admin") {
    $block1->contentRow("", $blockPage->buildLink("../administration/support.php", $strings["support_management"], "in"));
}

if ($databaseType == "mysql") {
    $block1->contentRow("", $blockPage->buildLink("../administration/phpmyadmin.php", $strings["database"], "in"));
}

$block1->contentRow("", $blockPage->buildLink("../administration/systeminfo.php", $strings["system_information"], "in"));
$block1->contentRow("", $blockPage->buildLink("../administration/mycompany.php", $strings["company_details"], "in"));
$block1->contentRow("", $blockPage->buildLink("../administration/listlogs.php", $strings["logs"], "in"));
$block1->contentRow($strings["update"] . $blockPage->printHelp("admin_update"), "1. " . $blockPage->buildLink("../administration/updatesettings.php", $strings["edit_settings"], "in") . " 2. " . $blockPage->buildLink("../administration/updatedatabase.php", $strings["edit_database"], "in"));

if (file_exists("../installation/setup.php")) {
    $deleteSettingsAlert = <<<HTML
<div class="alert error">
    <h3>{$strings["attention"]}: {$strings["setup_erase"]}</h3>
HTML;


    if (is_writable("../setup.php")) {
        $deleteSettingsAlert .= <<<HTML
        <a href="../installation/remove_files.php">{$strings["setup_erase_file"]}</a>
HTML;
    } else {
        $deleteSettingsAlert .= <<<HTML
        <span style="color: #F00;font-weight:bold;">{$strings["setup_erase_file_ua"]}</span>
HTML;
    }
    $block1->contentRow("", $deleteSettingsAlert);
}


if ($updateChecker == "true" && $installationType == "online") {
    $admin->checkForUpdate($version);

    if ($admin->isUpdate() !== false) {
        $checkMsg = <<<HTML
        <div class="alert info">
            <h3>{$strings["update_available"]}</h3>
            <p>{$strings["version_current"]} {$version} {$strings["version_latest"]} {$admin->getNewVersion()}.<br/>
            <a href='http://www.sourceforge.net/projects/phpcollab' target='_blank'>{$strings["sourceforge_link"]}</a>.</p>
        </div>
HTML;
        $block1->contentRow("", $checkMsg);
    }
}

$block1->closeContent();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
